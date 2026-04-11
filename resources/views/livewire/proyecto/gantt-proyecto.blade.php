<div class="min-h-screen bg-[#0a0a0a]">

    {{-- NAVBAR --}}
    <nav class="border-b border-white/5 bg-[#0d0d0d]">

        {{-- Fila superior: back + nombre --}}
        <div class="flex items-center justify-between px-4 py-3 gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a wire:navigate href="{{ route('recursos.index') }}" class="text-gray-500 hover:text-white transition-colors shrink-0">
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
                       {{ $tab['active'] ? 'bg-white text-black' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                    </svg>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

    </nav>

    {{-- GANTT --}}
    @php $mesesAgrupados = collect($semanas)->groupBy('mes'); $totalPx = count($semanas) * 48; @endphp

    @if(count($rubros) === 0)
        <div class="py-32 text-center text-gray-700 text-sm uppercase font-bold tracking-widest">
            Sin rubros cargados
        </div>
    @else
    <div class="flex overflow-hidden border-t border-white/5">

        {{-- ── Columna izquierda FIJA: nombres (resizable) ── --}}
        <div id="gantt-left" class="shrink-0 border-r border-white/5 flex flex-col bg-[#0d0d0d]"
             style="width:256px; min-width:160px; max-width:640px;">

            {{-- Cabecera meses (placeholder para alinear altura) --}}
            <div class="px-4 py-2 bg-white/[0.02] border-b border-white/5 flex items-center" style="height:33px">
                <span class="text-sm text-gray-600 font-black uppercase tracking-widest">Rubro</span>
            </div>
            {{-- Cabecera semanas (placeholder) --}}
            <div class="border-b border-white/5 bg-black/30" style="height:29px"></div>

            {{-- Filas nombres --}}
            @foreach($rubros as $fila)
                @php $indent = $fila['nivel'] > 0 ? 'pl-8' : ''; @endphp
                <div class="flex items-center justify-between px-4 border-b border-white/[0.025]
                            {{ $fila['es_categoria'] ? 'bg-white/[0.02]' : 'bg-transparent' }}
                            group hover:bg-white/[0.04] transition-colors {{ $indent }}"
                     style="height:40px"
                     wire:key="gL-{{ $fila['id'] }}">
                    <div class="flex items-center gap-2 min-w-0">
                        @if($fila['es_categoria'])
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-500 shrink-0"></div>
                        @else
                            <div class="w-1 h-1 rounded-full bg-blue-500/40 shrink-0 ml-1"></div>
                        @endif
                        <span class="text-base truncate
                                     {{ $fila['es_categoria'] ? 'text-white font-black uppercase tracking-wide' : 'text-gray-400 font-medium' }}">
                            {{ $fila['nombre'] }}
                        </span>
                    </div>
                    <button wire:click="abrirModalFechas({{ $fila['id'] }})"
                            title="Asignar fechas"
                            class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity p-1 rounded hover:bg-white/10 text-gray-500 hover:text-white">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            @endforeach

        </div>

           {{-- Resizer draggable --}}
           <div id="gantt-resizer" class="w-1 cursor-col-resize bg-transparent hover:bg-white/10 touch-none"
               title="Arrastrar para redimensionar" style="user-select:none"></div>

           {{-- ── Área derecha scrollable (UN SOLO contenedor) ── --}}
           <div id="gantt-right" class="flex-1 overflow-x-auto overflow-y-hidden">

            {{-- Cabecera meses --}}
            <div class="flex border-b border-white/5 bg-[#0d0d0d]" style="min-width:{{ $totalPx }}px; height:33px">
                @foreach($mesesAgrupados as $mes => $semanasDelMes)
                    <div class="border-r border-white/5 px-2 flex items-center justify-center shrink-0"
                         style="width:{{ count($semanasDelMes) * 48 }}px">
                        <span class="text-sm text-gray-500 font-black uppercase tracking-widest">{{ $mes }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Cabecera semanas --}}
            <div class="flex border-b border-white/5 bg-black/30" style="min-width:{{ $totalPx }}px; height:29px">
                @foreach($semanas as $semana)
                    <div class="w-12 shrink-0 border-r border-white/[0.04] flex items-center justify-center">
                        <span class="text-sm text-gray-600 font-bold">{{ $semana['label'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Filas barras Gantt --}}
            @foreach($rubros as $fila)
                @php
                    $tieneBar   = $fila['fecha_inicio'] && $fila['fecha_fin'];
                    $colorBar   = $fila['es_categoria'] ? 'bg-purple-500' : 'bg-[#e85d27]';
                    $barStart   = 0;
                    $barWidth   = 0;

                    if ($tieneBar) {
                        $proyInicio   = \Carbon\Carbon::parse($fechaInicioProyecto)->startOfWeek();
                        $rubroInicio  = \Carbon\Carbon::parse($fila['fecha_inicio']);
                        $rubroFin     = \Carbon\Carbon::parse($fila['fecha_fin']);
                        $offsetDias   = $proyInicio->diffInDays($rubroInicio);
                        $duracionDias = $rubroInicio->diffInDays($rubroFin) + 1;
                        $pxPorDia     = 48 / 7;
                        $barStart     = round($offsetDias * $pxPorDia);
                        $barWidth     = max(round($duracionDias * $pxPorDia), 14);
                    }
                @endphp
                <div class="relative border-b border-white/[0.025]
                            {{ $fila['es_categoria'] ? 'bg-white/[0.02]' : 'bg-transparent' }}
                            group hover:bg-white/[0.03] transition-colors"
                     style="height:40px; min-width:{{ $totalPx }}px"
                     wire:key="gR-{{ $fila['id'] }}">

                    {{-- Grid lines semanas --}}
                    @foreach($semanas as $i => $s)
                        <div class="absolute top-0 bottom-0 border-r border-white/[0.04]"
                             style="left:{{ ($i + 1) * 48 }}px"></div>
                    @endforeach

                    @if($tieneBar)
                        <div class="absolute top-1/2 -translate-y-1/2 h-[18px] rounded-full {{ $colorBar }} opacity-85
                                    flex items-center px-2 cursor-pointer hover:opacity-100 transition-opacity shadow-md"
                             style="left:{{ $barStart }}px; width:{{ $barWidth }}px"
                             wire:click="abrirModalFechas({{ $fila['id'] }})">
                            @if($barWidth > 44)
                                <span class="text-sm font-black text-white truncate whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($fila['fecha_inicio'])->format('d/m') }}–{{ \Carbon\Carbon::parse($fila['fecha_fin'])->format('d/m') }}
                                </span>
                            @endif
                        </div>
                    @else
                        <button wire:click="abrirModalFechas({{ $fila['id'] }})"
                                class="absolute left-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100
                                       transition-opacity text-sm text-gray-600 hover:text-gray-400 font-bold uppercase">
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

{{-- MODAL FECHAS --}}
@if($mostrarModalFechas)
    <div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">
        <div class="w-full max-w-sm border border-white/10 rounded-2xl p-6 space-y-5 bg-[#0d0d0d] shadow-2xl">
            <div class="text-center">
                <p class="text-sm text-gray-600 uppercase font-black mb-1">Asignar fechas</p>
                <h2 class="text-white font-extrabold text-sm uppercase truncate">{{ $editNombre }}</h2>
                @if($proyecto->fecha_inicio)
                    <p class="text-sm text-gray-600 mt-1">
                        Proyecto desde {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}
                    </p>
                @endif
            </div>

            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500 uppercase font-black">Fecha Inicio</label>
                    <input type="date" wire:model="editFechaInicio"
                        min="{{ $proyecto->fecha_inicio?->format('Y-m-d') }}"
                        class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 text-sm focus:border-purple-500/50 outline-none">
                    @error('editFechaInicio')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-sm text-gray-500 uppercase font-black">Fecha Fin</label>
                    <input type="date" wire:model="editFechaFin"
                        min="{{ $proyecto->fecha_inicio?->format('Y-m-d') }}"
                        class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 text-sm focus:border-purple-500/50 outline-none">
                    @error('editFechaFin')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button wire:click="$set('mostrarModalFechas', false)"
                    class="w-1/2 py-3 rounded-xl border border-white/10 text-white text-sm font-bold hover:bg-white/5 transition-all">
                    CANCELAR
                </button>
                <button wire:click="guardarFechas"
                    class="w-1/2 bg-white text-black py-3 rounded-xl font-black text-sm hover:bg-gray-100 transition-all">
                    GUARDAR
                </button>
            </div>
        </div>
    </div>
@endif

</div>
