<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;
use App\Models\ProyectoRecurso;
use Illuminate\Support\Facades\DB;

class EstadisticasProyecto extends Component
{
    public $proyectoId = null;
    public bool $modoProyecto = false;
    public bool $subtotalConBeneficio = false;

public function mount($proyectoId = null): void
{
    $this->modoProyecto = $proyectoId !== null;

    $this->proyectoId = $proyectoId ?? optional(
        Proyecto::latest()->first()
    )->id;
}

    public function seleccionarProyecto($id): void
    {
        $this->proyectoId = $id;
    }

    public function toggleSubtotal(): void
    {
        $this->subtotalConBeneficio = !$this->subtotalConBeneficio;
    }

    public function render()
{
    $user = auth()->user();

    // 🔥 Obtener proyectos del usuario o proyectos donde fue invitado
    if ($user->invited_by) {
        // Si el usuario fue invitado, ve los proyectos del que lo invitó
        $proyectos = Proyecto::where('user_id', $user->invited_by)
            ->orderBy('nombre_proyecto')
            ->get();
    } else {
        // Si no fue invitado, ve sus propios proyectos
        $proyectos = Proyecto::where('user_id', $user->id)
            ->orderBy('nombre_proyecto')
            ->get();
    }

    $proyecto = $proyectos->firstWhere('id', $this->proyectoId);
    $stats    = $proyecto ? $this->calcularStats($proyecto) : null;

    return view('livewire.proyecto.estadisticas-proyecto', [
        'proyectos' => $proyectos,
        'proyecto'  => $proyecto,
        'stats'     => $stats,
    ])->layout('layouts.app');
}

    public function updatedProyectoId()
    {
        $this->dispatch('estadisticas-ready');
    }

    private function calcularStats($proyecto)
    {
        if (!$proyecto) return null;

        // ── PRESUPUESTO ───────────────────────────────
        // Usamos presupuesto_total guardado (calculado correctamente via árbol).
        // Back-calculamos subtotal base y beneficio a partir de los porcentajes.
        $pctBen = (float)($proyecto->beneficio  ?? 0);
        $pctIva = (float)($proyecto->impuestos  ?? 22);

        $presupuesto = (float)($proyecto->presupuesto_total ?? 0);

        // Si por algún motivo aún no fue calculado, usar la suma directa de hojas
        if ($presupuesto <= 0) {
            $presupuesto = ProyectoRecurso::where('proyecto_id', $proyecto->id)
                ->whereNotNull('parent_id')
                ->whereNotNull('recurso_id')   // solo hojas (items reales, no subrubros)
                ->sum(DB::raw('cantidad * precio_usd'));
            $beneficio   = $presupuesto * ($pctBen / 100);
            $presupuesto = ($presupuesto + $beneficio) * (1 + $pctIva / 100);
        }

        // Subtotal base sin beneficio ni IVA (para mostrar desglose)
        $subtotal  = $presupuesto / ((1 + $pctBen / 100) * (1 + $pctIva / 100));
        $beneficio = $subtotal * ($pctBen / 100);
        $iva       = ($subtotal + $beneficio) * ($pctIva / 100);

        // ── COSTOS REALES (de ejecución) ─────────────────────────────
        $costoRealSubtotal = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNotNull('parent_id')
            ->sum('costo_real');
        
        // Calcular el precio final de ejecución (con IVA)
        $pctImpuestos = (float) ($proyecto->impuestos ?? 22);
        $ivaEjecutado = $costoRealSubtotal * ($pctImpuestos / 100);
        $costoReal = $costoRealSubtotal + $ivaEjecutado;

        $avanceFinanciero = $presupuesto > 0
            ? ($costoReal / $presupuesto) * 100
            : 0;

        $desviacion = $costoReal - $presupuesto;

        // ── TODOS LOS RUBROS (listado con %) ─────────────────────────────────────────
        $rubrosRoot = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNull('parent_id')
            ->with([
                'hijos',
                'hijos.recurso',
                'hijos.hijos',
                'hijos.hijos.recurso',
                'hijos.hijos.hijos',
                'hijos.hijos.hijos.recurso',
                'hijos.hijos.hijos.hijos',
                'hijos.hijos.hijos.hijos.recurso',
            ])
            ->get();

        $rubrosRaw = $rubrosRoot->map(function ($rubro) {
            $presMap = [];
            $this->sumarSubtotalNodos($rubro->hijos ?? collect(), $presMap, 1);
            $pres = array_sum($presMap);
            $real = $this->sumarCostoRealNodos($rubro->hijos ?? collect());
            return [
                'nombre'      => $rubro->nombre ?? 'Sin nombre',
                'presupuesto' => round($pres, 2),
                'costo_real'  => round($real, 2),
                'desviacion'  => round($real - $pres, 2),
            ];
        })->sortByDesc('presupuesto')->values();

        $totalRubros = $rubrosRaw->sum('presupuesto');
        $rubros = $rubrosRaw->map(fn($r) => array_merge($r, [
            'pct' => $totalRubros > 0 ? round(($r['presupuesto'] / $totalRubros) * 100, 1) : 0,
        ]))->values();

        // ── TOP 5 SUBRUBROS CON MAYOR DESVIACIÓN ──────────────────────────
        $subrubrosRaw = collect();
        foreach ($rubrosRoot as $rubro) {
            foreach ($rubro->hijos as $hijo) {
                $presMap = [];
                $this->sumarSubtotalNodos(collect([$hijo]), $presMap, 1);
                $pres = array_sum($presMap);
                $real = $this->sumarCostoRealNodos(collect([$hijo]));
                $subrubrosRaw->push([
                    'nombre'      => $hijo->nombre ?? 'Sin nombre',
                    'presupuesto' => round($pres, 2),
                    'costo_real'  => round($real, 2),
                    'desviacion'  => round($real - $pres, 2),
                ]);
            }
        }
        $topPartidas = $subrubrosRaw->sortByDesc('desviacion')->take(5)->values();

        // ── DISTRIBUCIÓN ─────────────────────────────
        // Recorremos el árbol correctamente para sumar por tipo,
        // multiplicando las cantidades de los ancestros (igual que obtenerDatosPresupuesto).
        $distribucionMap = [];
        $this->sumarDistribucionRecursiva($rubrosRoot, $distribucionMap, 1);
        $distribucion = collect($distribucionMap)->map(fn($total, $tipo) => (object)['tipo' => $tipo, 'total' => $total])->values();

        // ── MAYORES MATERIALES CONSUMIDOS ─────────────────────────────
        $materialesMap = [];
        $this->sumarMaterialesRecursiva($rubrosRoot, $materialesMap, 1);
        $materialesCollection = collect($materialesMap)->sortByDesc('costoReal')->values();
        $mayoresMateriales    = $materialesCollection->take(10);
        $todosLosMateriales   = $materialesCollection; // sin límite

        // ── MANO DE OBRA POR CARGO/ESPECIALIDAD ─────────────────────────────
        $pctCS = (float)($proyecto->carga_social ?? 0);
        $manoDeObraMap = [];
        $this->sumarManoDeObraRecursiva($rubrosRoot, $manoDeObraMap, 1, $pctCS);
        $manoDeObra = collect($manoDeObraMap)
            ->map(fn($r) => array_merge($r, ['totalConCS' => round($r['totalCosto'] + $r['cargaSocial'], 2)]))
            ->sortByDesc('totalCosto')
            ->values();

        // ── EVOLUCIÓN (vacía si no hay datos de fecha) ────────────────────────────
        $evolucion = collect([]);

        return compact(
            'presupuesto',
            'manoDeObra',
            'pctCS',
            'costoReal',
            'costoRealSubtotal',
            'ivaEjecutado',
            'avanceFinanciero',
            'desviacion',
            'topPartidas',
            'rubros',
            'distribucion',
            'mayoresMateriales',
            'todosLosMateriales',
            'evolucion',
            'subtotal',
            'beneficio'
        );
    }

