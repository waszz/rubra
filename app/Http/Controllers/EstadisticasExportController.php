<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoRecurso;
use App\Models\DiarioObra;
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
        $pctBen = (float)($proyecto->beneficio ?? 0);
        $pctIva = (float)($proyecto->impuestos ?? 22);

        $presupuesto = (float)($proyecto->presupuesto_total ?? 0);
        if ($presupuesto <= 0) {
            $hojas = ProyectoRecurso::where('proyecto_id', $proyecto->id)
                ->whereNotNull('parent_id')
                ->whereNotNull('recurso_id')
                ->sum(DB::raw('cantidad * precio_usd'));
            $ben = $hojas * ($pctBen / 100);
            $presupuesto = ($hojas + $ben) * (1 + $pctIva / 100);
        }

        $subtotal  = $presupuesto / ((1 + $pctBen / 100) * (1 + $pctIva / 100));
        $beneficio = $subtotal * ($pctBen / 100);

        $costosRealesDiario = DiarioObra::where('proyecto_id', $proyecto->id)
            ->groupBy('proyecto_recurso_id')
            ->pluck(DB::raw('SUM(costo_hoy)'), 'proyecto_recurso_id')
            ->map(fn($v) => (float)$v)
            ->toArray();

        $costoRealSubtotal = array_sum($costosRealesDiario);

        $ivaEjecutado = $costoRealSubtotal * ($pctIva / 100);
        $costoReal    = $costoRealSubtotal + $ivaEjecutado;

        $avanceFinanciero = $presupuesto > 0 ? ($costoReal / $presupuesto) * 100 : 0;
        $desviacion       = $costoReal - $presupuesto;

        // Rubros con deep traversal
        $rootNodes = ProyectoRecurso::where('proyecto_id', $proyecto->id)
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

        $rubrosRaw = $rootNodes->map(function ($rubro) {
            $presMap = [];
            $this->sumarSubtotalNodos($rubro->hijos ?? collect(), $presMap, 1);
            $pres = array_sum($presMap);
            $real = $this->sumarCostoRealNodos($rubro->hijos ?? collect(), $costosRealesDiario);
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

        $topPartidas = $rubrosRaw->sortByDesc('desviacion')->take(5)->values();

        // Reuse rootNodes (already deep-loaded) for all traversals
        $distribucionMap = [];
        $this->sumarDistribucionRecursiva($rootNodes, $distribucionMap, 1);
        $distribucion = collect($distribucionMap)
            ->map(fn($total, $tipo) => (object)['tipo' => $tipo, 'total' => $total])
            ->values();

        // Materiales con tree traversal correcto
        $materialesMap = [];
        $this->sumarMaterialesRecursiva($rootNodes, $materialesMap, 1);
        $materialesCollection = collect($materialesMap)->sortByDesc('costoReal')->values();
        $mayoresMateriales  = $materialesCollection->take(10);
        $todosLosMateriales = $materialesCollection;

        // Mano de obra con tree traversal correcto
        $pctCS = (float)($proyecto->carga_social ?? 0);
        $manoDeObraMap = [];
        $this->sumarManoDeObraRecursiva($rootNodes, $manoDeObraMap, 1, $pctCS);
        $manoDeObra = collect($manoDeObraMap)
            ->map(fn($r) => array_merge($r, ['totalConCS' => round($r['totalCosto'] + $r['cargaSocial'], 2)]))
            ->sortByDesc('totalCosto')
            ->values();

        $evolucion = collect([]);

        $cargaSocialTotal = $manoDeObra->sum('cargaSocial');

        return compact(
            'presupuesto', 'subtotal', 'beneficio',
            'costoReal', 'costoRealSubtotal', 'ivaEjecutado',
            'avanceFinanciero', 'desviacion',
            'rubros', 'topPartidas', 'distribucion',
            'mayoresMateriales', 'todosLosMateriales',
            'manoDeObra', 'pctCS',
            'evolucion', 'cargaSocialTotal'
        );
    }

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

    private function sumarCostoRealNodos($nodos, array $costosRealesDiario = []): float
    {
        $total = 0.0;
        foreach ($nodos as $nodo) {
            if (isset($costosRealesDiario[$nodo->id])) {
                $total += $costosRealesDiario[$nodo->id];
            } elseif ($nodo->hijos && $nodo->hijos->count() > 0) {
                $total += $this->sumarCostoRealNodos($nodo->hijos, $costosRealesDiario);
            } else {
                $total += (float)($nodo->costo_real ?? 0);
            }
        }
        return $total;
    }

    private function sumarDistribucionRecursiva($nodos, array &$map, float $multiplier): void
    {
        foreach ($nodos as $nodo) {
            $tieneHijos = $nodo->hijos && $nodo->hijos->count() > 0;
            if (is_null($nodo->recurso_id)) {
                $precioPropio = (float)($nodo->precio_usd ?? $nodo->precio_unitario ?? 0);
                $cantNodo = ($nodo->cantidad ?? 1) * $multiplier;
                if ($precioPropio > 0 && $tieneHijos) {
                    $map['sin_clasificar'] = ($map['sin_clasificar'] ?? 0) + ($precioPropio * $cantNodo);
                }
                if ($tieneHijos) {
                    $this->sumarDistribucionRecursiva($nodo->hijos, $map, $cantNodo);
                } elseif ($precioPropio > 0) {
                    $map['sin_clasificar'] = ($map['sin_clasificar'] ?? 0) + ($precioPropio * $cantNodo);
                }
            } else {
                $tipo     = $nodo->recurso->tipo ?? 'sin_clasificar';
                $subtotal = ($nodo->precio_usd ?? 0) * ($nodo->cantidad ?? 1) * $multiplier;
                $map[$tipo] = ($map[$tipo] ?? 0) + $subtotal;
            }
        }
    }

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
                $nombre   = trim($nodo->nombre ?? $nodo->recurso->nombre ?? 'Sin nombre');
                $key      = mb_strtolower($nombre);
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
