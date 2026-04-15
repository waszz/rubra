<div class="min-h-screen bg-white dark:bg-[#0a0a0a] text-black dark:text-white">

    {{-- NAVBAR --}}
    <nav class="border-b border-gray-200 dark:border-white/5 bg-white dark:bg-[#0d0d0d]">

        {{-- Fila superior: back + nombre + selector fecha --}}
        <div class="flex items-center justify-between px-3 py-1.5 gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a wire:navigate href="{{ route('recursos.index') }}" class="text-gray-500 hover:text-white transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="font-black text-xs uppercase tracking-widest truncate text-black dark:text-white">{{ $proyecto->nombre_proyecto }}</h1>
                        <span class="bg-green-500/10 text-green-700 dark:text-green-500 text-[10px] font-black px-1.5 py-0 rounded border border-green-500/20 flex items-center gap-1 shrink-0">
                            <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span> ONLINE
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-700 dark:text-gray-400 uppercase tracking-widest font-bold">DIARIO DE OBRA ▾</p>
                </div>
            </div>
        </div>

        {{-- Fila inferior: tabs (scroll horizontal en mobile) --}}
        <div class="flex items-center gap-0.5 px-3 pb-1 overflow-x-auto scrollbar-none">
            @foreach([
                ['label' => 'Presupuesto', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z', 'active' => false, 'route' => route('proyectos.presupuesto', $proyecto)],
                ['label' => 'Gantt',        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'active' => false, 'route' => route('proyectos.gantt', $proyecto)],
                ['label' => 'Diario',       'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'active' => true,  'route' => '#'],
                ['label' => 'Bitácora',     'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'active' => false, 'route' => route('proyectos.bitacora', $proyecto)],
                ['label' => 'Estadísticas','icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'active' => false, 'route' => route('estadisticas', ['proyectoId' => $proyecto->id])],
            ] as $tab)
                @if(($tab['label'] === 'Gantt' && !auth()->user()->puede('mapa')) || ($tab['label'] === 'Estadísticas' && !auth()->user()->puede('estadisticas')))
                    @continue
                @endif
                <a href="{{ $tab['route'] }}"
                   class="flex items-center gap-1 px-2 py-1 rounded text-[11px] font-black uppercase tracking-wider transition-all whitespace-nowrap shrink-0
                       {{ $tab['active'] ? 'bg-gray-100 dark:bg-white text-black dark:text-black' : 'text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                    </svg>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

    </nav>

    {{-- CONTENIDO --}}
    <div class="p-6 max-w-2xl mx-auto space-y-4">

        {{-- Fecha selector --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-lg uppercase tracking-widest text-black dark:text-white">Parte Diario</h2>
                <p class="text-sm text-gray-700 dark:text-gray-400 uppercase font-bold">Registrá el avance del día</p>
            </div>
            <input type="date" wire:model.live="fecha"
                class="px-3 py-2 bg-gray-100 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl text-black dark:text-white text-sm outline-none focus:border-gray-300 dark:focus:border-white/30">
        </div>

        {{-- Lista de rubros --}}
        @forelse($rubros as $rubro)
            <div wire:key="diario-{{ $rubro['id'] }}" class="text-black dark:text-white">

                {{-- Fila del rubro padre --}}
                <div wire:click="toggleRubro({{ $rubro['id'] }})"
                    class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-4 flex items-center justify-between hover:border-gray-300 dark:hover:border-white/10 transition-all cursor-pointer group
                        {{ $rubroExpandidoId === $rubro['id'] ? 'rounded-b-none border-b-0' : '' }}">

                    <div class="flex-1 min-w-0">
                        <p class="font-black text-sm uppercase truncate">{{ $rubro['nombre'] }}</p>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="flex-1 bg-gray-200 dark:bg-white/5 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500
                                    {{ $rubro['avance'] >= 100 ? 'bg-green-500' : ($rubro['avance'] > 0 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-white/10') }}"
                                     style="width: {{ min($rubro['avance'], 100) }}%"></div>
                            </div>
                            <span class="text-sm font-black shrink-0
                                {{ $rubro['avance'] >= 100 ? 'text-green-700 dark:text-green-400' : ($rubro['avance'] > 0 ? 'text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-400') }}">
                                {{ $rubro['avance'] }}%
                            </span>
                        </div>
                    </div>

                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-600 group-hover:text-black dark:group-hover:text-white transition-all ml-4 shrink-0
                        {{ $rubroExpandidoId === $rubro['id'] ? 'rotate-90' : '' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>

                {{-- Subrubros expandidos --}}
                @if($rubroExpandidoId === $rubro['id'])
                    <div class="bg-gray-50 dark:bg-[#0d0d0d] border border-t-0 border-gray-200 dark:border-white/5 rounded-b-2xl overflow-hidden divide-y divide-gray-100 dark:divide-white/[0.03]">

                        @forelse($rubro['subrubros'] as $sub)
                            <div wire:key="sub-{{ $sub['id'] }}"
                                wire:click="abrirModal({{ $sub['id'] }})"
                                class="flex items-center gap-3 px-5 py-3 hover:bg-gray-100 dark:hover:bg-white/[0.03] cursor-pointer transition group/sub">

                                {{-- Dot --}}
                                <div class="w-1.5 h-1.5 rounded-full shrink-0
                                    {{ $sub['avance'] >= 100 ? 'bg-green-500' : ($sub['avance'] > 0 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-white/20') }}"></div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold uppercase truncate text-gray-700 dark:text-gray-300 group-hover/sub:text-black dark:group-hover/sub:text-white transition">
                                        {{ $sub['nombre'] }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="flex-1 bg-gray-200 dark:bg-white/5 rounded-full h-1">
                                            <div class="h-1 rounded-full transition-all duration-500
                                                {{ $sub['avance'] >= 100 ? 'bg-green-500' : ($sub['avance'] > 0 ? 'bg-blue-400' : 'bg-gray-300 dark:bg-white/10') }}"
                                                 style="width: {{ min($sub['avance'], 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-black shrink-0 w-10 text-right
                                            {{ $sub['avance'] >= 100 ? 'text-green-600 dark:text-green-400' : ($sub['avance'] > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500') }}">
                                            {{ $sub['avance'] }}%
                                        </span>
                                    </div>
                                </div>

                                <svg class="w-3 h-3 text-gray-300 dark:text-gray-700 group-hover/sub:text-gray-600 dark:group-hover/sub:text-gray-400 transition shrink-0"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        @empty
                            {{-- Si no tiene subrubros, abrir modal del rubro padre directamente --}}
                            <div wire:click="abrirModal({{ $rubro['id'] }})"
                                class="flex items-center justify-center gap-2 px-5 py-4 text-xs font-bold uppercase text-gray-500 hover:text-black dark:hover:text-white cursor-pointer transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Registrar avance directo
                            </div>
                        @endforelse
                    </div>
                @endif

            </div>
        @empty
            <div class="py-20 text-center text-gray-400 dark:text-gray-700 text-sm uppercase font-bold tracking-widest">
                Sin rubros cargados
            </div>
        @endforelse
    </div>

    {{-- MODAL REPORTE --}}
    @if($mostrarModal)
        <div class="fixed inset-0 z-[90] flex items-end sm:items-center justify-center bg-black/80 backdrop-blur-md px-4 pb-4">
            <div class="w-full max-w-lg border border-gray-200 dark:border-white/10 rounded-2xl bg-white dark:bg-[#0d0d0d] shadow-2xl overflow-hidden text-black dark:text-white">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/5">
                    <div>
                        <p class="text-xs text-gray-700 dark:text-gray-400 uppercase font-black">Reporte del día</p>
                        <h3 class="font-black text-sm uppercase truncate text-black dark:text-white">{{ $rubroNombre }}</h3>
                    </div>
                    <button wire:click="cerrarModal"
                        class="text-gray-400 dark:text-gray-600 hover:text-black dark:hover:text-white transition-colors text-2xl leading-none">×</button>
                </div>

                <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">

                    {{-- Avance físico --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm text-gray-700 dark:text-gray-400 uppercase font-black">Avance Físico (%)</label>
                            <span class="font-black text-lg text-black dark:text-white">{{ $avanceFisico }}%</span>
                        </div>
                        <input type="range" wire:model.live.debounce.250ms="avanceFisico"
                            min="0" max="100" step="1"
                            class="w-full accent-black dark:accent-white">
                        <div class="w-full bg-gray-200 dark:bg-white/5 rounded-full h-1.5 mt-2">
                            <div class="h-1.5 rounded-full bg-black dark:bg-white transition-all"
                                 style="width: {{ $avanceFisico }}%"></div>
                        </div>
                    </div>

                    {{-- Cantidad y costo --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            @php
                                $disponibleM2 = $limiteM2 > 0 ? max(0, $limiteM2 - $acumuladoM2) : null;
                            @endphp
                            <label class="text-sm text-gray-700 dark:text-gray-400 uppercase font-black">
                                Cantidad Hoy ({{ $rubroUnidad ?: 'M2' }})
                            </label>
                            @if($disponibleM2 !== null)
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    Disponible: <span class="{{ $disponibleM2 <= 0 ? 'text-red-400 font-black' : 'text-green-400 font-bold' }}">{{ number_format($disponibleM2, 2) }}</span> / {{ number_format($limiteM2, 2) }} m²
                                </p>
                            @endif
                            <input type="number" step="0.01" min="0"
                                @if($disponibleM2 !== null) max="{{ $disponibleM2 }}" @endif
                                wire:model.live="cantidadHoy"
                                class="w-full mt-1 p-3 rounded-xl bg-gray-100 dark:bg-[#0f1115] text-black dark:text-white border border-gray-200 dark:border-white/10 text-sm outline-none focus:border-gray-300 dark:focus:border-white/30">
                            @error('cantidadHoy') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            @php
                                $disponibleCosto = $limiteCosto > 0 ? max(0, $limiteCosto - $acumuladoCosto) : null;
                            @endphp
                            <label class="text-sm text-gray-700 dark:text-gray-400 uppercase font-black">Costo Hoy (USD)</label>
                            @if($disponibleCosto !== null)
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    Disponible: <span class="{{ $disponibleCosto <= 0 ? 'text-red-400 font-black' : 'text-green-400 font-bold' }}">{{ number_format($disponibleCosto, 2) }}</span> / {{ number_format($limiteCosto, 2) }}
                                </p>
                            @endif
                            <input type="number" step="0.01" min="0"
                                @if($disponibleCosto !== null) max="{{ $disponibleCosto }}" @endif
                                wire:model="costoHoy"
                                class="w-full mt-1 p-3 rounded-xl bg-gray-100 dark:bg-[#0f1115] text-black dark:text-white border border-gray-200 dark:border-white/10 text-sm outline-none focus:border-gray-300 dark:focus:border-white/30">
                            @error('costoHoy') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Notas --}}
                    <div>
                        <label class="text-sm text-gray-700 dark:text-gray-400 uppercase font-black">Notas y Novedades</label>
                        <textarea wire:model="notas" rows="3"
                            placeholder="Ej: Se completó el vaciado de la zapata A1. Retraso por lluvia de 2 horas."
                            class="w-full mt-1 p-3 rounded-xl bg-gray-100 dark:bg-[#0f1115] text-black dark:text-white border border-gray-200 dark:border-white/10 text-sm outline-none focus:border-gray-300 dark:focus:border-white/30 resize-none placeholder-gray-500 dark:placeholder-gray-400"></textarea>
                    </div>

                    {{-- Foto --}}
                    <div>
                        <label class="text-sm text-gray-700 dark:text-gray-400 uppercase font-black">Foto de Evidencia</label>
                        <label class="mt-1 flex flex-col items-center justify-center w-full h-28 border border-dashed border-gray-200 dark:border-white/10 rounded-xl cursor-pointer hover:border-gray-300 dark:hover:border-white/30 transition-all bg-gray-100 dark:bg-[#0f1115]">
                            @if($foto)
                                <img src="{{ $foto->temporaryUrl() }}" class="h-full w-full object-cover rounded-xl">
                            @else
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-sm text-gray-700 dark:text-gray-400 font-bold uppercase">Tomar Foto o Subir</span>
                            @endif
                            <input type="file" wire:model="foto" accept="image/*" capture="environment" class="hidden">
                        </label>
                    </div>

                    {{-- Historial --}}
                 @if(count($historial) > 0)
    <div>
        <p class="text-sm text-gray-700 dark:text-gray-400 uppercase font-black mb-2">
            Últimos registros (click para ver)
        </p>

        <div class="space-y-1.5">
            @foreach($historial as $h)
                <div
                    wire:key="hist-{{ $h['id'] }}"
                    wire:click="verDetalle({{ $h['id'] }})"
                    class="flex items-center justify-between bg-white/[0.02] rounded-lg px-3 py-2 border border-white/[0.03] hover:border-white/10 cursor-pointer transition"
                >
                    <span class="text-sm text-gray-500 font-mono">
                        {{ $h['fecha'] }}
                    </span>

                    <span class="text-sm text-blue-400 font-black">
                        {{ $h['avance_fisico'] }}%
                    </span>

                    @if($h['notas'])
                        <span class="text-xs text-gray-600 truncate max-w-32">
                           {{ $h['notas'] }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-white/5">
                    <button wire:click="guardarReporte"
                        class="w-full bg-white text-black py-3.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Guardar Reporte
                    </button>
                </div>

            </div>
        </div>
    @endif
    @if($mostrarDetalle && $detalleRegistro)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">

        <div class="w-full max-w-lg bg-[#0d0d0d] border border-white/10 rounded-2xl overflow-hidden">

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                <div>
                    <p class="text-xs text-gray-600 uppercase font-black">Detalle de registro</p>
                    <h3 class="text-white font-black text-sm uppercase">
                        {{ $detalleRegistro->fecha->format('d/m/Y') }}
                    </h3>
                </div>

                <button wire:click="$set('mostrarDetalle', false)"
                    class="text-gray-600 hover:text-white text-2xl">
                    ×
                </button>
            </div>

            {{-- CONTENIDO --}}
            <div class="p-6 space-y-4">

                <div class="grid grid-cols-2 gap-3 text-xs">

                    <div class="bg-white/5 p-3 rounded-xl">
                        <p class="text-gray-500 uppercase text-sm">Avance</p>
                        <p class="text-white font-black text-lg">
                            {{ $detalleRegistro->avance_fisico }}%
                        </p>
                    </div>

                    <div class="bg-white/5 p-3 rounded-xl">
                        <p class="text-gray-500 uppercase text-sm">Cantidad (M2)</p>
                        <p class="text-white font-black">
                            {{ $detalleRegistro->cantidad_hoy }}
                        </p>
                    </div>

                    <div class="bg-white/5 p-3 rounded-xl col-span-2">
                        <p class="text-gray-500 uppercase text-sm">Costo</p>
                        <p class="text-white font-black">
                            ${{ number_format($detalleRegistro->costo_hoy, 2) }}
                        </p>
                    </div>

                </div>

                {{-- NOTAS --}}
                <div>
                    <p class="text-sm text-gray-500 uppercase font-black mb-1">
                        Notas
                    </p>

                    <div class="bg-white/5 p-3 rounded-xl text-sm text-gray-300">
                        {{ $detalleRegistro->notas ?? 'Sin notas registradas' }}
                    </div>
                </div>

                {{-- FOTO --}}
             @if($detalleRegistro->foto_path)
    <div x-data="{ open: false }">
        <p class="text-sm text-gray-500 uppercase font-black mb-1">Evidencia (clic para ampliar)</p>
        
        <img 
            src="{{ asset('storage/' . $detalleRegistro->foto_path) }}" 
            class="rounded-xl border border-white/10 w-full cursor-zoom-in hover:opacity-80 transition"
            @click="open = true"
        >

        <div 
            x-show="open" 
            x-transition.opacity
            class="fixed inset-0 z-[200] flex items-center justify-center bg-black/95 p-4"
            @click="open = false"
            @keydown.escape.window="open = false"
            style="display: none;"
        >
            <button class="absolute top-6 right-6 text-white text-4xl font-light">&times;</button>

            <img 
                src="{{ asset('storage/' . $detalleRegistro->foto_path) }}" 
                class="max-w-full max-h-full rounded-lg shadow-2xl"
                @click.stop
            >
        </div>
    </div>
@endif

            </div>

            {{-- Footer detalle --}}
            <div class="px-6 py-4 border-t border-white/5">
                <button
                    wire:click="confirmarEliminar({{ $detalleRegistro->id }})"
                    class="w-full bg-red-600/10 hover:bg-red-600 border border-red-600/30 hover:border-red-600 text-red-400 hover:text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar este registro
                </button>
            </div>

        </div>
    </div>
@endif

{{-- MODAL CONFIRMAR ELIMINACIÓN --}}
@if($mostrarConfirmar)
    <div class="fixed inset-0 z-[110] flex items-end justify-center sm:items-center bg-black/70 backdrop-blur-sm px-4 pb-6 sm:pb-0">
        <div class="w-full max-w-sm bg-[#111] border border-white/10 rounded-2xl overflow-hidden">

            <div class="p-6 text-center">
                {{-- Ícono --}}
                <div class="w-12 h-12 rounded-full bg-red-600/10 border border-red-600/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>

                <p class="text-white font-black text-sm uppercase tracking-wide mb-1">¿Eliminar registro?</p>
                <p class="text-gray-500 text-xs">Esta acción no se puede deshacer.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 px-6 pb-6">
                <button
                    wire:click="$set('mostrarConfirmar', false)"
                    class="w-full py-3 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:border-white/30 font-black text-xs uppercase tracking-widest transition-all">
                    Cancelar
                </button>
                <button
                    wire:click="eliminarRegistro"
                    class="w-full py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white font-black text-xs uppercase tracking-widest transition-all">
                    Eliminar
                </button>
            </div>

        </div>
    </div>
@endif

</div>