    private function obtenerIdsDescendientes(int $parentId): array
    {
        $hijos = ProyectoRecurso::where('parent_id', $parentId)->pluck('id')->toArray();
        $todos = $hijos;
        foreach ($hijos as $hijoId) {
            $todos = array_merge($todos, $this->obtenerIdsDescendientes($hijoId));
        }
        return $todos;
    }

    /**
     * Suma el subtotal presupuestado (cantidad * precio_usd) de un árbol con multiplicadores.
     * Acumula en $map como un array plano; el total es array_sum($map).
     */
    private function sumarSubtotalNodos($nodos, array &$map, float $multiplier): void
    {
        foreach ($nodos as $nodo) {
            $tieneHijos = $nodo->hijos && $nodo->hijos->count() > 0;
            $cantNodo   = ($nodo->cantidad ?? 1) * $multiplier;

            if (is_null($nodo->recurso_id)) {
                $precioPropio = (float)($nodo->precio_usd ?? 0);
                if ($precioPropio > 0 && !$tieneHijos) {
                    $map[] = $precioPropio * $cantNodo;
                }
                if ($tieneHijos) {
                    $this->sumarSubtotalNodos($nodo->hijos, $map, $cantNodo);
                }
            } else {
                $map[] = ($nodo->precio_usd ?? 0) * ($nodo->cantidad ?? 0) * $multiplier;
            }
        }
    }

    /**
     * Suma el costo_real de todas las hojas de un árbol (sin multiplicadores, costo_real ya es el final).
     */
    private function sumarCostoRealNodos($nodos): float
    {
        $total = 0.0;
        foreach ($nodos as $nodo) {
            if ($nodo->hijos && $nodo->hijos->count() > 0) {
                $total += $this->sumarCostoRealNodos($nodo->hijos);
            } else {
                $total += (float)($nodo->costo_real ?? 0);
            }
        }
        return $total;
    }

