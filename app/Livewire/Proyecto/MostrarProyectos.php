<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;
use Illuminate\Support\Facades\DB;

class MostrarProyectos extends Component
{
    public $mostrarModal = false;
    public $mostrarModalEditar  = false;
public $mostrarModalEliminar = false;
public $proyectoEditando    = null;
public $deleteId            = null;
public $vista = 'grid'; // grid | list

  protected $listeners = [
    'proyectoCreado'      => 'manejarProyectoCreado',
    'proyectoActualizado' => 'cerrarModalEditar', // 👈
];

public function cambiarVista($vista)
{
    $this->vista = $vista;
}

public function abrirModalEditar($id)
{
    $this->proyectoEditando   = Proyecto::find($id);
    $this->mostrarModalEditar = true;
}

public function cerrarModalEditar()
{
    // Recargar el proyecto actualizado de la BD
    if ($this->proyectoEditando) {
        $this->proyectoEditando->refresh();
    }
    $this->mostrarModalEditar = false;
    $this->proyectoEditando   = null;
}

public function confirmarEliminar($id)
{
    $this->deleteId             = $id;
    $this->mostrarModalEliminar = true;
}

public function cerrarModalEliminar()
{
    $this->mostrarModalEliminar = false;
    $this->deleteId             = null;
}

public function eliminarProyecto()
{
    Proyecto::find($this->deleteId)?->delete();
    $this->cerrarModalEliminar();
}

    public function abrirModal()
    {
        $this->mostrarModal = true;
        $this->dispatch('modalAbierto');
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
    }

    public function manejarProyectoCreado()
    {
        $this->cerrarModal();
    }

   public function render()
{

   
  $user = auth()->user();

$proyectos = Proyecto::where(function ($q) use ($user) {
    $q->where('user_id', $user->id)           // tus proyectos propios
      ->orWhereHas('usuarios', function ($q2) use ($user) {
          $q2->where('users.id', $user->id);  // proyectos donde fuiste invitado
      });
})
->orderBy('nombre_proyecto')
->get();

    $totalProyectos     = $proyectos->count();
    $completados        = $proyectos->where('estado_obra', 'finalizado')->count();
    $totalM2            = $proyectos->sum('metros_cuadrados');
    $inversionTotal     = 0;
    $gananciasTotal     = 0;
    $beneficioRealTotal = 0.0;
    $totalesPorProyecto = [];

    
    foreach ($proyectos as $p) {
        $subtotal = \App\Models\ProyectoRecurso::where('proyecto_id', $p->id)
            ->whereNotNull('recurso_id')
            ->leftJoin('recursos', 'proyecto_recursos.recurso_id', '=', 'recursos.id')
            ->sum(DB::raw('proyecto_recursos.cantidad * COALESCE(NULLIF(proyecto_recursos.precio_usd, 0), recursos.precio_usd, 0)'));

        $pctBeneficio = ($p->beneficio ?? 0) / 100;
        $pctIva       = ($p->impuestos ?? 22) / 100;

        if ($p->presupuesto_total && $p->presupuesto_total > 0) {
            // Derivar subtotal y beneficio desde el total guardado
            $total    = (float)$p->presupuesto_total;
            $subtotalDerived = $total / ((1 + $pctBeneficio) * (1 + $pctIva));
            $beneficio = $subtotalDerived * $pctBeneficio;
        } else {
            $beneficio     = $subtotal * $pctBeneficio;
            $iva           = ($subtotal + $beneficio) * $pctIva;
            $total         = $subtotal + $beneficio + $iva;
        }

        $totalesPorProyecto[$p->id] = $total;

        if (in_array($p->estado_obra, ['activo', 'ejecucion', 'finalizado'])) {
            $inversionTotal += $total;
            $gananciasTotal += $beneficio;
        }

        // Beneficio Real: solo proyectos en ejecución (misma lógica recursiva que presupuesto-detallado)
        if (in_array($p->estado_obra, ['ejecucion', 'pausado', 'finalizado'])) {
            $nodos = \App\Models\ProyectoRecurso::where('proyecto_id', $p->id)->get()->keyBy('id');
            $childMap = [];
            foreach ($nodos as $n) {
                if ($n->parent_id !== null) {
                    $childMap[$n->parent_id][] = $n->id;
                }
            }

            $computePerUnit = function(int $nodeId) use (&$computePerUnit, $nodos, $childMap): float {
                $node = $nodos[$nodeId];
                if (!is_null($node->recurso_id)) return (float)($node->precio_usd ?? 0);
                $kids = $childMap[$nodeId] ?? [];
                if (empty($kids) && ($node->precio_usd ?? 0) > 0) return (float)$node->precio_usd;
                $t = 0.0;
                foreach ($kids as $kidId) {
                    $t += $computePerUnit($kidId) * (float)($nodos[$kidId]->cantidad ?? 1);
                }
                return $t;
            };

            $sumReal = function(array $ids) use (&$sumReal, $nodos, $childMap): float {
                $s = 0.0;
                foreach ($ids as $id) {
                    $n = $nodos[$id];
                    if (!is_null($n->recurso_id)) {
                        if ($n->costo_real !== null) $s += (float)$n->costo_real * (float)($n->cantidad ?? 1);
                    } else {
                        $s += $sumReal($childMap[$id] ?? []) * (float)($n->cantidad ?? 1);
                    }
                }
                return $s;
            };

            $roots = $nodos->filter(fn($n) => is_null($n->parent_id));
            $subtotalB = 0.0;
            $totalRealP = 0.0;
            foreach ($roots as $root) {
                $rootKids = $childMap[$root->id] ?? [];
                foreach ($rootKids as $kidId) {
                    $subtotalB += $computePerUnit($kidId) * (float)($nodos[$kidId]->cantidad ?? 1);
                }
                $totalRealP += $sumReal($rootKids);
            }

            $subtotalConBeneficio = $subtotalB * (1 + $pctBeneficio);
            $beneficioRealTotal += ($subtotalConBeneficio - $totalRealP);
        }
    }

    $estadosData = [
        'en_revision' => $proyectos->where('estado_obra', 'en_revision')->count(),
        'activo'      => $proyectos->where('estado_obra', 'activo')->count(),
        'ejecucion'   => $proyectos->where('estado_obra', 'ejecucion')->count(),
        'pausado'     => $proyectos->where('estado_obra', 'pausado')->count(),
        'finalizado'  => $proyectos->where('estado_obra', 'finalizado')->count(),
    ];

    // Verificar si se alcanzó el límite de proyectos
    $proyectosDelUsuario = Proyecto::where('user_id', $user->id)->count();
    $limiteProyectos = $user->proyectosLimite();
    $limiteAlcanzado = $proyectosDelUsuario >= $limiteProyectos;

    return view('livewire.proyecto.mostrar-proyectos', [
        'proyectos'          => $proyectos,
        'totalProyectos'     => $totalProyectos,
        'completados'        => $completados,
        'totalM2'            => $totalM2,
        'inversionTotal'     => $inversionTotal,
        'gananciasTotal'     => $gananciasTotal,
        'beneficioRealTotal' => $beneficioRealTotal,
        'estadosData'        => $estadosData,
        'totalesPorProyecto' => $totalesPorProyecto,
        'limiteAlcanzado'    => $limiteAlcanzado,
        'limiteProyectos'    => $limiteProyectos,
    ])->layout('layouts.app');
}

