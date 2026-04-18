<div class="min-h-screen bg-white dark:bg-[#0a0a0a] text-black dark:text-white">
    {{-- NAVBAR --}}
    <nav class="border-b border-gray-200 dark:border-white/5 bg-white dark:bg-[#0d0d0d]">

        {{-- Fila superior: back + nombre + exportar --}}
        <div class="flex items-center justify-between px-3 py-1.5 gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a wire:navigate href="{{ route('recursos.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="font-black text-xs uppercase tracking-widest truncate text-black dark:text-white">{{ $proyecto->nombre_proyecto }}</h1>
                        <span class="bg-green-500/10 text-green-500 text-[10px] font-black px-1.5 py-0 rounded border border-green-500/20 flex items-center gap-1 shrink-0">
                            <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span> ONLINE
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-600 dark:text-gray-400 uppercase tracking-widest font-bold">BITÁCORA ▾</p>
                </div>
            </div>
            {{-- Exportar PDF --}}
            <button
                @click="$wire.exportarPDF(document.documentElement.classList.contains('dark'))"
                wire:loading.attr="disabled"
                class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white hover:bg-gray-200 dark:hover:bg-white/10 text-sm font-black uppercase tracking-wider transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span wire:loading.remove>PDF</span>
                <span wire:loading>...</span>
            </button>
        </div>

        {{-- Fila inferior: tabs (scroll horizontal en mobile) --}}
        <div class="flex items-center gap-0.5 px-3 pb-1 overflow-x-auto scrollbar-none">
            @foreach([
                ['label' => 'Presupuesto', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z', 'active' => false, 'route' => route('proyectos.presupuesto', $proyecto)],
                ['label' => 'Gantt',        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'active' => false, 'route' => route('proyectos.gantt', $proyecto)],
                ['label' => 'Diario',       'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'active' => false, 'route' => route('proyectos.diario', $proyecto)],
                ['label' => 'Bitácora',     'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'active' => true, 'route' => '#'],
                ['label' => 'Estadísticas','icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'active' => false, 'route' => route('estadisticas', ['proyectoId' => $proyecto->id])],
            ] as $tab)
                @if(($tab['label'] === 'Gantt' && !auth()->user()->puedeCompartido('computos'))
                    || ($tab['label'] === 'Gantt' && !in_array($proyecto->estado_obra, ['ejecucion', 'pausado', 'finalizado']))
                    || ($tab['label'] === 'Diario' && !auth()->user()->puedeCompartido('reporte_diario'))
                    || ($tab['label'] === 'Diario' && !in_array($proyecto->estado_obra, ['ejecucion', 'pausado', 'finalizado']))
                    || ($tab['label'] === 'Bitácora' && !auth()->user()->puedeCompartido('bitacora'))
                    || ($tab['label'] === 'Estadísticas' && !auth()->user()->puedeCompartido('estadisticas')))
                    @continue
                @endif
                <a href="{{ $tab['route'] }}"
                   class="flex items-center gap-1 px-2 py-1 rounded text-[11px] font-black uppercase tracking-wider transition-all whitespace-nowrap shrink-0
                       {{ $tab['active'] ? 'bg-gray-200 dark:bg-white text-black dark:text-black' : 'text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                    </svg>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

    </nav>

    {{-- CUERPO DE BITÁCORA --}}
    <div class="p-8 bg-white dark:bg-[#0a0a0a] min-h-screen text-black dark:text-white">
        <div class="max-w-5xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-black uppercase tracking-tighter italic">Bitácora de Obra</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm uppercase font-bold tracking-widest">Historial de movimientos del proyecto</p>
                </div>
            </div>

            {{-- BARRA DE FILTROS --}}
            <div class="bg-gray-100 dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-4 mb-10 flex flex-wrap items-center gap-6">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs text-gray-500 dark:text-gray-400 uppercase font-black ml-1 tracking-widest">Fecha del reporte</label>
                    <input type="date" wire:model.live="searchFecha" 
                        class="bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-black dark:text-white px-4 py-2 focus:border-blue-500 outline-none transition w-44">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs text-gray-500 dark:text-gray-400 uppercase font-black ml-1 tracking-widest">Filtrar por rubro</label>
                    <select wire:model.live="searchRubro" 
                        class="bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-black dark:text-white px-4 py-2 focus:border-blue-500 outline-none transition w-64">
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

            {{-- LINEA DE TIEMPO --}}
            <div class="relative border-l border-gray-200 dark:border-white/10 ml-4 space-y-12 pb-20">
                @forelse($registros as $registro)
                    <div class="relative pl-12 group">

                        <div class="absolute -left-[7px] top-6 w-3.5 h-3.5 bg-[#00ff88] rounded-full border-[3px] border-white dark:border-[#0a0a0a] shadow-[0_0_15px_rgba(0,255,136,0.3)] group-hover:scale-125 transition-transform"></div>

                        <div class="bg-gray-100 dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-3xl p-6 shadow-2xl hover:border-gray-300 dark:hover:border-white/10 transition-colors">
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-200 dark:bg-white/5 flex items-center justify-center border border-gray-300 dark:border-white/10 shadow-inner">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm text-black dark:text-white">
                                            {{ $registro->user?->name ?? 'Residente de Obra' }}
                                        </h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-black tracking-widest">
                                            @if($registro->user?->role)
                                                <span class="text-[#e85d27]">{{ ucfirst($registro->user->role) }}</span> ·
                                            @endif
                                            Reporte: <span class="text-blue-400">{{ $registro->recurso?->nombre ?? 'Sin recurso' }}</span>
                                        </p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 font-mono bg-gray-200/40 dark:bg-black/40 px-3 py-1 rounded-full border border-gray-300 dark:border-white/5">
                                    {{ $registro->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="md:col-span-2">
                                    <p class="text-gray-600 dark:text-gray-400 text-xs italic leading-relaxed">
                                        "{{ $registro->notas ?? 'Sin comentarios adicionales.' }}"
                                    </p>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <span class="text-sm bg-gray-200 dark:bg-white/5 px-3 py-1 rounded-full text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-white/5">
                                            Avance: <strong class="text-black dark:text-[#00ff88]">{{ $registro->avance_fisico }}%</strong>
                                        </span>
                                        <span class="text-sm bg-gray-200 dark:bg-white/5 px-3 py-1 rounded-full text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-white/5">
                                            {{ $registro->cantidad_hoy }} (M2)
                                        </span>
                                        <span class="text-sm bg-gray-200 dark:bg-white/5 px-3 py-1 rounded-full text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-white/5">
                                            ${{ number_format($registro->costo_hoy, 2) }} 
                                        </span>
                                    </div>
                                </div>
                                
                                @if($registro->foto_path)
                                    <div x-data="{ open: false }" class="relative">
                                        <img 
                                            src="{{ asset('storage/' . $registro->foto_path) }}" 
                                            @click="open = true"
                                            class="w-full h-24 object-cover rounded-2xl border border-gray-300 dark:border-white/10 cursor-zoom-in hover:brightness-110 transition-all"
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
                        <p class="text-gray-600 dark:text-gray-400 text-xs uppercase font-black tracking-widest italic">No se encontraron registros con los filtros aplicados.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
