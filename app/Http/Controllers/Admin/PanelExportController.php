<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\PanelAdminExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class PanelExportController extends Controller
{
    private function getData(): array
    {
        $precios = ['basico' => 12, 'profesional' => 24, 'enterprise' => 59];

        $porPlan = [
            'gratis'      => User::where('plan', 'gratis')->whereNull('deleted_at')->count(),
            'basico'      => User::where('plan', 'basico')->whereNull('deleted_at')->count(),
            'profesional' => User::where('plan', 'profesional')->whereNull('deleted_at')->count(),
            'enterprise'  => User::where('plan', 'enterprise')->whereNull('deleted_at')->count(),
        ];

        $ingresos = [
            'basico'      => $porPlan['basico'] * $precios['basico'],
            'profesional' => $porPlan['profesional'] * $precios['profesional'],
            'enterprise'  => $porPlan['enterprise'] * $precios['enterprise'],
        ];

        $stats = [
            'usuariosEnTrial' => User::where('plan', 'gratis')->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now())->whereNull('deleted_at')->count(),
            'usuariosNuevos'  => User::where('created_at', '>=', now()->subDays(7))->whereNull('deleted_at')->count(),
            'usuariosCreatedoFijos' => User::where('created_at', '<=', now()->subMonths(3))->whereNull('deleted_at')->count(),
            'bajas'           => User::whereNotNull('deleted_at')->count(),
            'porPlan'         => $porPlan,
            'precios'         => $precios,
            'ingresos'        => $ingresos,
            'ingresoTotal'    => array_sum($ingresos),
            'totalActivos'    => array_sum($porPlan),
        ];

        // Historial 12 meses
        $historial = ['labels' => [], 'suscriptores' => [], 'ingresos' => []];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $fin   = $fecha->copy()->endOfMonth();

            $b = User::where('plan', 'basico')->where('created_at', '<=', $fin)->whereNull('deleted_at')->count();
            $p = User::where('plan', 'profesional')->where('created_at', '<=', $fin)->whereNull('deleted_at')->count();
            $e = User::where('plan', 'enterprise')->where('created_at', '<=', $fin)->whereNull('deleted_at')->count();

            $historial['labels'][]       = $fecha->format('M Y');
            $historial['suscriptores'][] = $b + $p + $e;
            $historial['ingresos'][]     = ($b * $precios['basico']) + ($p * $precios['profesional']) + ($e * $precios['enterprise']);
        }

        return compact('stats', 'historial');
    }

    public function exportPdf()
    {
        $data = $this->getData();

        $pdf = Pdf::loadView('pdf.panel-admin', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('panel-admin-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(
            new PanelAdminExport(),
            'panel-admin-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
