<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoRecurso;
use App\Exports\EstadisticasProyectoExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EstadisticasExportController extends Controller
{
    public function excel($proyectoId)
    {
        $proyecto = Proyecto::findOrFail($proyectoId);
        $stats = $this->calcularStats($proyecto);

        $nombreArchivo = Str::slug($proyecto->nombre_proyecto) . '_estadisticas_' . now()->format('d-m-Y') . '.xlsx';

        return Excel::download(new EstadisticasProyectoExport($proyecto, $stats), $nombreArchivo);
    }

    public function pdf($proyectoId)
    {
        $proyecto = Proyecto::findOrFail($proyectoId);
        $stats = $this->calcularStats($proyecto);

        $pdf = Pdf::loadView('exports.estadisticas-pdf', [
            'proyecto' => $proyecto,
            'stats'    => $stats,
        ]);

        $nombreArchivo = Str::slug($proyecto->nombre_proyecto) . '_estadisticas_' . now()->format('d-m-Y') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    private function calcularStats($proyecto)
    {
        $subtotal  = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNotNull('parent_id')
            ->sum(DB::raw('cantidad * precio_usd'));

        $beneficio   = $subtotal * (($proyecto->beneficio ?? 0) / 100);
        $iva         = ($subtotal + $beneficio) * (($proyecto->impuestos ?? 22) / 100);
        $presupuesto = $subtotal + $beneficio + $iva;

        $costoRealSubtotal = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNotNull('parent_id')
            ->sum('costo_real');

        $pctImpuestos = (float) ($proyecto->impuestos ?? 22);
        $ivaEjecutado = $costoRealSubtotal * ($pctImpuestos / 100);
        $costoReal    = $costoRealSubtotal + $ivaEjecutado;

        $avanceFinanciero = $presupuesto > 0 ? ($costoReal / $presupuesto) * 100 : 0;
        $desviacion       = $costoReal - $presupuesto;

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

        $distribucion = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNotNull('parent_id')
            ->join('recursos', 'proyecto_recursos.recurso_id', '=', 'recursos.id')
            ->select('recursos.tipo', DB::raw('SUM(proyecto_recursos.cantidad * proyecto_recursos.precio_usd) as total'))
            ->groupBy('recursos.tipo')
            ->get();

        $mayoresMateriales = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNotNull('parent_id')
            ->with('recurso')
            ->get()
            ->filter(fn($pr) => $pr->recurso && $pr->recurso->tipo === 'material')
            ->map(fn($pr) => [
                'nombre'         => $pr->nombre ?? $pr->recurso->nombre ?? 'Sin nombre',
                'cantidad'       => $pr->cantidad ?? 0,
                'unidad'         => $pr->unidad ?? $pr->recurso->unidad ?? '',
                'precioUnitario' => $pr->precio_usd ?? 0,
                'costoReal'      => $pr->costo_real ?? 0,
            ])
            ->sortByDesc('costoReal')
            ->take(10)
            ->values();

        $evolucion = collect([]);

        return compact(
            'presupuesto', 'costoReal', 'costoRealSubtotal', 'ivaEjecutado',
            'avanceFinanciero', 'desviacion', 'topPartidas', 'distribucion',
            'mayoresMateriales', 'evolucion', 'subtotal', 'beneficio'
        );
    }
}
