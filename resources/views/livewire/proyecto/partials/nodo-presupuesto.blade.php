{{--
    Partial recursivo: nodo-presupuesto
    Variables:
      $nodo            - ProyectoRecurso actual
      $nombreCategoria - Categoría raíz
      $nivel           - Nivel de indentación (1, 2, 3...)
      $nodosAbiertos   - Array de keys abiertas
      $modoLectura     - bool: ocultar botones de edición (opcional, default false)
--}}

@php
    $modoLectura  = $modoLectura ?? false;
    $nodeKey      = 'node_' . $nodo->id;
    $nodoAbierto  = in_array($nodeKey, $nodosAbiertos ?? []);
    $hijos        = $nodo->hijos ?? collect([]);
    $esRecurso    = !is_null($nodo->recurso_id);
    $subRubros    = $hijos->whereNull('recurso_id');
    $recursosDir  = $hijos->whereNotNull('recurso_id');
    $totalNodo    = ($nodo->precio_unitario ?? $nodo->precio_usd ?? 0) * $nodo->cantidad;
    $esComposicion = $esRecurso && $nodo->recurso?->tipo === 'composition';

    $indent = match($nivel) {
        1 => 'pl-6',
        2 => 'pl-12',
        3 => 'pl-20',
        4 => 'pl-28',
        default => 'pl-36',
    };

    $colorTexto = match($nivel) {
        1 => 'text-gray-200',
        2 => 'text-gray-300',
        3 => 'text-gray-400',
        default => 'text-gray-500',
    };

    $bgFila = match($nivel) {
        1 => 'bg-black/10',
        2 => 'bg-black/20',
        3 => 'bg-black/30',
        default => 'bg-black/40',
    };

    $dotColor = match($nivel) {
        1 => 'bg-purple-500/40',
        2 => 'bg-blue-500/40',
        3 => 'bg-white/20',
        default => 'bg-white/10',
    };

    // Carga directa de items si es composición abierta
    $itemsComposicion = ($esComposicion && $nodoAbierto && $nodo->recurso)
        ? \App\Models\ComposicionItem::with('recursoBase')
            ->where('composicion_id', $nodo->recurso->id)
            ->get()
        : collect();
@endphp

