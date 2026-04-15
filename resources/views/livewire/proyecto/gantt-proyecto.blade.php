<div class="min-h-screen bg-white dark:bg-[#0a0a0a]">

    {{-- NAVBAR --}}
    <nav class="border-b border-gray-200 dark:border-white/5 bg-white dark:bg-[#0d0d0d]">

        {{-- Fila superior: back + nombre --}}
        <div class="flex items-center justify-between px-4 py-3 gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a wire:navigate href="{{ route('recursos.index') }}" class="text-gray-500 hover:text-black dark:hover:text-white transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-gray-900 dark:text-white font-black text-sm uppercase tracking-widest truncate">{{ $proyecto->nombre_proyecto }}</h1>
                        <span class="bg-green-500/10 text-green-700 dark:text-green-500 text-sm font-black px-2 py-0.5 rounded border border-green-500/20 flex items-center gap-1 shrink-0">
                            <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span> ONLINE
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 uppercase tracking-widest font-bold">GANTT ▾</p>
                </div>
            </div>
        </div>

        {{-- Fila inferior: tabs (scroll horizontal en mobile) --}}
        <div class="flex items-center gap-1 px-4 pb-2 overflow-x-auto scrollbar-none">

            @foreach([
                ['label' => 'Presupuesto', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z', 'active' => false, 'route' => route('proyectos.presupuesto', $proyecto)],
                ['label' => 'Gantt',       'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'active' => true,  'route' => '#'],
                ['label' => 'Diario',      'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'active' => false, 'route' => route('proyectos.diario', $proyecto)],
                ['label' => 'Bitácora',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'active' => false, 'route' => route('proyectos.bitacora', $proyecto)],
                ['label' => 'Estadísticas','icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'active' => false, 'route' => route('estadisticas', ['proyectoId' => $proyecto->id])],
            ] as $tab)
                <a href="{{ $tab['route'] }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-black uppercase tracking-wider transition-all whitespace-nowrap shrink-0
                       {{ $tab['active'] ? 'bg-gray-100 dark:bg-white text-black' : 'text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                    </svg>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        {{-- Controles: vista y días laborales --}}
        <div class="flex items-center gap-3 px-4 pb-3 flex-wrap">

            {{-- Toggle semanas / días --}}
            <div class="flex items-center rounded-lg overflow-hidden border border-gray-200 dark:border-white/10 shrink-0">
                <button wire:click="$set('vistaGantt', 'semanas')"
                        class="px-3 py-1.5 text-xs font-black uppercase tracking-wider transition-colors
                               {{ $vistaGantt === 'semanas' ? 'bg-gray-100 dark:bg-white/10 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-600 hover:text-gray-700 dark:hover:text-gray-400' }}">
                    Semanas
                </button>
                <button wire:click="$set('vistaGantt', 'dias')"
                        class="px-3 py-1.5 text-xs font-black uppercase tracking-wider transition-colors border-l border-gray-200 dark:border-white/10
                               {{ $vistaGantt === 'dias' ? 'bg-gray-100 dark:bg-white/10 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-600 hover:text-gray-700 dark:hover:text-gray-400' }}">
                    Días
                </button>
            </div>

            {{-- Separador --}}
            <div class="w-px h-5 bg-gray-200 dark:bg-white/10 shrink-0"></div>

            {{-- Label días laborales --}}
            <span class="text-[10px] text-gray-600 font-black uppercase tracking-widest shrink-0">Trabaja:</span>

            {{-- Sábado toggle --}}
            <button wire:click="toggleSabado"
                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border text-xs font-black uppercase tracking-wider transition-all shrink-0
                           {{ $trabajaSabado
                               ? 'bg-orange-500/15 border-orange-500/40 text-orange-400'
                               : 'bg-transparent border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-600 hover:border-gray-300 dark:hover:border-white/20 hover:text-gray-700 dark:hover:text-gray-400' }}">
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($trabajaSabado)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    @endif
                </svg>
                Sáb
            </button>

            {{-- Domingo toggle --}}
            <button wire:click="toggleDomingo"
                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border text-xs font-black uppercase tracking-wider transition-all shrink-0
                           {{ $trabajaDomingo
                               ? 'bg-orange-500/15 border-orange-500/40 text-orange-400'
                               : 'bg-transparent border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-600 hover:border-gray-300 dark:hover:border-white/20 hover:text-gray-700 dark:hover:text-gray-400' }}">
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($trabajaDomingo)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    @endif
                </svg>
                Dom
            </button>

        </div>

    </nav>

    {{-- GANTT --}}
    @php
        $letrasDia   = ['D','L','M','X','J','V','S'];
        $mesesAgrupados  = collect($semanas)->groupBy('mes');
        $totalPx         = count($semanas) * 48;
        $mesesDias       = collect($dias)->groupBy('mes');
        $totalPxDias     = count($dias) * 32;
        $diaInicioRef    = count($dias) ? $dias[0]['fecha'] : $fechaInicioProyecto;
        $totalPxActual   = $vistaGantt === 'dias' ? $totalPxDias : $totalPx;
    @endphp

    @if(count($rubros) === 0)
        <div class="py-32 text-center text-gray-400 dark:text-gray-700 text-sm uppercase font-bold tracking-widest">
            Sin rubros cargados
        </div>
    @else
    <div class="flex overflow-hidden border-t border-gray-200 dark:border-white/5">

        {{-- ── Columna izquierda FIJA: nombres (resizable) ── --}}
        <div id="gantt-left" class="shrink-0 border-r border-gray-200 dark:border-white/5 flex flex-col bg-white dark:bg-[#0d0d0d]"
             style="width:256px; min-width:160px; max-width:640px;">

            {{-- Cabecera meses (placeholder para alinear altura) --}}
            <div class="px-4 py-2 bg-gray-50 dark:bg-white/[0.02] border-b border-gray-200 dark:border-white/5 flex items-center" style="height:33px">
                <span class="text-sm text-gray-500 dark:text-gray-600 font-black uppercase tracking-widest">Rubro</span>
            </div>
            {{-- Cabecera semanas (placeholder) --}}
            <div class="border-b border-gray-200 dark:border-white/5 bg-gray-100/50 dark:bg-black/30" style="height:29px"></div>

            {{-- Filas nombres --}}
            @foreach($rubros as $fila)
                @php $indent = $fila['nivel'] > 0 ? 'pl-8' : ''; @endphp
                <div class="flex items-center justify-between px-4 border-b border-gray-100 dark:border-white/[0.025]
                            {{ $fila['es_categoria'] ? 'bg-gray-50 dark:bg-white/[0.02]' : 'bg-transparent' }}
                            group hover:bg-gray-50 dark:hover:bg-white/[0.04] transition-colors {{ $indent }}"
                     style="height:40px"
                     wire:key="gL-{{ $fila['id'] }}">
                    <div class="flex items-center gap-2 min-w-0">
                        @if($fila['es_categoria'])
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-500 shrink-0"></div>
                        @else
                            <div class="w-1 h-1 rounded-full bg-blue-500/40 shrink-0 ml-1"></div>
                        @endif
                        <div class="min-w-0">
                            <span class="text-base truncate block
                                     {{ $fila['es_categoria'] ? 'text-gray-900 dark:text-white font-black uppercase tracking-wide' : 'text-gray-600 dark:text-gray-400 font-medium' }}">
                                {{ $fila['nombre'] }}
                            </span>
                            @if(!$fila['es_categoria'] && $fila['depends_on_nombre'])
                                <span class="text-[10px] text-orange-400 font-bold truncate block" title="Depende de: {{ $fila['depends_on_nombre'] }}">
                                    → {{ Str::limit($fila['depends_on_nombre'], 22) }}
                                </span>
                            @endif
                            @if(!$fila['es_categoria'] && $fila['horas_totales'] > 0)
                                <span class="text-[10px] text-blue-500 dark:text-blue-400 opacity-70 font-bold truncate block">
                                    {{ number_format($fila['horas_totales'], 1) }} hs MO
                                </span>
                            @endif
                        </div>
                    </div>
                    <button wire:click="abrirModalFechas({{ $fila['id'] }})"
                            title="Asignar fechas"
                            class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity p-1 rounded hover:bg-gray-100 dark:hover:bg-white/10 text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-white">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            @endforeach

        </div>

           {{-- Resizer draggable --}}
           <div id="gantt-resizer" class="w-1 cursor-col-resize bg-transparent hover:bg-gray-200 dark:hover:bg-white/10 touch-none"
               title="Arrastrar para redimensionar" style="user-select:none"></div>

           {{-- ── Área derecha scrollable (UN SOLO contenedor) ── --}}
           <div id="gantt-right" class="flex-1 overflow-x-auto overflow-y-hidden"
                data-vista="{{ $vistaGantt }}"
                data-trabaja-sabado="{{ $trabajaSabado ? '1' : '0' }}"
                data-trabaja-domingo="{{ $trabajaDomingo ? '1' : '0' }}">

            {{-- Cabecera meses --}}
            <div class="flex border-b border-gray-200 dark:border-white/5 bg-white dark:bg-[#0d0d0d]" style="min-width:{{ $totalPxActual }}px; height:33px">
                @if($vistaGantt === 'semanas')
                    @foreach($mesesAgrupados as $mes => $semanasDelMes)
                        <div class="border-r border-gray-200 dark:border-white/5 px-2 flex items-center justify-center shrink-0"
                             style="width:{{ count($semanasDelMes) * 48 }}px">
                            <span class="text-sm text-gray-600 dark:text-gray-500 font-black uppercase tracking-widest">{{ $mes }}</span>
                        </div>
                    @endforeach
                @else
                    @foreach($mesesDias as $mes => $diasDelMes)
                        <div class="border-r border-gray-200 dark:border-white/5 px-2 flex items-center justify-center shrink-0"
                             style="width:{{ count($diasDelMes) * 32 }}px">
                            <span class="text-xs text-gray-600 dark:text-gray-500 font-black uppercase tracking-widest">{{ $mes }}</span>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Cabecera semanas / días --}}
            <div class="flex border-b border-gray-200 dark:border-white/5 bg-gray-100/50 dark:bg-black/30" style="min-width:{{ $totalPxActual }}px; height:29px">
                @if($vistaGantt === 'semanas')
                    @foreach($semanas as $semana)
                        <div class="w-12 shrink-0 border-r border-gray-100 dark:border-white/[0.04] flex items-center justify-center">
                            <span class="text-sm text-gray-500 dark:text-gray-600 font-bold">{{ $semana['label'] }}</span>
                        </div>
                    @endforeach
                @else
                    @foreach($dias as $dia)
                        @php
                            $esSab = $dia['dow'] === 6;
                            $esDom = $dia['dow'] === 0;
                            $esFinDeSemana = $esSab || $esDom;
                            $esNoLaboral = ($esSab && !$trabajaSabado) || ($esDom && !$trabajaDomingo);
                        @endphp
                        <div class="shrink-0 border-r flex flex-col items-center justify-center
                                    {{ $esNoLaboral ? 'bg-gray-100 dark:bg-white/[0.06] border-gray-200 dark:border-white/10' : 'border-gray-100 dark:border-white/[0.04]' }}"
                             style="width:32px">
                            <span class="text-[9px] font-bold leading-none {{ $esNoLaboral ? 'text-gray-400 dark:text-white/30' : ($esFinDeSemana ? 'text-orange-400' : 'text-gray-600 dark:text-gray-300') }}">
                                {{ $letrasDia[$dia['dow']] }}
                            </span>
                            <span class="text-[8px] font-bold leading-none {{ $esNoLaboral ? 'text-gray-300 dark:text-white/20' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $dia['label'] }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Filas barras Gantt --}}
            @php
                // Extraer preview fuera del foreach para evitar problemas de scope en Livewire 3
                $previewId     = $mostrarModalFechas && $editFechaId ? (int)$editFechaId : null;
                $previewInicio = $editFechaInicio ?: null;
                $previewFin    = $editFechaFin    ?: null;
            @endphp
            @foreach($rubros as $fila)
                @php
                    $esEditando  = $previewId !== null && $previewId === (int)$fila['id'];
                    $fechaIniBar = ($esEditando && $previewInicio) ? $previewInicio : $fila['fecha_inicio'];
                    $fechaFinBar = ($esEditando && $previewFin)    ? $previewFin    : $fila['fecha_fin'];

                    $tieneBar = $fechaIniBar && $fechaFinBar;
                    $colorBar = $fila['es_categoria'] ? 'bg-purple-500' : 'bg-[#e85d27]';
                    $barStart   = 0;
                    $barWidth   = 0;

                    if ($tieneBar) {
                        $rubroInicio  = \Carbon\Carbon::parse($fechaIniBar);
                        $rubroFin     = \Carbon\Carbon::parse($fechaFinBar);
                        $duracionDias = $rubroInicio->diffInDays($rubroFin) + 1;

                        if ($vistaGantt === 'semanas') {
                            $proyInicio = \Carbon\Carbon::parse($fechaInicioProyecto)->startOfWeek();
                            $pxPorDia   = 48 / 7;
                            $barStart   = round($proyInicio->diffInDays($rubroInicio) * $pxPorDia);
                            $barWidth   = max(round($duracionDias * $pxPorDia), 14);
                        } else {
                            $refInicio  = \Carbon\Carbon::parse($diaInicioRef);
                            $barStart   = max(0, $refInicio->diffInDays($rubroInicio)) * 32;
                            $barWidth   = max($duracionDias * 32, 18);
                        }
                    }
                @endphp
                <div class="relative border-b border-gray-100 dark:border-white/[0.025]
                            {{ $fila['es_categoria'] ? 'bg-gray-50 dark:bg-white/[0.02]' : 'bg-transparent' }}
                            {{ $esEditando ? 'bg-gray-100/40 dark:bg-white/[0.04]' : '' }}
                            group hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-colors"
                     style="height:40px; min-width:{{ $totalPxActual }}px"
                     wire:key="gR-{{ $fila['id'] }}">

                    {{-- Grid lines / columnas fondo --}}
                    @if($vistaGantt === 'semanas')
                        @foreach($semanas as $i => $s)
                            <div class="absolute top-0 bottom-0 border-r border-gray-100 dark:border-white/[0.04]"
                                 style="left:{{ ($i + 1) * 48 }}px"></div>
                        @endforeach
                    @else
                        @foreach($dias as $i => $dia)
                            @php
                                $esSabFondo = $dia['dow'] === 6;
                                $esDomFondo = $dia['dow'] === 0;
                                $noLabFondo = ($esSabFondo && !$trabajaSabado) || ($esDomFondo && !$trabajaDomingo);
                            @endphp
                            <div class="absolute top-0 bottom-0 border-r {{ $noLabFondo ? 'bg-gray-100/80 dark:bg-white/[0.05] border-gray-200 dark:border-white/10' : 'border-gray-100 dark:border-white/[0.04]' }}"
                                 style="left:{{ $i * 32 }}px; width:32px"></div>
                        @endforeach
                    @endif

                    @if($tieneBar)
                        <div class="gantt-bar absolute top-1/2 -translate-y-1/2 h-[18px] rounded-full {{ $colorBar }}
                                    flex items-center px-2 cursor-grab transition-opacity shadow-md select-none
                                    {{ $esEditando ? 'opacity-100 ring-2 ring-white/40' : 'opacity-85 hover:opacity-100' }}
                                    {{ $fila['depends_on_id'] ? 'ring-1 ring-white/30' : '' }}"
                             style="left:{{ $barStart }}px; width:{{ $barWidth }}px"
                             data-bar-id="{{ $fila['id'] }}"
                             data-fecha-inicio="{{ $fechaIniBar }}"
                             data-fecha-fin="{{ $fechaFinBar }}"
                             title="{{ $fila['nombre'] }}{{ $fila['horas_totales'] > 0 ? ' · ' . number_format($fila['horas_totales'], 1) . ' hs MO' : '' }}">
                            @if($fila['depends_on_id'])
                                <svg class="w-2.5 h-2.5 text-white/70 shrink-0 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            @endif
                            @if($barWidth > 44)
                                <span class="text-sm font-black text-white truncate whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($fechaIniBar)->format('d/m') }}–{{ \Carbon\Carbon::parse($fechaFinBar)->format('d/m') }}
                                    @if(!$fila['es_categoria'] && $fila['horas_totales'] > 0)
                                        &nbsp;<span class="text-white/60 font-bold">{{ number_format($fila['horas_totales'], 1) }}h</span>
                                    @endif
                                </span>
                            @endif
                        </div>
                    @else
                        <button wire:click="abrirModalFechas({{ $fila['id'] }})"
                                class="absolute left-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100
                                       transition-opacity text-sm text-gray-400 dark:text-gray-600 hover:text-gray-600 dark:hover:text-gray-400 font-bold uppercase">
                            + Asignar fechas
                        </button>
                    @endif

                </div>
            @endforeach

        </div>
    </div>
    @endif

<script>
    (function(){
        const left = document.getElementById('gantt-left');
        const resizer = document.getElementById('gantt-resizer');
        if (!left || !resizer) return;

        const MIN = 160; // px
        const MAX = 640; // px
        const DEFAULT = 256; // px
        const storageKey = 'gantt-left-width-{{ $proyecto->id }}';

        function setWidth(w){
            const width = Math.min(MAX, Math.max(MIN, Math.round(w)));
            left.style.width = width + 'px';
            try { localStorage.setItem(storageKey, width); } catch(e) {}
        }

        // load stored width (run immediately)
        try {
            const stored = parseInt(localStorage.getItem(storageKey));
            if (!isNaN(stored)) setWidth(stored);
            else setWidth(DEFAULT);
        } catch(e) {
            setWidth(DEFAULT);
        }

        let dragging = false;
        let startX = 0;
        let startWidth = 0;

        function onPointerDown(e){
            e.preventDefault();
            dragging = true;
            startX = (e.clientX !== undefined) ? e.clientX : (e.touches && e.touches[0].clientX);
            startWidth = left.getBoundingClientRect().width;
            document.documentElement.style.cursor = 'col-resize';
        }

        function onPointerMove(e){
            if (!dragging) return;
            const clientX = (e.clientX !== undefined) ? e.clientX : (e.touches && e.touches[0].clientX);
            const dx = clientX - startX;
            setWidth(startWidth + dx);
        }

        function stopDragging(){
            if (!dragging) return;
            dragging = false;
            document.documentElement.style.cursor = '';
        }

        resizer.addEventListener('mousedown', onPointerDown);
        document.addEventListener('mousemove', onPointerMove);
        document.addEventListener('mouseup', stopDragging);

        // touch support
        resizer.addEventListener('touchstart', onPointerDown, {passive:false});
        document.addEventListener('touchmove', onPointerMove, {passive:false});
        document.addEventListener('touchend', stopDragging);

        // double click to reset
        resizer.addEventListener('dblclick', function(){ setWidth(DEFAULT); });
    })();
</script>

<script>
(function(){
    const THRESHOLD = 5; // px mínimos para considerar drag
    let drag = null;

    function pxPerDay(vista) {
        return vista === 'dias' ? 32 : (48 / 7);
    }

    function addDays(dateStr, n) {
        // Use noon to avoid DST edge cases
        const d = new Date(dateStr + 'T12:00:00');
        d.setDate(d.getDate() + n);
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + day;
    }

    function fmtDate(d) {
        const parts = d.split('-');
        return parts[2] + '/' + parts[1];
    }

    function getTip() {
        let tip = document.getElementById('gantt-drag-tip');
        if (!tip) {
            tip = document.createElement('div');
            tip.id = 'gantt-drag-tip';
            tip.className = 'fixed z-[9999] bg-gray-900 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg pointer-events-none shadow-xl border border-white/20 select-none';
            tip.style.display = 'none';
            document.body.appendChild(tip);
        }
        return tip;
    }

    function getWireComponent(el) {
        let cur = el;
        while (cur && cur !== document.body) {
            if (cur.hasAttribute('wire:id')) {
                return window.Livewire ? window.Livewire.find(cur.getAttribute('wire:id')) : null;
            }
            cur = cur.parentElement;
        }
        return null;
    }

    document.addEventListener('mousedown', function(e) {
        const bar = e.target.closest('.gantt-bar');
        if (!bar) return;

        // Only left mouse button
        if (e.button !== 0) return;

        const rightEl = bar.closest('#gantt-right');
        if (!rightEl) return;

        e.preventDefault();

        drag = {
            bar,
            rightEl,
            startX: e.clientX,
            originalLeft: parseFloat(bar.style.left) || 0,
            fechaInicio: bar.dataset.fechaInicio,
            fechaFin: bar.dataset.fechaFin,
            barId: parseInt(bar.dataset.barId),
            moved: false,
            lastDelta: 0,
        };
    }, true);

    document.addEventListener('mousemove', function(e) {
        if (!drag) return;

        const dx = e.clientX - drag.startX;
        if (!drag.moved && Math.abs(dx) < THRESHOLD) return;
        drag.moved = true;

        const vista = drag.rightEl.dataset.vista || 'semanas';
        const ppd = pxPerDay(vista);
        const dayDelta = Math.round(dx / ppd);
        drag.lastDelta = dayDelta;

        const newLeft = drag.originalLeft + dayDelta * ppd;
        drag.bar.style.left = Math.max(0, newLeft) + 'px';
        drag.bar.style.cursor = 'grabbing';
        drag.bar.style.opacity = '1';
        drag.bar.style.zIndex = '10';

        const newInicio = addDays(drag.fechaInicio, dayDelta);
        const newFin    = addDays(drag.fechaFin, dayDelta);

        const tip = getTip();
        tip.textContent = fmtDate(newInicio) + ' – ' + fmtDate(newFin);
        tip.style.display = 'block';
        tip.style.left = (e.clientX + 14) + 'px';
        tip.style.top  = (e.clientY - 34) + 'px';
    });

    document.addEventListener('mouseup', function(e) {
        if (!drag) return;

        const { bar, moved, barId, originalLeft, fechaInicio, fechaFin, rightEl, lastDelta } = drag;
        drag = null;

        // Hide tooltip
        const tip = document.getElementById('gantt-drag-tip');
        if (tip) tip.style.display = 'none';

        bar.style.cursor = '';
        bar.style.zIndex = '';

        if (!moved) {
            // Single click → open modal
            const comp = getWireComponent(bar);
            if (comp) comp.call('abrirModalFechas', barId);
            return;
        }

        if (lastDelta === 0) {
            // Dragged but returned to original position
            bar.style.left = originalLeft + 'px';
            bar.style.opacity = '';
            return;
        }

        const newInicio = addDays(fechaInicio, lastDelta);
        const newFin    = addDays(fechaFin, lastDelta);

        // Visual feedback while saving
        bar.style.opacity = '0.45';

        const comp = getWireComponent(bar);
        if (comp) {
            comp.call('moverBarra', barId, newInicio, newFin);
        } else {
            // Fallback: restore
            bar.style.left = originalLeft + 'px';
            bar.style.opacity = '';
        }
    });

    // Cancelar drag si se pierde el foco de la ventana
    window.addEventListener('blur', function() {
        if (!drag) return;
        const { bar, originalLeft } = drag;
        drag = null;
        bar.style.left = originalLeft + 'px';
        bar.style.cursor = '';
        bar.style.opacity = '';
        bar.style.zIndex = '';
        const tip = document.getElementById('gantt-drag-tip');
        if (tip) tip.style.display = 'none';
    });
})();
</script>

{{-- MODAL FECHAS --}}
@if($mostrarModalFechas)
    <div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/60 dark:bg-black/80 backdrop-blur-md px-4">
        <div class="w-full max-w-sm border border-gray-200 dark:border-white/10 rounded-2xl p-6 space-y-5 bg-white dark:bg-[#0d0d0d] shadow-2xl">
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-600 uppercase font-black mb-1">Asignar fechas</p>
                <h2 class="text-gray-900 dark:text-white font-extrabold text-sm uppercase truncate">{{ $editNombre }}</h2>
                @if($proyecto->fecha_inicio)
                    <p class="text-sm text-gray-500 dark:text-gray-600 mt-1">
                        Proyecto desde {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}
                    </p>
                @endif
            </div>

            <div class="space-y-3">
                @php
                    $filaActual = collect($rubros)->firstWhere('id', $editFechaId);
                    $tieneDepend = $filaActual && count($filaActual['dependientes'] ?? []) > 0;
                    $diasCalculados = $editHorasTotales > 0 ? (int) ceil($editHorasTotales / 8) : 0;
                @endphp

                {{-- Badge horas MO --}}
                @if($editHorasTotales > 0)
                    <div class="flex items-center gap-2 bg-blue-500/10 border border-blue-500/20 rounded-xl px-3 py-2">
                        <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-blue-300 font-bold">
                            {{ number_format($editHorasTotales, 1) }} hs MO&nbsp;&nbsp;→&nbsp;&nbsp;<span class="text-blue-200">{{ $diasCalculados }} días laborables</span>
                            <span class="text-blue-400/60 font-normal ml-1">(8 hs/día{{ $trabajaSabado || $trabajaDomingo ? ', con ' . ($trabajaSabado ? 'Sáb' : '') . ($trabajaSabado && $trabajaDomingo ? '+' : '') . ($trabajaDomingo ? 'Dom' : '') : '' }})</span>
                        </p>
                    </div>
                @endif

                @if($tieneDepend)
                    <div class="flex items-start gap-2 bg-orange-500/10 border border-orange-500/20 rounded-xl px-3 py-2">
                        <svg class="w-4 h-4 text-orange-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        <p class="text-xs text-orange-300 font-bold">
                            Al guardar se moverá el inicio de: <span class="text-orange-200">{{ implode(', ', $filaActual['dependientes']) }}</span>
                        </p>
                    </div>
                @endif
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-500 uppercase font-black">Fecha Inicio</label>
                    <input type="date" wire:model.live="editFechaInicio"
                        wire:key="fechainicio-{{ (int)$trabajaSabado }}-{{ (int)$trabajaDomingo }}"
                        min="{{ $proyecto->fecha_inicio?->format('Y-m-d') }}"
                        class="w-full mt-1 p-3 rounded-xl bg-gray-100 dark:bg-[#0f1115] text-gray-900 dark:text-white border border-gray-200 dark:border-white/10 text-sm focus:border-purple-500/50 outline-none">
                    @error('editFechaInicio')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-500 uppercase font-black flex items-center gap-2">
                        Fecha Fin
                        @if($editHorasTotales > 0)
                            <span class="text-[10px] text-blue-500 dark:text-blue-400 opacity-70 font-bold normal-case">Auto-calculada · editable</span>
                        @endif
                    </label>
                    <input type="date" wire:model.live="editFechaFin"
                        min="{{ $proyecto->fecha_inicio?->format('Y-m-d') }}"
                        class="w-full mt-1 p-3 rounded-xl bg-gray-100 dark:bg-[#0f1115] text-gray-900 dark:text-white border text-sm focus:border-purple-500/50 outline-none
                               {{ $editHorasTotales > 0 ? 'border-blue-500/30' : 'border-gray-200 dark:border-white/10' }}">
                    @if($editDiasLaborables > 0)
                        <p class="text-xs text-blue-500/70 dark:text-blue-400/60 mt-1 font-bold">
                            {{ $editDiasLaborables }} días laborables
                            @if($editHorasTotales > 0)
                                · {{ number_format($editDiasLaborables * 8, 0) }} hs disponibles
                                @if($editDiasLaborables * 8 < $editHorasTotales)
                                    <span class="text-red-400">⚠ faltan {{ number_format($editHorasTotales - $editDiasLaborables * 8, 1) }} hs</span>
                                @endif
                            @endif
                        </p>
                    @endif
                    @error('editFechaFin')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button wire:click="$set('mostrarModalFechas', false)"
                    class="w-1/3 py-3 rounded-xl border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white text-sm font-bold hover:bg-gray-50 dark:hover:bg-white/5 transition-all">
                    CANCELAR
                </button>
                @if($editFechaInicio || $editFechaFin)
                    <button wire:click="eliminarFechas"
                        class="w-1/3 py-3 rounded-xl border border-red-500/30 text-red-400 text-sm font-black hover:bg-red-500/10 transition-all">
                        ELIMINAR
                    </button>
                @endif
                <button wire:click="guardarFechas"
                    class="{{ ($editFechaInicio || $editFechaFin) ? 'w-1/3' : 'w-1/2' }} bg-gray-900 dark:bg-white text-white dark:text-black py-3 rounded-xl font-black text-sm hover:bg-gray-800 dark:hover:bg-gray-100 transition-all">
                    GUARDAR
                </button>
            </div>
        </div>
    </div>
@endif

</div>