    /**
     * Duplica un proyecto y todos sus recursos (estructura jerárquica)
     */
    public function duplicarProyecto($id)
    {
        $user = auth()->user();
        $proyectosActuales = Proyecto::where('user_id', $user->id)->count();
        $limite = $user->proyectosLimite();

        // Validar que no exceda el límite
        if ($proyectosActuales >= $limite) {
            session()->flash('error', "Alcanzaste el límite de {$limite} proyectos en tu plan. Mejorá tu plan para crear más.");
            return;
        }

        $original = Proyecto::findOrFail($id);
        $nuevoProyecto = $original->replicate();
        $nuevoProyecto->nombre_proyecto = $original->nombre_proyecto . ' (Copia)';
        $nuevoProyecto->estado_obra = 'en_revision';
        $nuevoProyecto->estado_autorizacion = 'pendiente';
        $nuevoProyecto->push();

        foreach ($original->usuarios as $usuario) {
            $nuevoProyecto->usuarios()->attach($usuario->id);
        }

        $recursosOriginales = $original->proyectoRecursos()->get();
        $idMap = [];
        foreach ($recursosOriginales->where('parent_id', null) as $recurso) {
            $nuevo = $recurso->replicate();
            $nuevo->proyecto_id = $nuevoProyecto->id;
            $nuevo->parent_id = null;
            $nuevo->save();
            $idMap[$recurso->id] = $nuevo->id;
        }
        foreach ($recursosOriginales->where('parent_id', '!=', null) as $recurso) {
            $nuevo = $recurso->replicate();
            $nuevo->proyecto_id = $nuevoProyecto->id;
            $nuevo->parent_id = $idMap[$recurso->parent_id] ?? null;
            $nuevo->save();
            $idMap[$recurso->id] = $nuevo->id;
        }

        session()->flash('mensaje', 'Proyecto duplicado correctamente.');
        $this->render();
    }
}