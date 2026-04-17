<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoRecurso;
use App\Models\ComposicionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

class GanttExportController extends Controller
{
    public function pdf($proyectoId)
    {
        $proyecto = Proyecto::findOrFail($proyectoId);
        $rubros   = $this->buildRubros($proyecto);

        $pdf = Pdf::loadView('exports.gantt-pdf', [
            'proyecto' => $proyecto,
            'rubros'   => $rubros,
        ])->setPaper('a4', 'landscape');

        $nombreArchivo = Str::slug($proyecto->nombre_proyecto) . '_gantt_' . now()->format('d-m-Y') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    public function excel($proyectoId)
    {
        $proyecto = Proyecto::findOrFail($proyectoId);
        $rubros   = $this->buildRubros($proyecto);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Gantt');

        // ── Encabezado del proyecto ──────────────────────────────────
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'CRONOGRAMA GANTT — ' . strtoupper($proyecto->nombre_proyecto));
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        $sheet->mergeCells('A2:J2');
        $fechaInicio = $proyecto->fecha_inicio ? Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') : '—';
        $sheet->setCellValue('A2', 'Proyecto: ' . $proyecto->nombre_proyecto . '   |   Inicio: ' . $fechaInicio . '   |   Generado: ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['rgb' => '6B7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // ── Cabeceras de columnas ──────────────────────────────────
        $headers = ['#', 'Tarea / Sub-tarea', 'Inicio', 'Fin', 'Duración (días)', 'Hs. MO', 'Trabajadores', 'Depende de', '% Avance', 'Notas'];
        $row = 4;
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . $row;
            $sheet->setCellValue($cell, $header);
        }
        $sheet->getStyle('A4:J4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(18);

        // ── Filas de datos ──────────────────────────────────
        $dataRow    = 5;
        $numCategoria = 0;
        $numItem      = 0;

        foreach ($rubros as $fila) {
            if ($fila['es_categoria']) {
                $numCategoria++;
                $numItem = 0;
                $label = $numCategoria . '.';
                $bgColor = 'E5E7EB';
                $fontColor = '111827';
                $bold = true;
                $fontSize = 9;
            } else {
                $numItem++;
                $label = $numCategoria . '.' . $numItem;
                $bgColor = ($dataRow % 2 === 0) ? 'FAFBFC' : 'FFFFFF';
                $fontColor = '374151';
                $bold = false;
                $fontSize = 9;
            }

            $inicio   = $fila['fecha_inicio'] ?? null;
            $fin      = $fila['fecha_fin'] ?? null;
            $duracion = ($inicio && $fin)
                ? Carbon::parse($inicio)->diffInDays(Carbon::parse($fin)) + 1
                : null;

            $sheet->setCellValue('A' . $dataRow, $label);
            $sheet->setCellValue('B' . $dataRow, ($fila['es_categoria'] ? '' : '    ') . ($fila['nombre'] ?? ''));
            $sheet->setCellValue('C' . $dataRow, $inicio ? Carbon::parse($inicio)->format('d/m/Y') : '—');
            $sheet->setCellValue('D' . $dataRow, $fin    ? Carbon::parse($fin)->format('d/m/Y')    : '—');
            $sheet->setCellValue('E' . $dataRow, $duracion ?? '—');
            $sheet->setCellValue('F' . $dataRow, $fila['horas_totales'] > 0 ? $fila['horas_totales'] : '—');
            $sheet->setCellValue('G' . $dataRow, !$fila['es_categoria'] ? ($fila['trabajadores'] ?? 1) : '—');
            $sheet->setCellValue('H' . $dataRow, $fila['depends_on_nombre'] ?? '—');
            $sheet->setCellValue('I' . $dataRow, '');
            $sheet->setCellValue('J' . $dataRow, '');

            $sheet->getStyle('A' . $dataRow . ':J' . $dataRow)->applyFromArray([
                'font'      => ['bold' => $bold, 'size' => $fontSize, 'color' => ['rgb' => $fontColor]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle('A' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $dataRow . ':G' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($dataRow)->setRowHeight(16);

            $dataRow++;
        }

        // ── Anchos de columna ──────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(13);
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(28);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(20);

        // ── Freeze panes ──────────────────────────────────
        $sheet->freezePane('A5');

        $nombreArchivo = Str::slug($proyecto->nombre_proyecto) . '_gantt_' . now()->format('d-m-Y') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $tmpPath = tempnam(sys_get_temp_dir(), 'gantt_') . '.xlsx';
        $writer->save($tmpPath);

        return response()->download($tmpPath, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ── Lógica de construcción de rubros (replicada de GanttProyecto) ──

    private function buildRubros(Proyecto $proyecto): array
    {
        $rubrosDB = ProyectoRecurso::where('proyecto_id', $proyecto->id)
            ->whereNull('parent_id')
            ->with([
                'hijos',
                'hijos.dependeDe',
                'hijos.dependientes',
                'hijos.hijos',
                'hijos.hijos.recurso',
            ])
            ->get();

        $filas              = [];
        $fechaFinCalculada  = [];

        foreach ($rubrosDB as $rubro) {
            $filas[] = [
                'id'                => $rubro->id,
                'nombre'            => $rubro->nombre,
                'nivel'             => 0,
                'fecha_inicio'      => $rubro->fecha_inicio?->format('Y-m-d'),
                'fecha_fin'         => $rubro->fecha_fin?->format('Y-m-d'),
                'es_categoria'      => true,
                'depends_on_id'     => null,
                'depends_on_nombre' => null,
                'dependientes'      => [],
                'horas_totales'     => 0,
                'trabajadores'      => 1,
            ];

            foreach ($rubro->hijos as $hijo) {
                $horasTotales    = $this->calcularHorasSubrubro($hijo);
                $fechaInicioHijo = $hijo->fecha_inicio?->format('Y-m-d');

                if ($hijo->depends_on_id && isset($fechaFinCalculada[$hijo->depends_on_id])) {
                    $minInicio = Carbon::parse($fechaFinCalculada[$hijo->depends_on_id])->addDay()->format('Y-m-d');
                    if (!$fechaInicioHijo || $fechaInicioHijo < $minInicio) {
                        $fechaInicioHijo = $minInicio;
                    }
                }

                $fechaFinGuardada = $hijo->fecha_fin?->format('Y-m-d');
                if ($horasTotales > 0 && $fechaInicioHijo) {
                    $trabajadores = max(1, (int)($hijo->trabajadores ?? 1));
                    $fechaFinHijo = $this->calcularFechaFinPorHoras($fechaInicioHijo, $horasTotales, $trabajadores);
                } elseif ($fechaFinGuardada) {
                    $fechaFinHijo = $fechaFinGuardada;
                } else {
                    $fechaFinHijo = null;
                }

                if ($fechaFinHijo) {
                    $fechaFinCalculada[$hijo->id] = $fechaFinHijo;
                }

                $filas[] = [
                    'id'                => $hijo->id,
                    'nombre'            => $hijo->nombre,
                    'nivel'             => 1,
                    'fecha_inicio'      => $fechaInicioHijo,
                    'fecha_fin'         => $fechaFinHijo,
                    'es_categoria'      => false,
                    'depends_on_id'     => $hijo->depends_on_id,
                    'depends_on_nombre' => $hijo->dependeDe?->nombre,
                    'dependientes'      => $hijo->dependientes->pluck('nombre')->toArray(),
                    'horas_totales'     => $horasTotales,
                    'trabajadores'      => max(1, (int)($hijo->trabajadores ?? 1)),
                ];
            }
        }

        return $filas;
    }

    private function calcularHorasSubrubro($subrubro, float $factorAcumulado = 1.0): float
    {
        $cant  = (float)($subrubro->cantidad ?? 1) * $factorAcumulado;
        $horas = 0.0;

        $hijos = $subrubro->relationLoaded('hijos')
            ? $subrubro->hijos
            : $subrubro->hijos()->with('recurso')->get();

        foreach ($hijos as $child) {
            $rec = $child->recurso;

            if (!$rec) {
                $horas += $this->calcularHorasSubrubro($child, $cant);
                continue;
            }

            if (in_array($rec->tipo, ['labor', 'mano_obra'])) {
                $horas += (float)($child->cantidad ?? 0) * $cant;
            } elseif ($rec->tipo === 'composition') {
                $items = ComposicionItem::where('composicion_id', $rec->id)
                    ->with('recursoBase')
                    ->get();
                foreach ($items as $item) {
                    $base = $item->recursoBase;
                    if ($base && in_array($base->tipo, ['labor', 'mano_obra'])) {
                        $horas += (float)($item->cantidad ?? 0) * (float)($child->cantidad ?? 1) * $cant;
                    }
                }
            }
        }

        return round($horas, 2);
    }

    private function calcularFechaFinPorHoras(string $fechaInicio, float $horas, int $trabajadores = 1): string
    {
        $trabajadores   = max(1, $trabajadores);
        $diasNecesarios = (int)ceil($horas / (8 * $trabajadores));
        if ($diasNecesarios <= 0) return $fechaInicio;

        $cursor       = Carbon::parse($fechaInicio);
        $diasContados = 0;

        while ($diasContados < $diasNecesarios) {
            $dow       = $cursor->dayOfWeek;
            $esLaboral = !($dow === 6 || $dow === 0);

            if ($esLaboral) {
                $diasContados++;
            }
            if ($diasContados < $diasNecesarios) {
                $cursor->addDay();
            }
        }

        return $cursor->format('Y-m-d');
    }
}
