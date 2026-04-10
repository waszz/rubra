<div class="min-h-screen bg-[#090909] p-6 space-y-6">


    {{-- NAVBAR --}}
    @if($proyecto)
    <nav class="border-b border-white/5 bg-[#0d0d0d]">

        {{-- Fila superior: back + nombre --}}
        <div class="flex items-center px-4 py-3 gap-3">
            <a href="{{ route('proyectos.presupuesto', $proyecto) }}" class="text-gray-500 hover:text-white transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-white font-black text-sm uppercase tracking-widest truncate">{{ $proyecto->nombre_proyecto }}</h1>
                    <span class="bg-green-500/10 text-green-500 text-sm font-black px-2 py-0.5 rounded border border-green-500/20 flex items-center gap-1 shrink-0">
                        <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span> ONLINE
                    </span>
                </div>
                <p class="text-sm text-gray-600 uppercase tracking-widest font-bold">ESTADÍSTICAS ▾</p>
            </div>
        </div>

        {{-- Fila inferior: tabs (scroll horizontal en mobile) --}}
        <div class="flex items-center gap-1 px-4 pb-2 overflow-x-auto scrollbar-none">
            @foreach([
                ['label' => 'Presupuesto', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z', 'active' => false, 'route' => route('proyectos.presupuesto', $proyecto)],
                ['label' => 'Gantt',       'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'active' => false, 'route' => route('proyectos.gantt', $proyecto)],
                ['label' => 'Diario',      'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'active' => false, 'route' => route('proyectos.diario', $proyecto)],
                ['label' => 'Bitácora',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'active' => false, 'route' => route('proyectos.bitacora', $proyecto)],
                ['label' => 'Estadísticas','icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'active' => true, 'route' => '#'],
            ] as $tab)
                @if(($tab['label'] === 'Gantt' && !auth()->user()->puede('mapa')) || ($tab['label'] === 'Estadísticas' && !auth()->user()->puede('estadisticas')))
                    @continue
                @endif
                <a href="{{ $tab['route'] }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-black uppercase tracking-wider transition-all whitespace-nowrap shrink-0
                       {{ $tab['active'] ? 'bg-white text-black' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                    </svg>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

    </nav>
    @endif
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white uppercase tracking-[0.2em]">Estadísticas</h1>
            <p class="text-base text-gray-500 uppercase tracking-widest mt-1">Análisis detallado de costos, desviaciones y progreso temporal.</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            {{-- SELECTOR DE PROYECTO (solo en modo global) --}}
            @if(!$modoProyecto)
                <select wire:model.live="proyectoId"
                    class="px-4 py-2.5 rounded-xl bg-[#111] text-white border border-gray-800 focus:border-gray-600 focus:outline-none text-[13px] cursor-pointer min-w-[220px]">
                    @foreach($proyectos as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre_proyecto }}</option>
                    @endforeach
                </select>
            @endif

            {{-- BOTONES DE EXPORTACIÓN --}}
            @if($proyecto)
            <a href="{{ route('estadisticas.export.excel', $proyecto->id) }}" target="_blank"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-600/10 border border-gray-600/30 text-gray-600 text-[12px] font-black uppercase tracking-wider hover:bg-gray-600/15 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Excel
            </a>

            <a href="{{ route('estadisticas.export.pdf', $proyecto->id) }}" target="_blank"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-600/10 border border-gray-600/30 text-gray-600 text-[12px] font-black uppercase tracking-wider hover:bg-gray-600/15 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                PDF
            </a>
            @endif
        </div>
    </div>

    @if(!$proyecto || !$stats)
        <div class="flex items-center justify-center h-64 border-2 border-dashed border-gray-800 rounded-3xl">
            <p class="text-gray-600 text-base font-bold uppercase tracking-widest">Seleccioná un proyecto para ver sus estadísticas</p>
        </div>
    @else

    {{-- CARDS SUPERIORES --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Presupuesto --}}
        <div class="bg-[#111] border border-gray-800/50 rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-blue-400 text-lg">$</span>
                <p class="text-xs text-gray-500 font-black uppercase tracking-[0.15em]">Presupuesto Total</p>
            </div>
            <p class="text-2xl font-black text-white tracking-tighter">{{ number_format($stats['presupuesto'], 0, ',', '.') }}</p>
        </div>

        {{-- Avance financiero --}}
        <div class="bg-[#111] border border-gray-800/50 rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <p class="text-xs text-gray-500 font-black uppercase tracking-[0.15em]">Avance Financiero</p>
            </div>
            <p class="text-2xl font-black text-cyan-400 tracking-tighter">{{ number_format($stats['avanceFinanciero'], 1) }}%</p>
            <div class="mt-2 h-1.5 bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-cyan-500 rounded-full" style="width: {{ min($stats['avanceFinanciero'], 100) }}%"></div>
            </div>
        </div>

        {{-- Costo real (Precio Final Ejecutado) --}}
        <div class="bg-[#111] border border-gray-800/50 rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs text-gray-500 font-black uppercase tracking-[0.15em]">Precio Final (Real)</p>
            </div>
            <p class="text-2xl font-black text-white tracking-tighter">{{ number_format($stats['costoReal'], 0, ',', '.') }}</p>
            @php $desv = $stats['desviacion']; @endphp
            <p class="text-sm mt-1 font-bold {{ $desv > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                {{ $desv > 0 ? '▲' : '▼' }} Desv. USD {{ number_format(abs($desv), 0, ',', '.') }}
            </p>
            @if($stats['ivaEjecutado'] > 0)
            <div class="text-xs text-gray-600 mt-2 pt-2 border-t border-gray-700/50">
                <p>Subtotal: USD {{ number_format($stats['costoRealSubtotal'], 0, ',', '.') }}</p>
                <p>+ IVA: USD {{ number_format($stats['ivaEjecutado'], 0, ',', '.') }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- FILA CENTRAL: Dona + Top partidas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Distribución de costos --}}
<div class="bg-[#111] border border-gray-800/50 rounded-2xl p-6">
    <h2 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-5">Distribución de Costos</h2>
    @if($stats['distribucion']->count())
        <div class="flex flex-col items-center gap-5">
            <div id="data-dist" wire:key="data-dist-{{ $proyecto->id }}" data-value='@json($stats["distribucion"])' class="hidden"></div>
            <div class="w-80 h-80" wire:ignore>
                <canvas id="dona-distribucion" width="128" height="128"></canvas>
            </div>
            <div class="w-full space-y-2">
                @php
                    $colDist   = ['material'=>'#3b82f6','labor'=>'#22c55e','equipment'=>'#f97316','composition'=>'#a855f7'];
                    $labelDist = ['material'=>'Materiales','labor'=>'Mano de Obra','equipment'=>'Equipos','composition'=>'Composiciones'];
                @endphp
                @foreach($stats['distribucion'] as $dist)
                <div class="flex justify-between items-center text-base">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full" style="background:{{ $colDist[$dist->tipo] ?? '#6b7280' }}"></div>
                        <span class="text-gray-400 font-bold uppercase">{{ $labelDist[$dist->tipo] ?? $dist->tipo }}</span>
                    </div>
                    <span class="text-white font-black">USD {{ number_format($dist->total, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="flex items-center justify-center h-40 text-gray-700 text-base font-bold uppercase">Sin datos</div>
    @endif
</div>

        {{-- Top 5 partidas --}}
        <div class="bg-[#111] border border-gray-800/50 rounded-2xl p-6">
            <h2 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-5">Mayores Desviaciones (Top 5)</h2>
            @if($stats['topPartidas']->count())
                <div id="data-partidas" wire:key="data-partidas-{{ $proyecto->id }}" data-value='@json($stats["topPartidas"])' class="hidden"></div>
                <div class="space-y-3" wire:ignore>
                    <canvas id="bar-partidas" height="180"></canvas>
                </div>
            @else
                <div class="flex items-center justify-center h-40 text-gray-700 text-base font-bold uppercase">Sin datos</div>
            @endif
        </div>
    </div>

    {{-- MAYORES MATERIALES CONSUMIDOS --}}
    <div class="bg-[#111] border border-gray-800/50 rounded-2xl p-6">
        <h2 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-5">Mayores Materiales Consumidos (Top 10)</h2>
        @if($stats['mayoresMateriales']->count())
            <div class="overflow-x-auto">
                <table class="w-full text-base">
                    <thead>
                        <tr class="border-b border-gray-700/50">
                            <th class="text-left px-4 py-3 text-gray-500 font-black uppercase tracking-widest">#</th>
                            <th class="text-left px-4 py-3 text-gray-500 font-black uppercase tracking-widest">Material</th>
                            <th class="text-center px-4 py-3 text-gray-500 font-black uppercase tracking-widest">Cantidad</th>
                            <th class="text-center px-4 py-3 text-gray-500 font-black uppercase tracking-widest">P. Unitario</th>
                            <th class="text-right px-4 py-3 text-gray-500 font-black uppercase tracking-widest">Costo Real</th>
                            <th class="text-right px-4 py-3 text-gray-500 font-black uppercase tracking-widest">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalMateriales = $stats['mayoresMateriales']->sum('costoReal'); @endphp
                        @foreach($stats['mayoresMateriales'] as $index => $material)
                            @php
                                $porcentaje = $totalMateriales > 0 ? ($material['costoReal'] / $totalMateriales) * 100 : 0;
                            @endphp
                            <tr class="border-b border-gray-700/20 hover:bg-white/5 transition-colors">
                                <td class="px-4 py-2 text-gray-600 font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-gray-300 font-medium">{{ $material['nombre'] }}</td>
                                <td class="px-4 py-2 text-center text-gray-400">{{ number_format($material['cantidad'], 2) }} {{ $material['unidad'] }}</td>
                                <td class="px-4 py-2 text-center text-gray-400 font-mono">USD {{ number_format($material['precioUnitario'], 2, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right text-white font-black font-mono">USD {{ number_format($material['costoReal'], 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right">
                                    <span class="inline-block bg-orange-500/20 text-orange-400 px-2 py-1 rounded font-bold">{{ number_format($porcentaje, 1) }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-700/50 flex justify-between items-center">
                <span class="text-sm text-gray-500 font-black uppercase">TOTAL MATERIALES</span>
                <span class="text-lg font-black text-orange-400">USD {{ number_format($totalMateriales, 0, ',', '.') }}</span>
            </div>
        @else
            <div class="flex items-center justify-center h-40 text-gray-700 text-base font-bold uppercase">Sin materiales cargados</div>
        @endif
    </div>

    {{-- EVOLUCIÓN TEMPORAL --}}
    @if($stats['evolucion']->count())
    <div class="bg-[#111] border border-gray-800/50 rounded-2xl p-6">
        <h2 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-5">Evolución de Costos en el Tiempo</h2>
        <div id="data-evolucion" wire:key="data-evolucion-{{ $proyecto->id }}" data-value='@json($stats["evolucion"])' class="hidden"></div>
        <div wire:ignore>
            <canvas id="line-evolucion" height="80"></canvas>
        </div>
    </div>
    @endif

    @endif

    {{-- SCRIPTS GRÁFICAS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function () {
        function initCharts() {
            const colDist = {material:'#3b82f6',labor:'#22c55e',equipment:'#f97316',composition:'#a855f7'};

            // ── DONA ────────────────────────────────────────────
            const donaEl = document.getElementById('dona-distribucion');
            if (donaEl) {
                const existingDona = Chart.getChart(donaEl);
                if (existingDona) existingDona.destroy();
                const distRaw = document.getElementById('data-dist');
                const distData = distRaw ? JSON.parse(distRaw.dataset.value || '[]') : [];
                if (distData.length) {
                    new Chart(donaEl, {
                        type: 'doughnut',
                        data: {
                            labels: distData.map(d => d.tipo),
                            datasets: [{ data: distData.map(d => parseFloat(d.total) || 0), backgroundColor: distData.map(d => colDist[d.tipo] ?? '#6b7280'), borderWidth: 0, hoverOffset: 6 }]
                        },
                        options: { cutout: '72%', plugins: { legend: { display: false } } }
                    });
                }
            }

            // ── BARRAS ──────────────────────────────────────────
            const barEl = document.getElementById('bar-partidas');
            if (barEl) {
                const existingBar = Chart.getChart(barEl);
                if (existingBar) existingBar.destroy();
                const topRaw = document.getElementById('data-partidas');
                const top = topRaw ? JSON.parse(topRaw.dataset.value || '[]') : [];
                if (top.length) {
                    new Chart(barEl, {
                        type: 'bar',
                        data: {
                            labels: top.map(t => t.nombre.length > 20 ? t.nombre.substring(0,20)+'…' : t.nombre),
                            datasets: [
                                { label: 'Presupuesto', data: top.map(t => parseFloat(t.presupuesto) || 0), backgroundColor: '#3b82f640', borderColor: '#3b82f6', borderWidth: 2, borderRadius: 6 },
                                { label: 'Costo Real',  data: top.map(t => parseFloat(t.costo_real) || 0),  backgroundColor: '#22c55e40', borderColor: '#22c55e',  borderWidth: 2, borderRadius: 6 },
                                { label: 'Desviación',  data: top.map(t => parseFloat(t.desviacion) || 0),  backgroundColor: '#ef444440', borderColor: '#ef4444',  borderWidth: 2, borderRadius: 6 },
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { labels: { color: '#9ca3af', font: { size: 11, weight: 'bold' } } } },
                            scales: {
                                x: { ticks: { color: '#6b7280', font: { size: 9 } }, grid: { color: '#ffffff08' } },
                                y: { ticks: { color: '#6b7280', font: { size: 9 } }, grid: { color: '#ffffff08' }, beginAtZero: true }
                            }
                        }
                    });
                }
            }

            // ── LÍNEA ───────────────────────────────────────────
            const lineEl = document.getElementById('line-evolucion');
            if (lineEl) {
                const existingLine = Chart.getChart(lineEl);
                if (existingLine) existingLine.destroy();
                const evolRaw = document.getElementById('data-evolucion');
                const evol = evolRaw ? JSON.parse(evolRaw.dataset.value || '[]') : [];
                if (evol.length) {
                    new Chart(lineEl, {
                        type: 'line',
                        data: {
                            labels: evol.map(e => e.fecha),
                            datasets: [{
                                label: 'Costo del día',
                                data: evol.map(e => e.costo),
                                borderColor: '#f97316',
                                backgroundColor: '#f9731610',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 3,
                                pointBackgroundColor: '#f97316'
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { labels: { color: '#9ca3af', font: { size: 10 } } } },
                            scales: {
                                x: { ticks: { color: '#6b7280', font: { size: 9 } }, grid: { color: '#ffffff08' } },
                                y: { ticks: { color: '#6b7280', font: { size: 9 } }, grid: { color: '#ffffff08' } }
                            }
                        }
                    });
                }
            }
        }

        // Corre inmediatamente en carga inicial
        initCharts();

        // Registrar el listener de Livewire (funciona sin importar si init ya corrió)
        function registrarListeners() {
            Livewire.on('estadisticas-ready', () => {
                setTimeout(initCharts, 50);
            });
            // Fallback: hook de commit para cualquier update del componente
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => { setTimeout(initCharts, 80); });
            });
        }

        if (typeof Livewire !== 'undefined' && Livewire.on) {
            registrarListeners();
        } else {
            document.addEventListener('livewire:init', registrarListeners);
        }

        document.addEventListener('DOMContentLoaded', initCharts);
    })();
    </script>
</div>
