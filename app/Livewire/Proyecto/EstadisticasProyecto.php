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
        $subtotal  = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNotNull('parent_id')
            ->sum(DB::raw('cantidad * precio_usd'));

        $beneficio   = $subtotal * (($proyecto->beneficio ?? 0) / 100);
        $iva         = ($subtotal + $beneficio) * (($proyecto->impuestos ?? 22) / 100);
        $presupuesto = $subtotal + $beneficio + $iva;

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

        // ── TOP 5 RUBROS PRINCIPALES CON MAYOR DESVIACIÓN ──────────────────────────
        $topPartidas = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNull('parent_id')
            ->with('hijos')
            ->get()
            ->map(function ($rubro) {
                $presupuesto = $rubro->hijos->sum(fn($h) => ($h->cantidad ?? 0) * ($h->precio_usd ?? 0));
                $costoReal   = $rubro->hijos->sum(fn($h) => $h->costo_real ?? 0);
                return [
                    'nombre'      => $rubro->nombre ?? 'Sin nombre',
                    'presupuesto' => round($presupuesto, 2),
                    'costo_real'  => round($costoReal, 2),
                    'desviacion'  => round($costoReal - $presupuesto, 2),
                ];
            })
            ->sortByDesc('desviacion')
            ->take(5)
            ->values();

        // ── DISTRIBUCIÓN ─────────────────────────────
        $distribucion = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNotNull('parent_id')
            ->join('recursos', 'proyecto_recursos.recurso_id', '=', 'recursos.id')
            ->select(
                'recursos.tipo',
                DB::raw('SUM(proyecto_recursos.cantidad * proyecto_recursos.precio_usd) as total')
            )
            ->groupBy('recursos.tipo')
            ->get();

        // ── MAYORES MATERIALES CONSUMIDOS ─────────────────────────────
        $mayoresMateriales = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNotNull('parent_id')
            ->with('recurso')
            ->get()
            ->filter(fn($pr) => $pr->recurso && $pr->recurso->tipo === 'material')
            ->map(fn($pr) => [
                'nombre'      => $pr->nombre ?? $pr->recurso->nombre ?? 'Sin nombre',
                'cantidad'    => $pr->cantidad ?? 0,
                'unidad'      => $pr->unidad ?? $pr->recurso->unidad ?? '',
                'precioUnitario' => $pr->precio_usd ?? 0,
                'costoReal'   => $pr->costo_real ?? 0,
            ])
            ->sortByDesc('costoReal')
            ->take(10)
            ->values();

        // ── EVOLUCIÓN (vacía si no hay datos de fecha) ────────────────────────────
        $evolucion = collect([]);

        return compact(
            'presupuesto',
            'costoReal',
            'costoRealSubtotal',
            'ivaEjecutado',
            'avanceFinanciero',
            'desviacion',
            'topPartidas',
            'distribucion',
            'mayoresMateriales',
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
}