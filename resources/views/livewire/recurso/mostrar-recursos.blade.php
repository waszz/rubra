<div class="space-y-5 p-6 bg-gray-50 dark:bg-[#090909] text-black dark:text-white">

    {{-- HEADER --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold text-black dark:text-white tracking-tight">Recursos</h1>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-widest mt-1">
            Materiales, mano de obra, equipos y composiciones
        </p>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
        @if(count($selectedResources ?? []) > 0)
            <button wire:click="confirmarEliminacionMultiple"
                class="w-full sm:w-auto px-3 py-2 rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 text-[11px] font-medium hover:bg-red-500 hover:text-white transition-all">
                Eliminar seleccionados ({{ count($selectedResources) }})
            </button>
        @endif

        <label class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-white/5 border border-gray-300 dark:border-white/10 cursor-pointer hover:border-blue-300 dark:hover:border-white/20 transition-all">
            <input type="checkbox" wire:model.live="selectAll" wire:click="toggleSelectAll"
                class="w-4 h-4 rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-white/5 text-orange-500 focus:ring-0">
            <span class="text-[11px] font-medium text-gray-600 dark:text-gray-400">Seleccionar todo</span>
        </label>

        <div class="relative w-full sm:w-56">
            <input type="text"
                wire:model.live.debounce.300ms="buscar"
                placeholder="Buscar recurso..."
                class="pl-9 pr-4 py-2 rounded-lg bg-gray-50 dark:bg-[#111] text-black dark:text-white border border-gray-300 dark:border-gray-800 focus:border-blue-500 focus:outline-none text-[13px] w-full shadow-sm transition-all">
            <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <select wire:model.live="filtroTipo"
            class="flex-1 sm:flex-none px-3 py-2 rounded-lg bg-gray-50 dark:bg-[#111] text-black dark:text-white border border-gray-300 dark:border-gray-800 focus:border-blue-500 focus:outline-none text-[13px] cursor-pointer shadow-sm">
            <option value="">Todos los tipos</option>
            <option value="material">Material</option>
            <option value="labor">Mano de obra</option>
            <option value="equipment">Equipo</option>
            <option value="composition">Composición</option>
        </select>

        <select wire:model.live="filtroProyecto"
            class="flex-1 sm:flex-none px-3 py-2 rounded-lg bg-gray-50 dark:bg-[#111] text-black dark:text-white border border-gray-300 dark:border-gray-800 focus:border-blue-500 focus:outline-none text-[13px] cursor-pointer shadow-sm">
            <option value="">Todos los proyectos</option>
            @foreach($proyectos as $proyecto)
                <option value="{{ $proyecto->id }}">{{ $proyecto->nombre_proyecto }}</option>
            @endforeach
        </select>

        <button wire:click="abrirModalRecurso"
            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-blue-100 dark:border-white/10 bg-blue-50 dark:bg-transparent text-blue-700 dark:text-white text-[12px] font-bold hover:bg-blue-100 dark:hover:bg-white/5 transition-all shadow-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Recurso
        </button>

        <button wire:click="abrirModalComposicion"
            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-purple-100 dark:border-white/10 bg-purple-50 dark:bg-transparent text-purple-700 dark:text-white text-[12px] font-bold hover:bg-purple-100 dark:hover:bg-white/5 transition-all shadow-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Nueva Composición
        </button>

        <button wire:click="abrirModalImportar"
            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-green-100 dark:border-white/10 bg-green-50 dark:bg-transparent text-green-700 dark:text-white text-[12px] font-bold hover:bg-green-100 dark:hover:bg-white/5 transition-all shadow-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-4m0 0V8m0 4H8m4 0h4M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Importar Excel
        </button>
    </div>
</div>

    {{-- TOGGLE --}}
    <div class="flex bg-gray-100 dark:bg-[#111] p-1 rounded-lg border border-gray-300 dark:border-gray-800 w-fit shadow-sm">
        <button wire:click="$set('vista','grid')"
            class="px-4 py-1.5 rounded-md text-[11px] font-bold transition-all {{ $vista === 'grid' ? 'bg-gray-200 dark:bg-[#1a1a1a] text-black dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
            GRID
        </button>
        <button wire:click="$set('vista','lista')"
            class="px-4 py-1.5 rounded-md text-[11px] font-bold transition-all {{ $vista === 'lista' ? 'bg-gray-200 dark:bg-[#1a1a1a] text-black dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
            LISTA
        </button>
    </div>

    {{-- CONTENIDO --}}
    <div class="bg-gray-50 dark:bg-[#0f0f0f] border border-gray-200 dark:border-gray-800/60 rounded-2xl overflow-hidden shadow-xl">

      
     {{-- ================= GRID ================= --}}
@if($vista === 'grid')
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($recursos as $recurso)
            @php
                $config = match($recurso->tipo) {
                    'material'    => ['bg'=>'bg-blue-500/20',   'text'=>'text-blue-400',   'icon'=>'M'],
                    'labor'       => ['bg'=>'bg-green-500/20',  'text'=>'text-green-400',  'icon'=>'L'],
                    'equipment'   => ['bg'=>'bg-orange-500/20', 'text'=>'text-orange-400', 'icon'=>'E'],
                    'composition' => ['bg'=>'bg-purple-500/20', 'text'=>'text-purple-400', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>'],
                    default       => ['bg'=>'bg-gray-500/20',   'text'=>'text-gray-400',   'icon'=>'R']
                };
            @endphp

            <div wire:key="recurso-grid-{{ $recurso->id }}" class="bg-white dark:bg-[#141414] border border-gray-300 dark:border-white/5 rounded-[24px] p-6 hover:border-blue-300 dark:hover:border-white/10 transition-all group flex flex-col h-full relative shadow-sm">
                
                {{-- TOP ACTIONS & ICON --}}
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 rounded-2xl {{ $config['bg'] }} {{ $config['text'] }} flex items-center justify-center">
                        {!! $recurso->tipo === 'composition' ? $config['icon'] : '<span class="font-bold">'.$config['icon'].'</span>' !!}
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="editar({{ $recurso->id }})" class="p-2 text-gray-500 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        
                        <input type="checkbox" value="{{ $recurso->id }}" wire:model.live="selectedResources"
                            class="ml-2 w-4 h-4 rounded border-gray-700 bg-white/5 text-orange-500 focus:ring-0 cursor-pointer">
                    </div>
                </div>

                {{-- INFO --}}
                <div class="mb-6">
                    <h3 class="text-black dark:text-white text-[15px] font-bold uppercase tracking-wide leading-tight">{{ $recurso->nombre }}</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-gray-500 dark:text-gray-400 text-[11px] uppercase font-bold">{{ $recurso->unidad }}</span>
                        <span class="text-gray-700 dark:text-gray-500 text-[11px]">•</span>
                        <span class="text-gray-500 dark:text-gray-400 text-[11px] uppercase font-bold">SIN PROVEEDOR</span>
                    </div>

                    <div class="mt-6">
                        <p class="text-gray-500 dark:text-gray-400 text-[9px] font-bold uppercase tracking-widest mb-1">Precio Unitario</p>
                        <p class="text-[28px] font-bold text-black dark:text-white tracking-tighter">
                            USD {{ number_format($recurso->precio_usd, 0, ',', '.') }}<span class="text-lg text-black/50 dark:text-white/50">,{{ explode('.', number_format($recurso->precio_usd, 2, '.', ''))[1] }}</span>
                        </p>
                    </div>
                </div>

                {{-- BOTON HISTORIAL (OPCIONAL) --}}
                <button wire:click="abrirHistorialPrecios({{ $recurso->id }})" class="w-full py-2.5 mb-6 rounded-xl border border-gray-200 dark:border-white/5 bg-gray-100/50 dark:bg-white/[0.02] text-gray-600 dark:text-gray-400 text-[11px] font-bold flex items-center justify-center gap-2 hover:bg-gray-200 dark:hover:bg-white/5 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Ver Historial de Precios
                </button>

             
             
{{-- DESGLOSE (COMPOSICIÓN) --}}
@if($recurso->tipo === 'composition' && $recurso->items->count())
    <div class="mt-auto pt-4 border-t border-gray-200 dark:border-white/5">
        <div class="flex justify-between items-center mb-3">
            <p class="text-[10px] text-purple-400 font-black uppercase tracking-[0.15em]">Composición</p>
            <button wire:click="abrirAgregarItem({{ $recurso->id }})"
                class="flex items-center gap-1 text-[14px] text-purple-400 hover:text-purple-300 font-bold transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Agregar
            </button>
        </div>
        <div class="space-y-2">
            @foreach($recurso->items as $item)
                <div class="flex justify-between items-center text-[12px] group/item">
                    <span class="text-gray-600 dark:text-gray-400 font-medium truncate max-w-[140px]">{{ $item->nombre }}</span>
                   <div class="flex items-center gap-2">
    <span class="text-black dark:text-white font-bold">{{ number_format($item->cantidad, 2) }} <span class="text-gray-500 dark:text-gray-400 font-medium">{{ $item->recursoBase?->unidad ?? '' }}</span></span>
    <span class="text-gray-700 dark:text-gray-600">–</span>
    <span class="text-black dark:text-white font-bold">USD {{ number_format($item->precio_total, 2) }}</span>

    {{-- Editar --}}
    <button wire:click="editarItem({{ $item->id }})"
        class="opacity-0 group-hover/item:opacity-100 transition-opacity">
        <svg class="w-4 h-4 text-gray-500 hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    {{-- Eliminar --}}
  <button wire:click="abrirModalEliminar({{ $item->id }})"
    class="opacity-0 group-hover/item:opacity-100 transition-opacity">
    
    <svg class="w-4 h-4 text-gray-500 hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
    </svg>

</button>
</div>
                </div>
            @endforeach
        </div>

        {{-- TOTAL --}}
        <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200 dark:border-white/5">
            <span class="text-[10px] text-purple-400 font-black uppercase tracking-[0.15em]">Total</span>
            <span class="text-black dark:text-white font-bold text-[13px]">USD {{ number_format($recurso->precio_usd, 2) }}</span>
        </div>
    </div>
@endif
            </div>
        @endforeach
    </div>
@endif
        {{-- ================= LISTA ================= --}}
        @if($vista === 'lista')
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-300 dark:border-gray-800 bg-gray-100 dark:bg-[#111]/60">
                            <th class="pl-6 py-4 w-10"></th>
                            <th class="px-3 py-4 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Nombre</th>
                            <th class="px-3 py-4 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-center">Tipo</th>
                            <th class="px-3 py-4 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-center">Unidad</th>
                            <th class="px-3 py-4 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-right">Precio</th>
                            <th class="pr-6 py-4 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-right w-20">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800/40">
                        @foreach($recursos as $recurso)
                            <tr wire:key="recurso-lista-{{ $recurso->id }}" class="hover:bg-blue-50 dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="pl-6 py-4">
                                    <input type="checkbox" value="{{ $recurso->id }}" wire:model.live="selectedResources"
                                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-orange-500 focus:ring-0 cursor-pointer">
                                </td>
                                <td class="px-3 py-4">
                                    <span class="text-black dark:text-white text-[13px] font-medium">{{ $recurso->nombre }}</span>
                                </td>
                                <td class="px-3 py-4 text-center">
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-gray-200 dark:bg-white/5 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-white/5 uppercase inline-block">
                                        {{ str_replace('_', ' ', $recurso->tipo) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 text-gray-600 dark:text-gray-400 text-[12px] text-center font-medium">{{ $recurso->unidad }}</td>
                                <td class="px-3 py-4 text-right text-black dark:text-white font-mono font-bold">
                                    USD {{ number_format($recurso->precio_usd, 2, ',', '.') }}
                                </td>
                                <td class="pr-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button wire:click="editar({{ $recurso->id }})" 
                                            class="p-1.5 rounded-lg bg-gray-100 dark:bg-white/5 border border-gray-300 dark:border-white/5 text-gray-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-white hover:bg-blue-100 dark:hover:bg-white/10 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            {{-- ... (Mantenemos la fila de composición igual si existe) --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- PAGINACIÓN --}}
        @if($recursos->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#111]/30">
                {{ $recursos->links() }}
            </div>
        @endif
    </div>



    {{-- ════════════════════════════════════════════════════════
     MODAL: EDITAR RECURSO
═════════════════════════════════════════════════════════ --}}
@if($modalEditar)
<div
    class="fixed inset-0 z-50 flex items-center justify-center"
    wire:keydown.escape="cerrarModalEditar"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/70 backdrop-blur-sm"
        wire:click="cerrarModalEditar"
    ></div>

    {{-- Panel --}}
    <div class="relative z-10 w-full max-w-md mx-4 bg-[#111] border border-gray-800 rounded-2xl shadow-2xl">

        {{-- Cabecera --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60">
            <h2 class="text-[15px] font-semibold text-white">Editar recurso</h2>
            <button
                wire:click="cerrarModalEditar"
                class="p-1.5 rounded-lg text-gray-500 hover:text-white hover:bg-white/8 transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Formulario --}}
        <div class="px-6 py-5 space-y-4">

            {{-- Nombre --}}
            <div>
                <label class="block text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-1.5">Nombre</label>
                <input
                    type="text"
                    wire:model="editNombre"
                    class="w-full px-3 py-2 rounded-lg bg-[#0a0a0a] text-white border text-[13px] focus:outline-none transition-colors
                           {{ $errors->has('editNombre') ? 'border-red-500/50 focus:border-red-500' : 'border-gray-800 focus:border-gray-600' }}"
                    placeholder="Ej: Cemento Portland 50kg"
                >
                @error('editNombre')
                    <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipo + Unidad (2 col) --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-1.5">Tipo</label>
                    <select
                        wire:model.live="editTipo"
                        class="w-full px-3 py-2 rounded-lg bg-[#0a0a0a] text-white border text-[13px] focus:outline-none cursor-pointer transition-colors
                               {{ $errors->has('editTipo') ? 'border-red-500/50' : 'border-gray-800 focus:border-gray-600' }}"
                    >
                        <option value="">Seleccionar...</option>
                        <option value="material">Material</option>
                        <option value="labor">Mano de obra</option>
                        <option value="equipment">Equipo</option>
                        <option value="Composición">Composición</option>
                    </select>
                    @error('editTipo')
                        <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-1.5">Unidad</label>
                    <input
                        type="text"
                        wire:model="editUnidad"
                        class="w-full px-3 py-2 rounded-lg bg-[#0a0a0a] text-white border text-[13px] focus:outline-none transition-colors
                               {{ $errors->has('editUnidad') ? 'border-red-500/50' : 'border-gray-800 focus:border-gray-600' }}"
                        placeholder="Ej: m², hr, kg"
                    >
                    @error('editUnidad')
                        <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Precio --}}
            <div>
                <label class="block text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-1.5">Precio USD</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-600 font-mono">USD</span>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        wire:model="editPrecio"
                        class="w-full pl-10 pr-3 py-2 rounded-lg bg-[#0a0a0a] text-white border text-[13px] focus:outline-none transition-colors
                               {{ $errors->has('editPrecio') ? 'border-red-500/50' : 'border-gray-800 focus:border-gray-600' }}"
                        placeholder="0.00"
                    >
                </div>
                @error('editPrecio')
                    <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Carga Social (solo para mano de obra) --}}
            @if ($editTipo === 'labor')
            <div>
                <label class="block text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-1.5">Carga Social (%)</label>
                <input
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    wire:model="editSocialChargesPercentage"
                    class="w-full px-3 py-2 rounded-lg bg-[#0a0a0a] text-white border text-[13px] focus:outline-none transition-colors
                           {{ $errors->has('editSocialChargesPercentage') ? 'border-red-500/50' : 'border-gray-800 focus:border-gray-600' }}"
                    placeholder="Ej: 72"
                >
                <p class="text-[9px] text-gray-500 mt-1">Porcentaje de carga social sobre el costo de mano de obra</p>
                @error('editSocialChargesPercentage')
                    <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>
                @enderror
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-800/60 flex justify-end gap-2">
            <button
                wire:click="cerrarModalEditar"
                class="px-4 py-2 rounded-lg border border-gray-800 text-gray-400 hover:text-white hover:border-gray-600 text-[13px] transition-all"
            >
                Cancelar
            </button>
            <button
                wire:click="guardarEdicion"
                wire:loading.attr="disabled"
                wire:target="guardarEdicion"
                class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-400 text-white text-[13px] font-medium transition-all disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="guardarEdicion">Guardar cambios</span>
                <span wire:loading wire:target="guardarEdicion">Guardando...</span>
            </button>
        </div>
    </div>
</div>
@endif


{{-- ════════════════════════════════════════════════════════
     MODAL: CONFIRMAR ELIMINACIÓN INDIVIDUAL
═════════════════════════════════════════════════════════ --}}
@if($modalEliminar)
<div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="cerrarModalEliminar"></div>

    <div class="relative z-10 w-full max-w-sm mx-4 bg-[#111] border border-gray-800 rounded-2xl shadow-2xl p-6 text-center">
        <div class="w-11 h-11 rounded-xl bg-red-500/10 flex items-center justify-center mx-auto mb-4">
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <h3 class="text-[15px] font-semibold text-white mb-1">¿Eliminar recurso?</h3>
        <p class="text-[13px] text-gray-500 mb-6">Esta acción no se puede deshacer.</p>

        <div class="flex gap-2 justify-center">
            <button
                wire:click="cerrarModalEliminar"
                class="px-4 py-2 rounded-lg border border-gray-800 text-gray-400 hover:text-white text-[13px] transition-all"
            >
                Cancelar
            </button>
            <button
                wire:click="eliminar"
                wire:loading.attr="disabled"
                wire:target="eliminar"
                class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-400 text-white text-[13px] font-medium transition-all disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="eliminar">Sí, eliminar</span>
                <span wire:loading wire:target="eliminar">Eliminando...</span>
            </button>
        </div>
    </div>
</div>
@endif


{{-- ════════════════════════════════════════════════════════
     MODAL: CONFIRMAR ELIMINACIÓN MÚLTIPLE
═════════════════════════════════════════════════════════ --}}
@if($modalEliminarMultiple)
<div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="cerrarModalEliminarMultiple"></div>

    <div class="relative z-10 w-full max-w-sm mx-4 bg-[#111] border border-gray-800 rounded-2xl shadow-2xl p-6 text-center">
        <div class="w-11 h-11 rounded-xl bg-red-500/10 flex items-center justify-center mx-auto mb-4">
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <h3 class="text-[15px] font-semibold text-white mb-1">
            ¿Eliminar {{ count($selectedResources) }} recurso{{ count($selectedResources) !== 1 ? 's' : '' }}?
        </h3>
        <p class="text-[13px] text-gray-500 mb-6">Esta acción no se puede deshacer.</p>

        <div class="flex gap-2 justify-center">
            <button
                wire:click="cerrarModalEliminarMultiple"
                class="px-4 py-2 rounded-lg border border-gray-800 text-gray-400 hover:text-white text-[13px] transition-all"
            >
                Cancelar
            </button>
            <button
                wire:click="eliminarMultiple"
                wire:loading.attr="disabled"
                wire:target="eliminarMultiple"
                class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-400 text-white text-[13px] font-medium transition-all disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="eliminarMultiple">Sí, eliminar todos</span>
                <span wire:loading wire:target="eliminarMultiple">Eliminando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- MODAL EDITAR ITEM --}}
@if($modalEditarItem)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-[#141414] border border-white/10 rounded-2xl p-6 w-full max-w-sm shadow-xl">
        <h2 class="text-white font-bold text-[15px] mb-4">Editar item</h2>

        <div class="mb-3">
            <label class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-1 block">Nombre</label>
            <input type="text" wire:model="editItemNombre"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-[14px] focus:outline-none focus:border-purple-500/50">
            @error('editItemNombre') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-1 block">Cantidad</label>
            <input type="number" step="0.001" wire:model="editItemCantidad"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-[14px] focus:outline-none focus:border-purple-500/50">
            @error('editItemCantidad') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button wire:click="cerrarModalEditarItem"
                class="flex-1 py-2.5 rounded-xl border border-white/10 text-gray-400 text-[12px] font-bold hover:bg-white/5 transition-all">
                Cancelar
            </button>
            <button wire:click="guardarItem"
                class="flex-1 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-[12px] font-bold transition-all">
                Guardar
            </button>
        </div>
    </div>
</div>
@endif

@if($modalEliminarItem)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-[#141414] border border-white/10 rounded-2xl p-6 w-full max-w-sm shadow-xl">

        <h2 class="text-white font-bold text-[15px] mb-2">
            ¿Eliminar item?
        </h2>

        <p class="text-gray-400 text-[12px] mb-5">
            Esta acción no se puede deshacer.
        </p>

        <div class="flex gap-3">
            <button wire:click="cerrarModalEliminarItem"
                class="flex-1 py-2.5 rounded-xl border border-white/10 text-gray-400 text-[12px] font-bold hover:bg-white/5 transition-all">
                Cancelar
            </button>

            <button wire:click="eliminarItem"
                class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white text-[12px] font-bold transition-all">
                Eliminar
            </button>
        </div>

    </div>
</div>
@endif

{{-- MODAL AGREGAR ITEM --}}
@if($modalAgregarItem)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-[#141414] border border-white/10 rounded-2xl p-6 w-full max-w-sm shadow-xl">
        <h2 class="text-white font-bold text-[15px] mb-4">Agregar material</h2>

        <div class="mb-3 relative overflow-visible" x-data="{ valor: '' }">
            <label class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-1 block">Buscar recurso</label>
            <input
                type="text"
                x-model="valor"
                x-on:input.debounce.300ms="$wire.set('nuevoItemNombre', valor); $wire.buscarRecursos()"
                placeholder="Ej: Arena Gruesa..."
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-[14px] focus:outline-none focus:border-purple-500/50">
            @error('nuevoItemNombre') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror

            @if(count($recursosSugeridos))
                <div class="absolute z-[9999] w-full mt-1 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl overflow-hidden">
                    @foreach($recursosSugeridos as $sugerido)
                        <button
                            x-on:mousedown.prevent="valor = '{{ $sugerido }}'; $wire.seleccionarRecurso('{{ $sugerido }}')"
                            class="w-full text-left px-4 py-2.5 text-[13px] text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                            {{ $sugerido }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mb-4">
            <label class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-1 block">Cantidad</label>
            <input type="number" step="0.001" wire:model="nuevoItemCantidad"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-[14px] focus:outline-none focus:border-purple-500/50">
            @error('nuevoItemCantidad') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button wire:click="cerrarModalAgregarItem"
                class="flex-1 py-2.5 rounded-xl border border-white/10 text-gray-400 text-[12px] font-bold hover:bg-white/5 transition-all">
                Cancelar
            </button>
            <button wire:click="guardarNuevoItem"
                class="flex-1 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-[12px] font-bold transition-all">
                Agregar
            </button>
        </div>
    </div>
</div>
@endif

{{-- MODAL NUEVO RECURSO --}}
@if($modalRecurso)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto py-8">
    <div class="w-full max-w-md mx-4">
        <livewire:recurso.crear-recurso :key="'crear-recurso-'.now()" />
    </div>
</div>
@endif

{{-- MODAL NUEVA COMPOSICIÓN --}}
@if($modalComposicion)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-md mx-4">
        <livewire:recurso.crear-composicion :key="'crear-composicion-'.now()" />
    </div>
</div>
@endif

{{-- MODAL IMPORTAR DESDE EXCEL --}}
@if($modalImportar)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto py-8">
    <div class="max-w-md mx-4 border border-white/10 rounded-2xl p-6 space-y-6 shadow-2xl">

        <h2 class="text-center text-white font-extrabold tracking-widest text-sm uppercase">
            Importar Recursos
        </h2>

        {{-- Contenido --}}
        @if(!$mostrarResultadosImportacion)
            {{-- Seleccionar tipo de recurso --}}
            <div>
                <label class="text-[10px] tracking-widest text-gray-500 uppercase">Tipo de Recurso</label>
                <select wire:model="tipoImportacion" class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
                    <option value="material">Material</option>
                    <option value="equipment">Herramienta/Equipo</option>
                    <option value="labor">Mano de Obra</option>
                </select>
                <p class="text-[10px] text-gray-500 mt-2">Todos los recursos importados serán de este tipo.</p>
            </div>

            {{-- Seleccionar archivo --}}
            <div>
                <label class="text-[10px] tracking-widest text-gray-500 uppercase">Archivo Excel</label>
                <input type="file" wire:model="archivoImportacion" accept=".xlsx,.xls,.csv" 
                    class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none text-[13px]">
                <p class="text-[10px] text-gray-500 mt-2">Formatos soportados: Excel (.xlsx, .xls) y CSV</p>
            </div>

            {{-- Instrucciones de formato --}}
            <div class="p-3 bg-white/5 border border-white/10 rounded-xl">
                <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Formato requerido</p>
                <p class="text-[10px] text-gray-500">
                    <strong>Columna A:</strong> Nombre del recurso<br>
                    <strong>Columna B:</strong> Unidad (ej: m, kg, broca)<br>
                    <strong>Columna C:</strong> Precio USD
                </p>
                <p class="text-[9px] text-gray-600 mt-2">Ejemplo: Cable Unipolar 2.5mm | m | 0.80</p>
            </div>

            {{-- Estado de carga --}}
            @if($importandoEnProgreso)
                <div class="flex items-center justify-center gap-3 py-3">
                    <div class="w-3 h-3 rounded-full bg-white/30 animate-pulse"></div>
                    <span class="text-[12px] text-gray-400">Importando...</span>
                </div>
            @endif

            {{-- Botones --}}
            <div class="flex gap-3 pt-2">
                <button wire:click="cerrarModalImportar" class="w-1/2 py-3 rounded-xl border border-white/10 text-white hover:bg-white/5 transition">
                    Cancelar
                </button>
                <button wire:click="importarDesdeExcel" wire:disable="!archivoImportacion || importandoEnProgreso" 
                    class="w-1/2 bg-white text-black py-3 rounded-xl font-bold hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    Importar
                </button>
            </div>
        @else
            {{-- Resultados de importación --}}
            <div class="space-y-4">
                <div class="p-3 rounded-xl border" 
                    @class(['border-green-500/20 bg-green-500/5' => str_contains($mensajeImportacion, '✓'), 'border-red-500/20 bg-red-500/5' => !str_contains($mensajeImportacion, '✓')])>
                    <p class="text-[12px] whitespace-pre-wrap leading-relaxed"
                        @class(['text-green-400' => str_contains($mensajeImportacion, '✓'), 'text-red-400' => !str_contains($mensajeImportacion, '✓')])>
                        {{ $mensajeImportacion }}
                    </p>
                </div>

                {{-- Tabla con recursos importados --}}
                @if(!empty($recursosBienImportados))
                    <div class="max-h-[250px] overflow-y-auto">
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Recursos importados</p>
                        <div class="space-y-2">
                            @foreach($recursosBienImportados as $recurso)
                                <div class="p-2.5 bg-white/5 border border-white/10 rounded-lg text-[10px]">
                                    <p class="font-bold text-white">{{ $recurso['nombre'] }}</p>
                                    <p class="text-gray-500">{{ $recurso['unidad'] }} • USD {{ number_format($recurso['precio'], 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Botón de cerrar --}}
            <button wire:click="cerrarModalImportar" class="w-full py-3 rounded-xl bg-white text-black font-bold hover:bg-gray-200 transition">
                Cerrar
            </button>
        @endif

    </div>
</div>
@endif

{{-- MODAL HISTORIAL DE PRECIOS --}}
@if($modalHistorialPrecios)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md px-4">
    <div class="bg-[#141414] border border-white/10 rounded-2xl w-full max-w-2xl shadow-xl max-h-[80vh] overflow-hidden flex flex-col">
        
        {{-- HEADER --}}
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-white font-bold text-lg uppercase tracking-widest">Historial de Precios</h2>
            <button wire:click="cerrarHistorialPrecios" class="text-gray-500 hover:text-white text-2xl transition-colors">×</button>
        </div>

        {{-- CONTENIDO --}}
        <div class="overflow-y-auto flex-1 p-6">
            @if(count($precioHistorial) > 0)
                <div class="space-y-4">
                    @foreach($precioHistorial as $registro)
                        <div class="border border-white/10 rounded-xl p-4 bg-white/[0.02] hover:bg-white/[0.05] transition-all">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-[11px] text-gray-600 uppercase font-black tracking-widest mb-1">
                                        {{ $registro->created_at->locale('es')->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }}
                                    </p>
                                    @if($registro->razon)
                                        <p class="text-[12px] text-gray-400">{{ $registro->razon }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    @if($registro->precio_anterior)
                                        <div>
                                            <p class="text-[10px] text-gray-600 uppercase font-bold">De</p>
                                            <p class="text-[18px] font-black text-gray-400 font-mono">USD {{ number_format($registro->precio_anterior, 2, ',', '.') }}</p>
                                        </div>
                                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    @endif
                                    <div>
                                        <p class="text-[10px] text-gray-600 uppercase font-bold">{{ $registro->precio_anterior ? 'A' : 'Precio inicial' }}</p>
                                        <p class="text-[18px] font-black {{ $registro->precio_anterior && $registro->precio_nuevo > $registro->precio_anterior ? 'text-green-400' : 'text-white' }} font-mono">USD {{ number_format($registro->precio_nuevo, 2, ',', '.') }}</p>
                                    </div>
                                </div>

                                @if($registro->precio_anterior)
                                    @php
                                        $diferencia = $registro->precio_nuevo - $registro->precio_anterior;
                                        $porcentaje = ($diferencia / $registro->precio_anterior) * 100;
                                    @endphp
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray-600 uppercase font-bold">Cambio</p>
                                        <p class="text-[14px] font-black {{ $diferencia >= 0 ? 'text-red-400' : 'text-green-400' }} font-mono">
                                            {{ $diferencia >= 0 ? '+' : '' }}USD {{ number_format($diferencia, 2, ',', '.') }}
                                        </p>
                                        <p class="text-[11px] {{ $porcentaje >= 0 ? 'text-red-400' : 'text-green-400' }} font-bold">
                                            {{ $porcentaje >= 0 ? '+' : '' }}{{ number_format($porcentaje, 1) }}%
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 text-[13px]">No hay historial de precios registrado.</p>
                </div>
            @endif
        </div>

        {{-- FOOTER --}}
        <div class="px-6 py-4 border-t border-white/10 flex justify-end">
            <button wire:click="cerrarHistorialPrecios"
                class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-[12px] font-bold hover:bg-white/10 transition-all">
                Cerrar
            </button>
        </div>
    </div>
</div>
@endif
</div>
