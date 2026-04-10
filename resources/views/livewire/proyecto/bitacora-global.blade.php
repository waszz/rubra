<div class="min-h-screen bg-[#0a0a0a]">

    {{-- NAVBAR solo en modo proyecto --}}
    @if($modoProyecto && $proyecto)
    <nav class="flex items-center justify-between px-6 py-3 border-b border-white/5 bg-[#0d0d0d]">
        <div class="flex items-center gap-4">
            <a href="{{ route('proyectos.presupuesto', $proyecto) }}" class="text-gray-500 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-white font-black text-sm uppercase tracking-widest">{{ $proyecto->nombre_proyecto }}</h1>
                    <span class="bg-green-500/10 text-green-500 text-sm font-black px-2 py-0.5 rounded border border-green-500/20 flex items-center gap-1">
                        <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span> ONLINE
                    </span>
                </div>
                <p class="text-sm text-gray-600 uppercase tracking-widest font-bold">BITÁCORA ▾</p>
            </div>
        </div>

        <div class="flex items-center gap-1">
            @foreach([
                ['label' => 'Presupuesto', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z', 'active' => false, 'route' => route('proyectos.presupuesto', $proyecto)],
                ['label' => 'Gantt',       'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'active' => false, 'route' => route('proyectos.gantt', $proyecto)],
                ['label' => 'Diario',      'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'active' => false, 'route' => route('proyectos.diario', $proyecto)],
                ['label' => 'Bitácora',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'active' => true, 'route' => '#'],
                ['label' => 'Estadísticas','icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'active' => false, 'route' => route('estadisticas', ['proyectoId' => $proyecto->id])],
            ] as $tab)
                @if(($tab['label'] === 'Gantt' && !auth()->user()->puede('mapa')) || ($tab['label'] === 'Estadísticas' && !auth()->user()->puede('estadisticas')))
                    @continue
                @endif
                <a href="{{ $tab['route'] }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-black uppercase tracking-wider transition-all
                       {{ $tab['active'] ? 'bg-white text-black' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                    </svg>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        <div class="w-48 flex justify-end"></div>
    </nav>
    @endif

    <div class="p-8 min-h-screen text-white">
        <div class="max-w-5xl mx-auto">

            {{-- HEADER --}}
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-black uppercase tracking-tighter italic">Bitácora de Obra</h1>
                    <p class="text-gray-500 text-sm uppercase font-bold tracking-widest">Historial de movimientos del proyecto</p>
                </div>
            </div>

            {{-- FILTROS --}}
            <div class="bg-[#111] border border-white/5 rounded-2xl p-4 mb-10 flex flex-wrap items-center gap-6">

                {{-- Selector de proyecto (solo modo global) --}}
                @if(!$modoProyecto)
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs text-gray-500 uppercase font-black ml-1 tracking-widest">Proyecto</label>
                    <select wire:model.live="proyectoId"
                        class="bg-[#0a0a0a] border border-white/10 rounded-xl text-xs text-white px-4 py-2 focus:border-blue-500 outline-none transition w-64">
                        @foreach($proyectos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre_proyecto }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs text-gray-500 uppercase font-black ml-1 tracking-widest">Fecha del reporte</label>
                    <input type="date" wire:model.live="searchFecha"
                        class="bg-[#0a0a0a] border border-white/10 rounded-xl text-xs text-white px-4 py-2 focus:border-blue-500 outline-none transition w-44">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs text-gray-500 uppercase font-black ml-1 tracking-widest">Filtrar por rubro</label>
                    <select wire:model.live="searchRubro"
                        class="bg-[#0a0a0a] border border-white/10 rounded-xl text-xs text-white px-4 py-2 focus:border-blue-500 outline-none transition w-64">
                        <option value="">Todos los rubros</option>
                        @foreach($rubrosDisponibles as $rubro)
                            <option value="{{ $rubro->id }}">{{ $rubro->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                @if($searchFecha || $searchRubro)
                    <button wire:click="limpiarFiltros"
                        class="mt-5 text-sm text-red-500/80 uppercase font-black hover:text-red-400 transition flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Limpiar
                    </button>
                @endif
            </div>

            {{-- LÍNEA DE TIEMPO --}}
            @if(!$proyecto)
                <div class="flex items-center justify-center h-64 border-2 border-dashed border-gray-800 rounded-3xl">
                    <p class="text-gray-600 text-base font-bold uppercase tracking-widest">Seleccioná un proyecto para ver su bitácora</p>
                </div>
            @else
            <div class="relative border-l border-white/10 ml-4 space-y-12 pb-20">
                @forelse($registros as $registro)
                    <div class="relative pl-12 group">
                        <div class="absolute -left-[7px] top-6 w-3.5 h-3.5 bg-[#00ff88] rounded-full border-[3px] border-[#0a0a0a] shadow-[0_0_15px_rgba(0,255,136,0.3)] group-hover:scale-125 transition-transform"></div>

                        <div class="bg-[#111] border border-white/5 rounded-3xl p-6 shadow-2xl hover:border-white/10 transition-colors">
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center border border-white/10 shadow-inner">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm">Residente de Obra</h4>
                                        <p class="text-xs text-gray-500 uppercase font-black tracking-widest">
                                            Reporte: <span class="text-blue-400">{{ $registro->recurso->nombre }}</span>
                                        </p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600 font-mono bg-black/40 px-3 py-1 rounded-full border border-white/5">
                                    {{ $registro->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="md:col-span-2">
                                    <p class="text-gray-400 text-xs italic leading-relaxed">
                                        "{{ $registro->notas ?? 'Sin comentarios adicionales.' }}"
                                    </p>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <span class="text-sm bg-white/5 px-3 py-1 rounded-full text-gray-300 border border-white/5">
                                            Avance: <strong class="text-[#00ff88]">{{ $registro->avance_fisico }}%</strong>
                                        </span>
                                        <span class="text-sm bg-white/5 px-3 py-1 rounded-full text-gray-400 border border-white/5">
                                            {{ $registro->cantidad_hoy }} (M2)
                                        </span>
                                        <span class="text-sm bg-white/5 px-3 py-1 rounded-full text-gray-400 border border-white/5">
                                            ${{ number_format($registro->costo_hoy, 2) }}
                                        </span>
                                    </div>
                                </div>

                                @if($registro->foto_path)
                                    <div x-data="{ open: false }" class="relative">
                                        <img
                                            src="{{ asset('storage/' . $registro->foto_path) }}"
                                            @click="open = true"
                                            class="w-full h-24 object-cover rounded-2xl border border-white/10 cursor-zoom-in hover:brightness-110 transition-all"
                                        >
                                        <template x-if="open">
                                            <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-6" @click="open = false" @keydown.escape.window="open = false">
                                                <img src="{{ asset('storage/' . $registro->foto_path) }}" class="max-w-full max-h-full rounded-lg shadow-2xl">
                                            </div>
                                        </template>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="pl-12 py-10">
                        <p class="text-gray-600 text-xs uppercase font-black tracking-widest italic">No se encontraron registros con los filtros aplicados.</p>
                    </div>
                @endforelse
            </div>
            @endif

        </div>
    </div>
</div>
