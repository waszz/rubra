<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EstadisticasProyectoExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    private $proyecto;
    private $stats;
    private $headings = [];
    private $rows = [];

    public function __construct($proyecto, $stats)
    {
        $this->proyecto = $proyecto;
        $this->stats = $stats;
        $this->buildData();
    }

    private function buildData()
    {
        $tiposNombres = [
            'material'       => 'Materiales',
            'labor'          => 'Mano de Obra',
            'equipment'      => 'Equipos',
            'composition'    => 'Composiciones',
            'sin_clasificar' => 'Sin Clasificar',
        ];

        // ── 1. INFORMACIÓN DEL PROYECTO ──────────────────────────────
        $this->rows[] = ['INFORMACIÓN DEL PROYECTO'];
        $this->rows[] = ['Proyecto',         $this->proyecto->nombre_proyecto];
        $this->rows[] = ['Estado',            ucfirst($this->proyecto->estado_obra ?? '—')];
        $this->rows[] = ['Metros cuadrados',  number_format($this->proyecto->metros_cuadrados ?? 0, 2, ',', '.') . ' m²'];
        $this->rows[] = ['Beneficio',         ($this->proyecto->beneficio ?? 0) . '%'];
        $this->rows[] = ['IVA',               ($this->proyecto->impuestos ?? 22) . '%'];
        if ($this->proyecto->carga_social) {
            $this->rows[] = ['Carga Social', $this->proyecto->carga_social . '%'];
        }
        $this->rows[] = [];

        // ── 2. RESUMEN FINANCIERO ────────────────────────────────────
        $this->rows[] = ['RESUMEN FINANCIERO'];
        $this->rows[] = ['Presupuesto Total',           'USD ' . number_format($this->stats['presupuesto'],        0, ',', '.')];
        $this->rows[] = ['Subtotal Base (sin ben./IVA)', 'USD ' . number_format($this->stats['subtotal'],           0, ',', '.')];
        $this->rows[] = ['Beneficio (' . ($this->proyecto->beneficio ?? 0) . '%)', 'USD ' . number_format($this->stats['beneficio'], 0, ',', '.')];
        $this->rows[] = ['Costo Real Ejecutado',        'USD ' . number_format($this->stats['costoReal'],          0, ',', '.')];
        $this->rows[] = ['Costo Real (sin IVA)',        'USD ' . number_format($this->stats['costoRealSubtotal'],  0, ',', '.')];
        $this->rows[] = ['IVA Ejecutado (' . ($this->proyecto->impuestos ?? 22) . '%)', 'USD ' . number_format($this->stats['ivaEjecutado'], 0, ',', '.')];
        $this->rows[] = ['Desviación',                 'USD ' . number_format($this->stats['desviacion'],         0, ',', '.')];
        $this->rows[] = ['Avance Financiero',           number_format($this->stats['avanceFinanciero'], 1) . '%'];
        if (($this->proyecto->metros_cuadrados ?? 0) > 0 && ($this->stats['subtotal'] ?? 0) > 0) {
            $this->rows[] = ['Costo / m²', 'USD ' . number_format($this->stats['subtotal'] / $this->proyecto->metros_cuadrados, 0, ',', '.')];
        }
        $this->rows[] = [];

        // ── 3. DISTRIBUCIÓN DE COSTOS ────────────────────────────────
        if ($this->stats['distribucion']->count()) {
            $totalDist = $this->stats['distribucion']->sum('total');
            $this->rows[] = ['DISTRIBUCIÓN DE COSTOS'];
            $this->rows[] = ['Tipo de Recurso', 'Total (USD)', '%'];
            foreach ($this->stats['distribucion']->sortByDesc('total') as $dist) {
                $label  = $tiposNombres[$dist->tipo] ?? $dist->tipo;
                $pctD   = $totalDist > 0 ? round(($dist->total / $totalDist) * 100, 1) : 0;
                $this->rows[] = [$label, number_format($dist->total, 0, ',', '.'), $pctD . '%'];
            }
            $this->rows[] = ['TOTAL', number_format($totalDist, 0, ',', '.'), '100%'];
            $this->rows[] = [];
        }

        // ── 4. TOP 5 RUBROS CON MAYOR DESVIACIÓN ─────────────────────
        if ($this->stats['topPartidas']->count()) {
            $this->rows[] = ['TOP 5 RUBROS CON MAYOR DESVIACIÓN'];
            $this->rows[] = ['Rubro', 'Presupuesto (USD)', 'Costo Real (USD)', 'Desviación (USD)', 'Var %'];
            foreach ($this->stats['topPartidas'] as $p) {
                $varPct = $p['presupuesto'] > 0
                    ? round((($p['desviacion'] ?? 0) / $p['presupuesto']) * 100, 1) : 0;
                $this->rows[] = [
                    $p['nombre'],
                    number_format($p['presupuesto'], 0, ',', '.'),
                    number_format($p['costo_real'],  0, ',', '.'),
                    number_format($p['desviacion'] ?? 0, 0, ',', '.'),
                    $varPct . '%',
                ];
            }
            $this->rows[] = [];
        }

        // ── 5. MANO DE OBRA ──────────────────────────────────────────
        if (isset($this->stats['manoDeObra']) && $this->stats['manoDeObra']->count()) {
            $pctCS     = $this->stats['pctCS'] ?? 0;
            $totalMOCS = $this->stats['manoDeObra']->sum('totalConCS');
            $this->rows[] = ['MANO DE OBRA POR CARGO / ESPECIALIDAD' . ($pctCS > 0 ? ' — CS: ' . $pctCS . '%' : '')];
            $this->rows[] = ['Cargo / Especialidad', 'Costo Base (USD)', 'Carga Social (USD)', 'Total c/CS (USD)', '%'];
            foreach ($this->stats['manoDeObra'] as $mo) {
                $pctMO = $totalMOCS > 0 ? round(($mo['totalConCS'] / $totalMOCS) * 100, 1) : 0;
                $this->rows[] = [
                    $mo['nombre'],
                    number_format($mo['totalCosto'],  0, ',', '.'),
                    number_format($mo['cargaSocial'], 0, ',', '.'),
                    number_format($mo['totalConCS'],  0, ',', '.'),
                    $pctMO . '%',
                ];
            }
            $this->rows[] = [
                'TOTAL MANO DE OBRA',
                number_format($this->stats['manoDeObra']->sum('totalCosto'),  0, ',', '.'),
                number_format($this->stats['manoDeObra']->sum('cargaSocial'), 0, ',', '.'),
                number_format($totalMOCS, 0, ',', '.'),
                '100%',
            ];
            $this->rows[] = [];
        }

        // ── 6. TODOS LOS MATERIALES ───────────────────────────────────
        $materiales = isset($this->stats['todosLosMateriales'])
            ? $this->stats['todosLosMateriales']
            : $this->stats['mayoresMateriales'];

        if ($materiales->count()) {
            $isTodos    = isset($this->stats['todosLosMateriales']);
            $totalMat   = $materiales->sum('costoReal');
            $this->rows[] = [$isTodos ? 'TODOS LOS MATERIALES' : 'MAYORES MATERIALES CONSUMIDOS (TOP 10)'];
            $this->rows[] = ['#', 'Material', 'Cantidad', 'Unidad', 'P. Unitario (USD)', 'Total (USD)', '%'];
            foreach ($materiales as $i => $mat) {
                $pctMat = $totalMat > 0 ? round(($mat['costoReal'] / $totalMat) * 100, 2) : 0;
                $this->rows[] = [
                    $i + 1,
                    $mat['nombre'],
                    number_format($mat['cantidad'],       2, ',', '.'),
                    $mat['unidad'] ?? '',
                    number_format($mat['precioUnitario'], 2, ',', '.'),
                    number_format($mat['costoReal'],      0, ',', '.'),
                    $pctMat . '%',
                ];
            }
            $this->rows[] = ['', 'TOTAL MATERIALES', '', '', '', number_format($totalMat, 0, ',', '.'), '100%'];
            $this->rows[] = [];
        }
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 22,
            'C' => 18,
            'D' => 15,
            'E' => 20,
            'F' => 18,
            'G' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sectionHeaders = [
            'INFORMACIÓN DEL PROYECTO',
            'RESUMEN FINANCIERO',
            'DISTRIBUCIÓN DE COSTOS',
            'TOP 5 RUBROS CON MAYOR DESVIACIÓN',
            'MANO DE OBRA POR CARGO / ESPECIALIDAD',
            'TODOS LOS MATERIALES',
            'MAYORES MATERIALES CONSUMIDOS (TOP 10)',
        ];

        $tableHeaders = ['Tipo de Recurso', 'Rubro', 'Material', 'Cargo / Especialidad', '#'];
        $totalRows    = ['TOTAL', 'TOTAL MATERIALES', 'TOTAL MANO DE OBRA', 'TOTAL TOP 10'];

        $styles = [];
        foreach ($this->rows as $index => $row) {
            $excelRow = $index + 1;

            if (empty($row) || !isset($row[0])) continue;

            // Section headers
            $isSectionHeader = false;
            foreach ($sectionHeaders as $sh) {
                if (str_starts_with($row[0], $sh)) { $isSectionHeader = true; break; }
            }
            if ($isSectionHeader) {
                $styles["A{$excelRow}:G{$excelRow}"] = [
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
                ];
                continue;
            }

            // Table header rows
            $isTableHeader = false;
            foreach ($tableHeaders as $th) {
                if ($row[0] === $th) { $isTableHeader = true; break; }
            }
            if ($isTableHeader) {
                $styles["A{$excelRow}:G{$excelRow}"] = [
                    'font' => ['bold' => true, 'color' => ['rgb' => '374151']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                ];
                continue;
            }

            // Total rows
            $isTotalRow = false;
            foreach ($totalRows as $tr) {
                if (str_starts_with($row[0], $tr) || str_ends_with($row[0] ?? '', $tr)) {
                    $isTotalRow = true; break;
                }
            }
            if ($isTotalRow || (isset($row[1]) && str_starts_with($row[1] ?? '', 'TOTAL'))) {
                $styles["A{$excelRow}:G{$excelRow}"] = [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                ];
            }
        }

        return $styles;
    }
}
