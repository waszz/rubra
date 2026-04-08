<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
        // Sección 1: INFORMACIÓN DEL PROYECTO
        $this->rows[] = ['INFORMACIÓN DEL PROYECTO'];
        $this->rows[] = ['Proyecto', $this->proyecto->nombre_proyecto];
        $this->rows[] = ['Estado', ucfirst($this->proyecto->estado_obra)];
        $this->rows[] = ['Metros Cuadrados', number_format($this->proyecto->metros_cuadrados, 2, ',', '.')];
        $this->rows[] = [];

        // Sección 2: RESUMEN FINANCIERO
        $this->rows[] = ['RESUMEN FINANCIERO'];
        $this->rows[] = ['Presupuesto Total', 'USD ' . number_format($this->stats['presupuesto'], 2, ',', '.')];
        $this->rows[] = ['Costo Real (Subtotal)', 'USD ' . number_format($this->stats['costoRealSubtotal'], 2, ',', '.')];
        $this->rows[] = ['IVA Ejecutado (' . ($this->proyecto->impuestos ?? 22) . '%)', 'USD ' . number_format($this->stats['ivaEjecutado'], 2, ',', '.')];
        $this->rows[] = ['Precio Final (Real)', 'USD ' . number_format($this->stats['costoReal'], 2, ',', '.')];
        $this->rows[] = ['Desviación', 'USD ' . number_format($this->stats['desviacion'], 2, ',', '.')];
        $this->rows[] = ['Avance Financiero', number_format($this->stats['avanceFinanciero'], 1) . '%'];
        $this->rows[] = [];

        // Sección 3: DISTRIBUCIÓN DE COSTOS
        if ($this->stats['distribucion']->count()) {
            $this->rows[] = ['DISTRIBUCIÓN DE COSTOS'];
            $tiposNombres = [
                'material' => 'Materiales',
                'labor' => 'Mano de Obra',
                'equipment' => 'Equipos',
                'composition' => 'Composiciones'
            ];
            foreach ($this->stats['distribucion'] as $dist) {
                $label = $tiposNombres[$dist->tipo] ?? $dist->tipo;
                $this->rows[] = [$label, 'USD ' . number_format($dist->total, 2, ',', '.')];
            }
            $this->rows[] = [];
        }

        // Sección 4: TOP 5 PARTIDAS CON MAYOR DESVIACIÓN
        if ($this->stats['topPartidas']->count()) {
            $this->rows[] = ['TOP 5 PARTIDAS CON MAYOR DESVIACIÓN'];
            $this->rows[] = ['Partida', 'Presupuesto', 'Costo Real', 'Desviación'];
            foreach ($this->stats['topPartidas'] as $partida) {
                $this->rows[] = [
                    $partida['nombre'],
                    'USD ' . number_format($partida['presupuesto'], 2, ',', '.'),
                    'USD ' . number_format($partida['costo_real'], 2, ',', '.'),
                    'USD ' . number_format($partida['desviacion'] ?? 0, 2, ',', '.')
                ];
            }
            $this->rows[] = [];
        }

        // Sección 5: MAYORES MATERIALES CONSUMIDOS
        if ($this->stats['mayoresMateriales']->count()) {
            $this->rows[] = ['MAYORES MATERIALES CONSUMIDOS (TOP 10)'];
            $this->rows[] = ['Material', 'Cantidad', 'Unidad', 'Precio Unit.', 'Costo Real'];
            $totalMateriales = 0;
            foreach ($this->stats['mayoresMateriales'] as $material) {
                $costoReal = $material['costoReal'] ?? 0;
                $totalMateriales += $costoReal;
                $this->rows[] = [
                    $material['nombre'],
                    number_format($material['cantidad'], 2, ',', '.'),
                    $material['unidad'],
                    'USD ' . number_format($material['precioUnitario'], 2, ',', '.'),
                    'USD ' . number_format($costoReal, 2, ',', '.')
                ];
            }
            $this->rows[] = ['TOTAL MATERIALES', '', '', '', 'USD ' . number_format($totalMateriales, 2, ',', '.')];
            $this->rows[] = [];
        }

        $this->headings = ['ESTADÍSTICAS DEL PROYECTO'];
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
            'A' => 35,
            'B' => 22,
            'C' => 22,
            'D' => 22,
            'E' => 22,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        $rowIndex = 1;

        // Recorrer todas las filas y aplicar estilos
        foreach ($this->rows as $index => $row) {
            $excelRow = $index + 1;
            
            // Título general en primer fila
            if ($index === 0) {
                $styles['A' . $excelRow . ':E' . $excelRow] = [
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '111827']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ];
            }
            // Encabezados de sección (gris claro)
            elseif (isset($row[0]) && in_array($row[0], ['INFORMACIÓN DEL PROYECTO', 'RESUMEN FINANCIERO', 'DISTRIBUCIÓN DE COSTOS', 
                'TOP 5 PARTIDAS CON MAYOR DESVIACIÓN', 'MAYORES MATERIALES CONSUMIDOS (TOP 10)'])) {
                $styles['A' . $excelRow . ':E' . $excelRow] = [
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '111827']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                ];
            }
            // Encabezado tabla (gris claro)
            elseif (isset($row[0]) && ($row[0] === 'Partida' || $row[0] === 'Material')) {
                for ($col = 65; $col <= 69; $col++) { // A-E
                    $colLetter = chr($col);
                    $styles[$colLetter . $excelRow] = [
                        'font' => ['bold' => true, 'color' => ['rgb' => '374151']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                    ];
                }
            }
            // Total row (gris claro)
            elseif (isset($row[0]) && $row[0] === 'TOTAL MATERIALES') {
                $styles['A' . $excelRow . ':E' . $excelRow] = [
                    'font' => ['bold' => true, 'color' => ['rgb' => '111827']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                ];
            }
        }

        return $styles;
    }
}
