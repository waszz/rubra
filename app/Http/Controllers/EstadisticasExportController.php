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

    public function materialesExcel($proyectoId)
    {
        $proyecto   = Proyecto::findOrFail($proyectoId);
        $materiales = $this->calcularMateriales($proyecto);

        $nombreArchivo = Str::slug($proyecto->nombre_proyecto) . '_materiales_' . now()->format('d-m-Y') . '.xlsx';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Materiales');

        // Encabezado proyecto
        $sheet->setCellValue('A1', $proyecto->nombre_proyecto . ' — Listado de Materiales');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->setCellValue('A2', 'Generado: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A2:F2');

        // Cabecera tabla
        $headers = ['#', 'Material', 'Cantidad', 'Unidad', 'P. Unitario (USD)', 'Total (USD)'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}4", $h);
            $sheet->getStyle("{$col}4")->getFont()->setBold(true);
            $sheet->getStyle("{$col}4")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1a1a1a');
            $sheet->getStyle("{$col}4")->getFont()->getColor()->setRGB('FFFFFF');
        }

        $row = 5;
        $total = 0;
        foreach ($materiales as $i => $mat) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $mat['nombre']);
            $sheet->setCellValue("C{$row}", round($mat['cantidad'], 4));
            $sheet->setCellValue("D{$row}", $mat['unidad']);
            $sheet->setCellValue("E{$row}", round($mat['precioUnitario'], 4));
            $sheet->setCellValue("F{$row}", round($mat['costoReal'], 2));
            $total += $mat['costoReal'];
            $row++;
        }

        // Fila total
        $sheet->setCellValue("E{$row}", 'TOTAL');
        $sheet->setCellValue("F{$row}", round($total, 2));
        $sheet->getStyle("E{$row}:F{$row}")->getFont()->setBold(true);

        // Anchos
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $ruta   = storage_path('app/temp/' . $nombreArchivo);
        if (!is_dir(dirname($ruta))) mkdir(dirname($ruta), 0755, true);
        $writer->save($ruta);

        return response()->download($ruta, $nombreArchivo)->deleteFileAfterSend(true);
    }

    public function materialesPdf($proyectoId)
    {
        $proyecto   = Proyecto::findOrFail($proyectoId);
        $materiales = $this->calcularMateriales($proyecto);

        $pdf = Pdf::loadView('exports.materiales-pdf', [
            'proyecto'   => $proyecto,
            'materiales' => $materiales,
            'total'      => $materiales->sum('costoReal'),
        ])->setPaper('a4', 'portrait');

        $nombreArchivo = Str::slug($proyecto->nombre_proyecto) . '_materiales_' . now()->format('d-m-Y') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    private function calcularMateriales($proyecto): \Illuminate\Support\Collection
    {
        $map = [];
        $this->sumarMaterialesRecursiva(
            ProyectoRecurso::where('proyecto_id', $proyecto->id)
                ->whereNull('parent_id')
                ->with(['hijos.recurso', 'hijos.hijos.recurso', 'hijos.hijos.hijos.recurso', 'recurso'])
                ->get(),
            $map,
            1
        );
        return collect($map)->sortByDesc('costoReal')->values();
    }

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
                $nombre   = trim($nodo->nombre ?? $nodo->recurso->nombre ?? 'Sin nombre');
                $key      = mb_strtolower($nombre);
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

    private function calcularStats($proyecto)
    {
        $pctBen = (float)($proyecto->beneficio  ?? 0);
        $pctIva = (float)($proyecto->impuestos  ?? 22);

        // Usar presupuesto_total guardado (calculado con traversal correcto del árbol).
        $presupuesto = (float)($proyecto->presupuesto_total ?? 0);

        if ($presupuesto <= 0) {
            // Fallback: sumar solo hojas reales (evita doble conteo con subrubros)
            $hojas = ProyectoRecurso::where('proyecto_id', $proyecto->id)
                ->whereNotNull('parent_id')
                ->whereNotNull('recurso_id')
                ->sum(DB::raw('cantidad * precio_usd'));
            $ben = $hojas * ($pctBen / 100);
            $presupuesto = ($hojas + $ben) * (1 + $pctIva / 100);
        }

        $subtotal  = $presupuesto / ((1 + $pctBen / 100) * (1 + $pctIva / 100));
        $beneficio = $subtotal * ($pctBen / 100);

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
