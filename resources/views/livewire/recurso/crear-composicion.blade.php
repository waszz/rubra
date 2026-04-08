<div class="max-w-md mx-auto border border-white/10 rounded-2xl p-6 space-y-6 shadow-2xl">

    <h2 class="text-center text-white font-extrabold tracking-widest text-sm uppercase">
        Nueva Composición
    </h2>

    {{-- NOMBRE --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Nombre</label>
        <input type="text" wire:model="nombre"
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
        @error('nombre') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- UNIDAD --}}
    <div>
        <label class="text-[10px] text-gray-500 uppercase">Unidad</label>
        <select wire:model="unidad" class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
            <option value="un">und (Unidad)</option>
            <option value="m">m (Metro)</option>
            <option value="m2">m² (Metro cuadrado)</option>
            <option value="m3">m³ (Metro cúbico)</option>
            <option value="kg">kg (Kilogramo)</option>
            <option value="h">h (Hora)</option>
        </select>
    </div>

    {{-- RECURSOS --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <label class="text-[10px] tracking-widest text-gray-500 uppercase">Recursos en Composición</label>
            <button wire:click="abrirSelector" type="button"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-600/20 border border-purple-500/30 text-purple-400 text-[11px] font-bold hover:bg-purple-600/30 transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar Recurso
            </button>
        </div>

        {{-- LISTA DE ITEMS --}}
        <div class="space-y-2">
            @forelse($items as $i => $item)
                <div class="flex items-center gap-2 bg-white/5 rounded-xl px-3 py-2.5">
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-[13px] font-medium truncate">{{ $item['nombre'] }}</p>
                        <p class="text-gray-500 text-[10px] uppercase">{{ $item['unidad'] }} · USD {{ number_format($item['precio_usd'], 2) }}</p>
                    </div>
                    <input type="number" step="0.01" min="0"
                        wire:model="items.{{ $i }}.cantidad"
                        class="w-20 bg-[#0f1115] border border-white/10 rounded-lg px-2 py-1.5 text-white text-[12px] text-center focus:outline-none focus:border-purple-500/50">
                    <button wire:click="quitarItem({{ $i }})" type="button" class="text-gray-600 hover:text-red-400 transition-colors ml-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @empty
                <div class="border border-dashed border-white/10 rounded-xl p-6 text-center text-gray-600 text-[12px]">
                    No hay recursos agregados
                </div>
            @endforelse
        </div>

        {{-- TOTAL --}}
        @if(count($items) > 0)
            @php $total = collect($items)->sum(fn($i) => $i['precio_usd'] * $i['cantidad']); @endphp
            <div class="flex justify-between items-center mt-3 pt-3 border-t border-white/5">
                <span class="text-[10px] text-gray-500 uppercase font-bold">Total estimado</span>
                <span class="text-white font-bold text-[13px]">USD {{ number_format($total, 2) }}</span>
            </div>
        @endif
    </div>

    {{-- BOTONES --}}
    <div class="flex gap-3 pt-2">
        <button type="button" wire:click="cancelar"
            class="w-1/2 py-3 rounded-xl border border-white/10 text-white hover:bg-white/5 transition">
            Cancelar
        </button>
        <button type="button" wire:click="guardar"
            class="w-1/2 bg-white text-black py-3 rounded-xl font-bold hover:bg-gray-200 transition">
            Crear Composición
        </button>
    </div>


{{-- ══════════════════════════════════════════════
     MODAL SELECTOR DE RECURSOS
══════════════════════════════════════════════ --}}
@if($modalSelector)
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-sm">
    <div class="bg-[#0f0f0f] border border-white/10 rounded-2xl w-full max-w-lg mx-4 shadow-2xl flex flex-col max-h-[80vh]">

        {{-- HEADER --}}
        <div class="px-6 pt-6 pb-4 border-b border-white/5">
            <div class="flex justify-between items-start mb-1">
                <h3 class="text-white font-extrabold tracking-widest text-sm uppercase">Seleccionar Recurso</h3>
                <button wire:click="cerrarSelector" type="button" class="text-gray-500 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-[10px] text-gray-500 uppercase tracking-widest">Busca y selecciona materiales, mano de obra o equipos</p>

            {{-- BUSCADOR --}}
            <div class="relative mt-4">
                <svg class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" wire:model.live.debounce.200ms="buscarSelector"
                    placeholder="Buscar por nombre..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-[13px] focus:outline-none focus:border-purple-500/50">
            </div>

            {{-- FILTROS --}}
            <div class="flex gap-2 mt-3 flex-wrap">
              @foreach(['' => 'Todos', 'material' => 'Materiales', 'labor' => 'Mano de Obra', 'equipment' => 'Equipos', 'composition' => 'Composiciones'] as $val => $label)
                    <button wire:click="$set('filtroTipo', '{{ $val }}')" type="button"
                        class="px-3 py-1 rounded-lg text-[11px] font-bold transition-all
                            {{ $filtroTipo === $val
                                ? 'bg-white text-black'
                                : 'bg-white/5 text-gray-400 hover:bg-white/10 border border-white/10' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- LISTA --}}
        <div class="overflow-y-auto flex-1 divide-y divide-white/5">
            @forelse($recursosFiltrados as $recurso)
                @php
                   $icono = match($recurso->tipo) {
    'material'    => ['bg' => 'bg-blue-500/20',   'text' => 'text-blue-400'],
    'labor'       => ['bg' => 'bg-green-500/20',  'text' => 'text-green-400'],
    'equipment'   => ['bg' => 'bg-orange-500/20', 'text' => 'text-orange-400'],
    'composition' => ['bg' => 'bg-purple-500/20', 'text' => 'text-purple-400'],
    default       => ['bg' => 'bg-gray-500/20',   'text' => 'text-gray-400'],
};
                    $yaAgregado = collect($items)->contains('recurso_id', $recurso->id);
                @endphp
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-white/[0.02] transition-colors">
                    <div class="w-9 h-9 rounded-xl {{ $icono['bg'] }} {{ $icono['text'] }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-[13px] font-bold uppercase tracking-wide truncate">{{ $recurso->nombre }}</p>
                        <p class="text-gray-500 text-[10px] uppercase">{{ $recurso->unidad }} · {{ str_replace(['material','labor','equipment','composition'], ['Material','Mano de Obra','Equipo','Composición'], $recurso->tipo) }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-white font-bold text-[13px]">USD {{ number_format($recurso->precio_usd, 2) }}</p>
                        <p class="text-gray-600 text-[9px] uppercase">Precio unit.</p>
                    </div>
                   <div class="flex items-center gap-2 flex-shrink-0" x-data="{ cant: 1 }">
    <input type="number" step="0.01" min="0.01"
        x-model="cant"
        x-on:click.stop=""
        class="w-16 bg-white/5 border border-white/10 rounded-lg px-2 py-1.5 text-white text-[12px] text-center focus:outline-none focus:border-purple-500/50">
    <button
        x-on:click="$wire.agregarItemConCantidad({{ $recurso->id }}, cant)"
        type="button"
        class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-all
            {{ $yaAgregado
                ? 'bg-green-500/20 text-green-400 cursor-default'
                : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-purple-600 hover:text-white hover:border-purple-500' }}">
        @if($yaAgregado)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        @else
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        @endif
    </button>
</div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-600 text-[13px]">
                    No se encontraron recursos
                </div>
            @endforelse
        </div>

        {{-- FOOTER --}}
        <div class="px-6 py-4 border-t border-white/5">
            <button wire:click="cerrarSelector" type="button"
                class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-[13px] font-bold transition-all">
                Confirmar selección ({{ count($items) }})
            </button>
        </div>
    </div>
</div>
@endif
</div>