<div class="border-t border-white/[0.025]" wire:key="{{ 'node-' . $nodo->id }}">

    {{-- FILA DEL NODO --}}
    <div class="{{ $bgFila }} grid grid-cols-12 px-4 py-2.5 items-center group hover:brightness-110 transition-all">

        <div class="col-span-1"></div>

        <div class="col-span-5 flex items-center justify-between pr-2 {{ $indent }}">
            <div class="flex items-center gap-2 min-w-0 flex-1">

                {{-- Ícono izquierdo según tipo --}}
                @if(!$esRecurso)
                    <button wire:click="toggleNodo('{{ $nodeKey }}')" class="shrink-0">
                        <svg class="w-2.5 h-2.5 text-gray-600 transition-transform duration-200 {{ $nodoAbierto ? 'rotate-90' : '' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @elseif($esComposicion)
                    <button wire:click="toggleNodo('{{ $nodeKey }}')" class="shrink-0">
                        <svg class="w-2.5 h-2.5 text-amber-500 transition-transform duration-200 {{ $nodoAbierto ? 'rotate-90' : '' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @else
                    <div class="w-2.5 h-2.5 shrink-0 flex items-center justify-center">
                        <div class="w-1 h-1 rounded-full {{ $dotColor }}"></div>
                    </div>
                @endif

                {{-- Nombre + badge APU --}}
                <div class="flex items-center gap-1 min-w-0">
                    <p class="text-[11px] {{ $colorTexto }} font-semibold uppercase truncate">
                        {{ $nodo->nombre }}
                    </p>
                    @if($esComposicion)
                        <span class="shrink-0 text-[8px] font-black px-1.5 py-0.5 bg-amber-500/20 text-amber-400 rounded uppercase tracking-wide">APU</span>
                    @endif
                </div>
            </div>

            {{-- Botones icono-only (ocultos en modo lectura) --}}
            @if(!$modoLectura)
            <div class="flex items-center gap-0.5 shrink-0 ml-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button wire:click.stop="abrirModalEditar({{ $nodo->id }})"
                    title="Editar"
                    class="w-5 h-5 flex items-center justify-center bg-yellow-500/20 text-yellow-400 rounded hover:bg-yellow-500/40 transition">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button wire:click.stop="abrirModalEliminar({{ $nodo->id }})"
                    title="Eliminar"
                    class="w-5 h-5 flex items-center justify-center bg-red-500/20 text-red-400 rounded hover:bg-red-500/40 transition">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
                @if(!$esRecurso)
                <button wire:click.stop="abrirModalSubrubro({{ $nodo->id }}, '{{ $nombreCategoria }}', '{{ addslashes($nodo->nombre) }}')"
                    title="+ Sub-rubro"
                    class="w-5 h-5 flex items-center justify-center bg-purple-500/20 text-purple-400 rounded hover:bg-purple-500/40 transition">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </button>
                <button wire:click.stop="abrirModalRecursos({{ $nodo->id }}, '{{ $nombreCategoria }}', '{{ addslashes($nodo->nombre) }}')"
                    title="+ Recurso"
                    class="w-5 h-5 flex items-center justify-center bg-blue-500/20 text-blue-400 rounded hover:bg-blue-500/40 transition">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </button>
                @endif
            </div>
            @endif {{-- /!$modoLectura --}}

        </div>

        @if(!$esRecurso)
            {{-- Subrubro (con o sin hijos): sin cantidad, precio ni subtotal --}}
            <div class="col-span-6"></div>
        @else
            <div class="col-span-1 text-[10px] text-gray-600 text-center uppercase">{{ $nodo->unidad }}</div>

            <div class="col-span-1 flex justify-center">
                @if(!$modoLectura)
                <input type="number"
                       wire:change="updateCantidad({{ $nodo->id }}, $event.target.value)"
                       value="{{ $nodo->cantidad }}"
                       step="0.01"
                       class="w-16 bg-[#0a0a0a] border border-white/5 rounded px-1 py-0.5 text-[10px] text-center text-white font-bold focus:border-white/20 focus:outline-none">
                @else
                <span class="text-[10px] text-gray-500 font-mono">{{ number_format($nodo->cantidad, 2) }}</span>
                @endif
            </div>

            <div class="col-span-2 text-center text-[10px] text-gray-600 font-mono">
                {{ number_format($nodo->precio_unitario ?? $nodo->precio_usd ?? 0, 2, ',', '.') }}
            </div>

            <div class="col-span-2 text-right text-xs font-bold {{ $colorTexto }} font-mono">
                {{ number_format($totalNodo, 2, ',', '.') }}
            </div>
        @endif
    </div>

    {{-- HIJOS recursivos (solo sub-rubros) --}}
    @if(!$esRecurso && $nodoAbierto && $hijos->count() > 0)
        <div>
            @foreach($hijos as $hijo)
                @include('livewire.proyecto.partials.nodo-presupuesto', [
                    'nodo'            => $hijo,
                    'nombreCategoria' => $nombreCategoria,
                    'nivel'           => $nivel + 1,
                    'nodosAbiertos'   => $nodosAbiertos,
                    'modoLectura'     => $modoLectura,
                ])
            @endforeach
        </div>
    @endif

    {{-- DETALLE DE COMPOSICIÓN (APU) --}}
    @if($esComposicion && $nodoAbierto && $itemsComposicion->count())
        <div class="px-8 pb-4">
            <div class="bg-[#0f0f0f] border border-white/5 rounded-lg overflow-hidden">

                {{-- Encabezado --}}
                <div class="grid grid-cols-12 px-4 py-2 bg-white/5 border-b border-white/5 items-center">
                    <div class="col-span-6">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                            Análisis de Precios Unitarios (APU)
                        </span>
                    </div>
                   
                </div>

                {{-- Cabecera columnas --}}
                <div class="grid grid-cols-12 px-4 py-1.5 bg-black/40 border-b border-white/[0.03]">
                    <div class="col-span-6 text-[9px] text-gray-500 uppercase">Recurso</div>
                    <div class="col-span-1 text-[9px] text-gray-500 uppercase text-center">Cant.</div>
                    <div class="col-span-2 text-[9px] text-gray-500 uppercase text-center">Carga Social</div>
                    <div class="col-span-1 text-[9px] text-gray-500 uppercase text-center">P. Unit</div>
                    <div class="col-span-2 text-[9px] text-gray-500 uppercase text-right">Subtotal</div>
                </div>

                {{-- Filas agrupadas por tipo --}}
                @foreach($itemsComposicion->filter(fn($i) => !is_null($i->recursoBase))->groupBy(fn($i) => $i->recursoBase->tipo) as $tipo => $items)
                    <div class="border-b border-white/[0.02] last:border-0">

                        {{-- Título del grupo --}}
                        <div class="px-4 py-1 bg-white/[0.02]">
                            <span class="text-[10px] font-bold text-gray-400 uppercase italic">
                                {{ match($tipo) {
                                    'material'  => 'Materiales',
                                    'labor'     => 'Mano de Obra',
                                    'equipment' => 'Equipos',
                                    default     => $tipo,
                                } }}
                            </span>
                        </div>

                        {{-- Items --}}
                        @foreach($items as $item)
                            @if($item->recursoBase)
                                @php
                                    $base         = $item->recursoBase;
                                    $pUnit        = $base->precio_usd ?? 0;
                                    $esLabor      = in_array($base->tipo, ['labor', 'mano_obra']);
                                    $cargaSocial  = $esLabor ? ($pUnit * (($base->social_charges_percentage ?? 0) / 100)) : 0;
                                    $subtotalItem = $item->cantidad * ($pUnit + $cargaSocial);
                                @endphp
                                <div class="grid grid-cols-12 px-4 py-2 items-center hover:bg-white/[0.02] transition">
                                    <div class="col-span-6 pl-4 text-[10px] text-gray-400 italic">
                                        {{ $base->nombre }}
                                    </div>
                                    <div class="col-span-1 text-center text-[10px] text-white">
                                        {{ number_format($item->cantidad, 2) }}
                                    </div>
                                    <div class="col-span-2 text-center text-[10px] text-gray-500">
                                        {{ $cargaSocial > 0 ? number_format($cargaSocial, 2) : '-' }}
                                    </div>
                                    <div class="col-span-1 text-center text-[10px] text-gray-500">
                                        {{ number_format($pUnit, 2) }}
                                    </div>
                                    <div class="col-span-2 text-right text-[10px] text-gray-300 font-bold">
                                        {{ number_format($subtotalItem, 2) }}
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    </div>
                @endforeach

            </div>
        </div>
    @endif

</div>