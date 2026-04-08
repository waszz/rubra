<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCharts;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PanelHistorialSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize, WithCharts
{
    public function title(): string
    {
        return 'Historial Mensual';
    }

    public function array(): array
    {
        $precios = ['basico' => 12, 'profesional' => 24, 'enterprise' => 59];

        $rows = [
            ['HISTORIAL MENSUAL - ÚLTIMOS 12 MESES'],
            [''],
            ['Mes', 'Suscriptores', 'Básico', 'Profesional', 'Enterprise', 'Ingresos Totales (USD)', 'Variación'],
        ];

        $ingresoAnterior = null;

        for ($i = 11; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $fin   = $fecha->copy()->endOfMonth();
            $mes   = $fecha->format('M Y');

            $basico      = User::where('plan', 'basico')->where('created_at', '<=', $fin)->whereNull('deleted_at')->count();
            $profesional = User::where('plan', 'profesional')->where('created_at', '<=', $fin)->whereNull('deleted_at')->count();
            $enterprise  = User::where('plan', 'enterprise')->where('created_at', '<=', $fin)->whereNull('deleted_at')->count();
            $suscriptores = $basico + $profesional + $enterprise;
            $ingreso = ($basico * $precios['basico']) + ($profesional * $precios['profesional']) + ($enterprise * $precios['enterprise']);

            $variacion = '';
            if ($ingresoAnterior !== null) {
                $diff = $ingreso - $ingresoAnterior;
                if ($diff > 0)      $variacion = '+$' . number_format($diff, 0);
                elseif ($diff < 0)  $variacion = '-$' . number_format(abs($diff), 0);
                else                $variacion = '—';
            }

            $rows[] = [$mes, $suscriptores, $basico, $profesional, $enterprise, $ingreso, $variacion];

            $ingresoAnterior = $ingreso;
        }

        return $rows;
    }

    public function charts(): array
    {
        $sheetName = 'Historial Mensual';
        $dataRows  = 12; // filas 4–15

        $charts = [];

        // ─── Gráfico 1: Suscriptores ──────────────────────────────────
        $series1 = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            [0],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Suscriptores'])],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$A\$4:\$A\$15", null, $dataRows)],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$B\$4:\$B\$15", null, $dataRows)]
        );
        $series1->setPlotDirection(DataSeries::DIRECTION_COL);

        $chartSubs = new Chart(
            'chart_suscriptores',
            new Title('Evolución de Suscriptores'),
            new Legend(Legend::POSITION_TOP, null, false),
            new PlotArea(null, [$series1])
        );
        $chartSubs->setTopLeftPosition('I2');
        $chartSubs->setBottomRightPosition('S18');
        $charts[] = $chartSubs;

        // ─── Gráfico 2: Ingresos ──────────────────────────────────────
        $series2 = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            [0],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Ingresos Mensuales (USD)'])],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetName}'!\$A\$4:\$A\$15", null, $dataRows)],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetName}'!\$F\$4:\$F\$15", null, $dataRows)]
        );
        $series2->setPlotDirection(DataSeries::DIRECTION_COL);

        $chartIng = new Chart(
            'chart_ingresos',
            new Title('Ingresos Mensuales (USD)'),
            new Legend(Legend::POSITION_TOP, null, false),
            new PlotArea(null, [$series2])
        );
        $chartIng->setTopLeftPosition('I20');
        $chartIng->setBottomRightPosition('S36');
        $charts[] = $chartIng;

        return $charts;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
