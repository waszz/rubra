<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Panel extends Component
{
    public function getEstadisticas(): array
    {
        // Usuarios en período de prueba (plan = 'gratis' y trial no expirado)
        $usuariosEnTrial = User::where('plan', 'gratis')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->where('deleted_at', null)
            ->count();

        // Usuarios creados nuevos (últimos 7 días)
        $usuariosNuevos = User::where('created_at', '>=', now()->subDays(7))
            ->where('deleted_at', null)
            ->count();

        // Usuarios creados fijos (más de 3 meses de antigüedad con plan activo)
        $usuariosCreatedoFijos = User::where('created_at', '<=', now()->subMonths(3))
            ->where('deleted_at', null)
            ->count();

        // Bajas (usuarios eliminados)
        $bajas = User::whereNotNull('deleted_at')->count();

        // Usuarios por plan
        $porPlan = [
            'gratis' => User::where('plan', 'gratis')->where('deleted_at', null)->count(),
            'basico' => User::where('plan', 'basico')->where('deleted_at', null)->count(),
            'profesional' => User::where('plan', 'profesional')->where('deleted_at', null)->count(),
            'enterprise' => User::where('plan', 'enterprise')->where('deleted_at', null)->count(),
        ];

        // Precios mensuales de planes (en dólares)
        $precios = [
            'basico' => 12,
            'profesional' => 24,
            'enterprise' => 59,
        ];

        // Cálculo de ingresos mensuales por plan
        $ingresos = [
            'basico' => $porPlan['basico'] * $precios['basico'],
            'profesional' => $porPlan['profesional'] * $precios['profesional'],
            'enterprise' => $porPlan['enterprise'] * $precios['enterprise'],
        ];

        $ingresoTotal = array_sum($ingresos);
        $totalActivos = array_sum($porPlan);

        return [
            'usuariosEnTrial' => $usuariosEnTrial,
            'usuariosNuevos' => $usuariosNuevos,
            'usuariosCreatedoFijos' => $usuariosCreatedoFijos,
            'bajas' => $bajas,
            'porPlan' => $porPlan,
            'precios' => $precios,
            'ingresos' => $ingresos,
            'ingresoTotal' => $ingresoTotal,
            'totalActivos' => $totalActivos,
        ];
    }

    private function obtenerSuscriptoresPorMes(): array
    {
        $datos = [];
        $meses = [];
        $precios = [
            'basico' => 12,
            'profesional' => 24,
            'enterprise' => 59,
        ];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $meses[] = $fecha->format('M Y');

            $basico = User::where('plan', 'basico')
                ->where('created_at', '<=', $fecha->copy()->endOfMonth())
                ->where('deleted_at', null)->count();

            $profesional = User::where('plan', 'profesional')
                ->where('created_at', '<=', $fecha->copy()->endOfMonth())
                ->where('deleted_at', null)->count();

            $enterprise = User::where('plan', 'enterprise')
                ->where('created_at', '<=', $fecha->copy()->endOfMonth())
                ->where('deleted_at', null)->count();

            $suscriptores = $basico + $profesional + $enterprise;
            $ingreso = ($basico * $precios['basico']) + ($profesional * $precios['profesional']) + ($enterprise * $precios['enterprise']);

            $datos['suscriptores'][] = $suscriptores;
            $datos['ingresos'][] = $ingreso;
        }

        return [
            'labels' => $meses,
            'suscriptores' => $datos['suscriptores'],
            'ingresos' => $datos['ingresos'],
        ];
    }

    public function render()
    {
        $stats = $this->getEstadisticas();
        $suscriptoresMes = $this->obtenerSuscriptoresPorMes();

        return view('livewire.admin.panel', [
            'stats' => $stats,
            'suscriptoresMes' => $suscriptoresMes,
        ])->layout('layouts.app');
    }
}
