<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PanelResumenSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function title(): string
    {
        return 'Resumen';
    }

    public function array(): array
    {
        $precios = ['basico' => 12, 'profesional' => 24, 'enterprise' => 59];

        $gratis      = User::where('plan', 'gratis')->whereNull('deleted_at')->count();
        $basico      = User::where('plan', 'basico')->whereNull('deleted_at')->count();
        $profesional = User::where('plan', 'profesional')->whereNull('deleted_at')->count();
        $enterprise  = User::where('plan', 'enterprise')->whereNull('deleted_at')->count();
        $total       = $gratis + $basico + $profesional + $enterprise;

        $subBasico      = $basico * $precios['basico'];
        $subProfesional = $profesional * $precios['profesional'];
        $subEnterprise  = $enterprise * $precios['enterprise'];
        $ingresoTotal   = $subBasico + $subProfesional + $subEnterprise;

        $trial = User::where('plan', 'gratis')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->whereNull('deleted_at')
            ->count();

        $nuevos = User::where('created_at', '>=', now()->subDays(7))->whereNull('deleted_at')->count();
        $bajas  = User::whereNotNull('deleted_at')->count();

        $conversion = $total > 0
            ? round((($basico + $profesional + $enterprise) / $total) * 100, 1)
            : 0;

        return [
            ['PANEL ADMINISTRATIVO - RUBRA'],
            ['Generado el:', now()->format('d/m/Y H:i')],
            [''],
            ['MÉTRICAS GENERALES'],
            ['Usuarios en Trial',        $trial],
            ['Usuarios nuevos (7 días)', $nuevos],
            ['Bajas (eliminados)',        $bajas],
            ['Total activos',             $total],
            ['Tasa de conversión',        $conversion . '%'],
            [''],
            ['USUARIOS POR PLAN'],
            ['Plan',         'Usuarios', 'Precio/mes', 'Subtotal Mensual'],
            ['Gratis',       $gratis,    '$0',         '$0'],
            ['Básico',       $basico,    '$' . $precios['basico'],      '$' . number_format($subBasico, 0)],
            ['Profesional',  $profesional,'$' . $precios['profesional'], '$' . number_format($subProfesional, 0)],
            ['Enterprise',   $enterprise,'$' . $precios['enterprise'],  '$' . number_format($subEnterprise, 0)],
            ['TOTAL',        $total,     '',           '$' . number_format($ingresoTotal, 0)],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1  => ['font' => ['bold' => true, 'size' => 14]],
            4  => ['font' => ['bold' => true, 'size' => 12]],
            11 => ['font' => ['bold' => true, 'size' => 12]],
            12 => ['font' => ['bold' => true]],
            17 => ['font' => ['bold' => true]],
        ];
    }
}
