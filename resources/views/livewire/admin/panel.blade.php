<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-12">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase mb-2">Panel RUBRA</h1>
            <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Estadísticas y análisis de usuarios</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('panel.export.excel') }}"
               class="flex items-center gap-2 bg-emerald-600 text-white px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Excel
            </a>
            <a href="{{ route('panel.export.pdf') }}"
               class="flex items-center gap-2 bg-red-600 text-white px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-red-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                PDF
            </a>
            <a href="{{ route('admin.usuarios') }}" class="bg-purple-600 text-white px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-purple-700 transition-colors">
                Gestionar Usuarios
            </a>
        </div>
    </div>

    {{-- Estadísticas principales --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        {{-- En período de prueba --}}
        <div class="bg-[#111] border border-[#2a2a2a] rounded-xl p-6 hover:border-blue-500/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">En Período de Prueba</h3>
                <svg class="w-5 h-5 text-blue-500/60" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                </svg>
            </div>
            <p class="text-3xl font-black text-blue-500">{{ $stats['usuariosEnTrial'] }}</p>
            <p class="text-xs text-gray-600 mt-2">Usuarios activos en prueba gratuita</p>
        </div>

        {{-- Usuarios nuevos --}}
        <div class="bg-[#111] border border-[#2a2a2a] rounded-xl p-6 hover:border-green-500/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Usuarios Nuevos</h3>
                <svg class="w-5 h-5 text-green-500/60" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            <p class="text-3xl font-black text-green-500">{{ $stats['usuariosNuevos'] }}</p>
            <p class="text-xs text-gray-600 mt-2">Últimos 7 días</p>
        </div>

        {{-- Usuarios fijos --}}
        <div class="bg-[#111] border border-[#2a2a2a] rounded-xl p-6 hover:border-purple-500/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Usuarios Fijos</h3>
                <svg class="w-5 h-5 text-purple-500/60" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <p class="text-3xl font-black text-purple-500">{{ $stats['usuariosCreatedoFijos'] }}</p>
            <p class="text-xs text-gray-600 mt-2">+3 meses de antigüedad</p>
        </div>

        {{-- Bajas --}}
        <div class="bg-[#111] border border-[#2a2a2a] rounded-xl p-6 hover:border-red-500/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Bajas</h3>
                <svg class="w-5 h-5 text-red-500/60" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
            </div>
            <p class="text-3xl font-black text-red-500">{{ $stats['bajas'] }}</p>
            <p class="text-xs text-gray-600 mt-2">Usuarios eliminados</p>
        </div>
    </div>

    {{-- Usuarios por plan con ingresos --}}
    <div class="mb-12">
        <h2 class="text-xl font-black text-white uppercase tracking-wider mb-6">Usuarios y Ingresos por Plan</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            {{-- Básico --}}
            <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/5 border border-blue-500/20 rounded-xl p-6">
                <h3 class="text-sm font-bold uppercase tracking-widest text-blue-400 mb-4">Plan Básico</h3>
                
                <div class="space-y-3 mb-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Usuarios</p>
                        <p class="text-3xl font-black text-blue-400">{{ $stats['porPlan']['basico'] }}</p>
                    </div>
                    <div class="border-t border-blue-500/20 pt-3">
                        <p class="text-xs text-gray-500 mb-1">Precio Mensual</p>
                        <p class="text-lg font-black text-blue-400">${{ $stats['precios']['basico'] }}/usuario</p>
                    </div>
                    <div class="bg-blue-500/20 rounded-lg p-3">
                        <p class="text-xs text-blue-400/70 mb-1">SUBTOTAL MENSUAL</p>
                        <p class="text-2xl font-black text-blue-300">${{ number_format($stats['ingresos']['basico'], 0) }}</p>
                    </div>
                </div>
                
                <p class="text-xs text-blue-600">3 proyectos</p>
            </div>

            {{-- Profesional --}}
            <div class="bg-gradient-to-br from-purple-500/10 to-purple-600/5 border border-purple-500/20 rounded-xl p-6">
                <h3 class="text-sm font-bold uppercase tracking-widest text-purple-400 mb-4">Plan Profesional</h3>
                
                <div class="space-y-3 mb-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Usuarios</p>
                        <p class="text-3xl font-black text-purple-400">{{ $stats['porPlan']['profesional'] }}</p>
                    </div>
                    <div class="border-t border-purple-500/20 pt-3">
                        <p class="text-xs text-gray-500 mb-1">Precio Mensual</p>
                        <p class="text-lg font-black text-purple-400">${{ $stats['precios']['profesional'] }}/usuario</p>
                    </div>
                    <div class="bg-purple-500/20 rounded-lg p-3">
                        <p class="text-xs text-purple-400/70 mb-1">SUBTOTAL MENSUAL</p>
                        <p class="text-2xl font-black text-purple-300">${{ number_format($stats['ingresos']['profesional'], 0) }}</p>
                    </div>
                </div>
                
                <p class="text-xs text-purple-600">20 proyectos</p>
            </div>

            {{-- Enterprise --}}
            <div class="bg-gradient-to-br from-orange-500/10 to-orange-600/5 border border-orange-500/20 rounded-xl p-6">
                <h3 class="text-sm font-bold uppercase tracking-widest text-orange-400 mb-4">Plan Enterprise</h3>
                
                <div class="space-y-3 mb-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Usuarios</p>
                        <p class="text-3xl font-black text-orange-400">{{ $stats['porPlan']['enterprise'] }}</p>
                    </div>
                    <div class="border-t border-orange-500/20 pt-3">
                        <p class="text-xs text-gray-500 mb-1">Precio Mensual</p>
                        <p class="text-lg font-black text-orange-400">${{ $stats['precios']['enterprise'] }}/usuario</p>
                    </div>
                    <div class="bg-orange-500/20 rounded-lg p-3">
                        <p class="text-xs text-orange-400/70 mb-1">SUBTOTAL MENSUAL</p>
                        <p class="text-2xl font-black text-orange-300">${{ number_format($stats['ingresos']['enterprise'], 0) }}</p>
                    </div>
                </div>
                
                <p class="text-xs text-orange-600">50 proyectos</p>
            </div>
        </div>

        {{-- Total de ingresos con historial --}}
        <div class="bg-gradient-to-r from-green-500/20 to-emerald-500/20 border border-green-500/30 rounded-xl p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-green-400 mb-2">Ingresos Mensuales Totales</p>
                    <p class="text-xs text-gray-500">Suma de los 3 planes pagos</p>
                </div>
                <p class="text-5xl font-black text-green-400">${{ number_format($stats['ingresoTotal'], 0) }}</p>
            </div>

            {{-- Historial mensual --}}
            <div class="border-t border-green-500/20 pt-5">
                <p class="text-xs font-bold uppercase tracking-widest text-green-500/70 mb-4">Historial de los últimos 12 meses</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
                    @foreach($suscriptoresMes['labels'] as $i => $mes)
                        @php
                            $ingreso = $suscriptoresMes['ingresos'][$i];
                            $esCurrent = $i === count($suscriptoresMes['labels']) - 1;
                        @endphp
                        <div class="rounded-lg p-3 text-center {{ $esCurrent ? 'bg-green-500/30 border border-green-400/50' : 'bg-black/20 border border-white/5' }}">
                            <p class="text-[10px] font-bold uppercase tracking-widest {{ $esCurrent ? 'text-green-300' : 'text-gray-500' }} mb-1">{{ $mes }}</p>
                            <p class="text-lg font-black {{ $esCurrent ? 'text-green-300' : ($ingreso > 0 ? 'text-white' : 'text-gray-600') }}">${{ number_format($ingreso, 0) }}</p>
                            @if(!$esCurrent && $i > 0)
                                @php
                                    $anterior = $suscriptoresMes['ingresos'][$i - 1];
                                    $diff = $ingreso - $anterior;
                                @endphp
                                @if($diff > 0)
                                    <p class="text-[9px] text-green-400 mt-1">▲ +${{ number_format($diff, 0) }}</p>
                                @elseif($diff < 0)
                                    <p class="text-[9px] text-red-400 mt-1">▼ -${{ number_format(abs($diff), 0) }}</p>
                                @else
                                    <p class="text-[9px] text-gray-600 mt-1">─</p>
                                @endif
                            @elseif($esCurrent)
                                <p class="text-[9px] text-green-400/70 mt-1">Mes actual</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico de Suscriptores / Ingresos por Mes --}}
    <div class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-white uppercase tracking-wider">Evolución Mensual</h2>
            <div class="flex bg-[#111] border border-[#2a2a2a] rounded-lg p-1 gap-1">
                <button id="btnSuscriptores"
                    onclick="switchChart('suscriptores')"
                    class="px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest transition-all bg-blue-600 text-white">
                    Suscriptores
                </button>
                <button id="btnIngresos"
                    onclick="switchChart('ingresos')"
                    class="px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest transition-all text-gray-400 hover:text-white">
                    Ingresos
                </button>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-blue-500/10 to-purple-500/10 border border-blue-500/20 rounded-xl p-8" id="chartWrapper">
            <div style="position: relative; height: 400px; width: 100%;">
                <canvas id="chartSuscriptores"></canvas>
            </div>
        </div>
    </div>

    {{-- Breakdown de estadísticas --}}
    <div class="bg-[#111] border border-[#2a2a2a] rounded-xl p-8">
        <h2 class="text-lg font-black text-white uppercase tracking-wider mb-6">Resumen General</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Total de Usuarios</p>
                <p class="text-3xl font-black text-white">
                    {{ $stats['totalActivos'] + $stats['bajas'] }}
                </p>
                <p class="text-xs text-gray-600 mt-2">
                    {{ $stats['totalActivos'] }} activos / {{ $stats['bajas'] }} eliminados
                </p>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Tasa de Retención</p>
                <p class="text-3xl font-black text-green-500">
                    @if($stats['totalActivos'] + $stats['bajas'] > 0)
                        {{ round(($stats['totalActivos'] / ($stats['totalActivos'] + $stats['bajas'])) * 100, 1) }}%
                    @else
                        0%
                    @endif
                </p>
                <p class="text-xs text-gray-600 mt-2">Usuarios que mantienen sus cuentas</p>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Conversión (No Gratis)</p>
                <p class="text-3xl font-black text-orange-500">
                    @if($stats['totalActivos'] > 0)
                        {{ round((($stats['porPlan']['basico'] + $stats['porPlan']['profesional'] + $stats['porPlan']['enterprise']) / $stats['totalActivos']) * 100, 1) }}%
                    @else
                        0%
                    @endif
                </p>
                <p class="text-xs text-gray-600 mt-2">Del total de usuarios activos</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartLabels = @json($suscriptoresMes['labels']);
    const dataSuscriptores = @json($suscriptoresMes['suscriptores']);
    const dataIngresos = @json($suscriptoresMes['ingresos']);

    let chartInstance = null;
    let modoActual = 'suscriptores';

    function buildChart(modo) {
        const ctx = document.getElementById('chartSuscriptores');
        if (!ctx) return;

        if (chartInstance) {
            chartInstance.destroy();
        }

        const esSuscriptores = modo === 'suscriptores';
        const color = esSuscriptores ? '#3b82f6' : '#10b981';
        const colorBg = esSuscriptores ? 'rgba(59, 130, 246, 0.1)' : 'rgba(16, 185, 129, 0.1)';
        const label = esSuscriptores ? 'Usuarios Suscritos (Activos)' : 'Ingresos Mensuales (USD)';
        const data = esSuscriptores ? dataSuscriptores : dataIngresos;

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: label,
                    data: data,
                    borderColor: color,
                    backgroundColor: colorBg,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: color,
                    pointBorderColor: '#000',
                    pointBorderWidth: 2,
                    pointHoverRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#9ca3af',
                            font: { size: 13, weight: 'bold' },
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: color,
                        borderWidth: 2,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                if (esSuscriptores) {
                                    return 'Suscriptores: ' + context.parsed.y;
                                } else {
                                    return 'Ingresos: $' + context.parsed.y;
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.08)' },
                        ticks: {
                            color: '#9ca3af',
                            font: { size: 12 },
                            stepSize: esSuscriptores ? 1 : undefined,
                            callback: function(value) {
                                return esSuscriptores ? value : '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: { color: 'transparent' },
                        ticks: { color: '#9ca3af', font: { size: 12 } }
                    }
                }
            }
        });

        // Actualizar wrapper color
        const wrapper = document.getElementById('chartWrapper');
        if (wrapper) {
            wrapper.className = esSuscriptores
                ? 'bg-gradient-to-br from-blue-500/10 to-purple-500/10 border border-blue-500/20 rounded-xl p-8'
                : 'bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/20 rounded-xl p-8';
        }
    }

    function switchChart(modo) {
        if (modo === modoActual) return;
        modoActual = modo;
        buildChart(modo);

        // Actualizar botones
        const btnSus = document.getElementById('btnSuscriptores');
        const btnIng = document.getElementById('btnIngresos');

        if (modo === 'suscriptores') {
            btnSus.className = 'px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest transition-all bg-blue-600 text-white';
            btnIng.className = 'px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest transition-all text-gray-400 hover:text-white';
        } else {
            btnIng.className = 'px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest transition-all bg-green-600 text-white';
            btnSus.className = 'px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest transition-all text-gray-400 hover:text-white';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        buildChart('suscriptores');
    });
</script>
@endpush