    /**
     * Recorre el árbol acumulando el costo por tipo de recurso con multiplicadores correctos.
     * Los subrubros con precio_usd propio se acumulan como 'sin_clasificar'.
     */
    private function sumarDistribucionRecursiva($nodos, array &$map, float $multiplier): void
    {
        foreach ($nodos as $nodo) {
            $tieneHijos = $nodo->hijos && $nodo->hijos->count() > 0;

            if (is_null($nodo->recurso_id)) {
                // Subrubro/categoría: puede tener precio propio + hijos
                $precioPropio = (float)($nodo->precio_usd ?? $nodo->precio_unitario ?? 0);
                $cantNodo = ($nodo->cantidad ?? 1) * $multiplier;

                if ($precioPropio > 0 && $tieneHijos) {
                    // Tiene precio propio pero también hijos: solo contar el precio propio
                    $map['sin_clasificar'] = ($map['sin_clasificar'] ?? 0) + ($precioPropio * $cantNodo);
                }

                if ($tieneHijos) {
                    $this->sumarDistribucionRecursiva($nodo->hijos, $map, $cantNodo);
                } elseif ($precioPropio > 0) {
                    // Subrubro hoja con precio (sin hijos y sin recurso)
                    $map['sin_clasificar'] = ($map['sin_clasificar'] ?? 0) + ($precioPropio * $cantNodo);
                }
            } else {
                // Hoja con recurso vinculado
                $tipo     = $nodo->recurso->tipo ?? 'sin_clasificar';
                $subtotal = ($nodo->precio_usd ?? 0) * ($nodo->cantidad ?? 1) * $multiplier;
                $map[$tipo] = ($map[$tipo] ?? 0) + $subtotal;
            }
        }
    }

    /**
     * Recorre el árbol acumulando materiales con cantidad efectiva (multiplicada por ancestros).
     */
    private function sumarMaterialesRecursiva($nodos, array &$map, float $multiplier): void
    {
        foreach ($nodos as $nodo) {
            $tieneHijos = $nodo->hijos && $nodo->hijos->count() > 0;

            if (is_null($nodo->recurso_id)) {
                $cantNodo = ($nodo->cantidad ?? 1) * $multiplier;
                if ($tieneHijos) {
                    $this->sumarMaterialesRecursiva($nodo->hijos, $map, $cantNodo);
                }
            } elseif ($nodo->recurso && $nodo->recurso->tipo === 'material') {
                $nombreRaw = $nodo->nombre ?? $nodo->recurso->nombre ?? 'Sin nombre';
                $nombre    = trim($nombreRaw);
                $key       = mb_strtolower($nombre);
                $cantEfec = ($nodo->cantidad ?? 0) * $multiplier;
                $subtotal = ($nodo->precio_usd ?? 0) * $cantEfec;
                if (!isset($map[$key])) {
                    $map[$key] = [
                        'nombre'         => $nombre,
                        'cantidad'       => 0,
                        'unidad'         => $nodo->unidad ?? $nodo->recurso->unidad ?? '',
                        'precioUnitario' => $nodo->precio_usd ?? 0,
                        'costoReal'      => 0,
                    ];
                }
                $map[$key]['cantidad']  += $cantEfec;
                $map[$key]['costoReal'] += $subtotal;
            }
        }
    }

    /**
     * Recorre el árbol acumulando mano de obra con cantidad efectiva y carga social.
     */
    private function sumarManoDeObraRecursiva($nodos, array &$map, float $multiplier, float $pctCS): void
    {
        foreach ($nodos as $nodo) {
            $tieneHijos = $nodo->hijos && $nodo->hijos->count() > 0;

            if (is_null($nodo->recurso_id)) {
                $cantNodo = ($nodo->cantidad ?? 1) * $multiplier;
                if ($tieneHijos) {
                    $this->sumarManoDeObraRecursiva($nodo->hijos, $map, $cantNodo, $pctCS);
                }
            } elseif ($nodo->recurso && in_array($nodo->recurso->tipo, ['labor', 'mano_obra'])) {
                $nombreRaw = $nodo->nombre ?? $nodo->recurso->nombre ?? 'Sin nombre';
                $nombre    = trim($nombreRaw);
                $key       = mb_strtolower($nombre); // clave normalizada para agrupar
                $cantEfec = ($nodo->cantidad ?? 0) * $multiplier;
                $precio   = $nodo->precio_usd ?? 0;
                $subtotal = $precio * $cantEfec;
                $pct      = $pctCS > 0 ? $pctCS : (float)($nodo->recurso->social_charges_percentage ?? 0);
                $cs       = $precio * ($pct / 100) * $cantEfec;
                if (!isset($map[$key])) {
                    $map[$key] = [
                        'nombre'      => $nombre,
                        'unidad'      => $nodo->unidad ?? $nodo->recurso->unidad ?? 'h',
                        'totalCosto'  => 0,
                        'cargaSocial' => 0,
                    ];
                }
                $map[$key]['totalCosto']  += $subtotal;
                $map[$key]['cargaSocial'] += $cs;
            }
        }
    }
}