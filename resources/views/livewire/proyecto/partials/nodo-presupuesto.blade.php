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
    $esLabor       = $esRecurso && in_array($nodo->recurso?->tipo, ['labor', 'mano_obra']);
    $pctCSGlobal   = isset($proyecto) ? (float)($proyecto->carga_social ?? 0) : 0;
    $pctCSRecurso  = $esLabor ? (float)($nodo->recurso?->social_charges_percentage ?? 0) : 0;
    $csAplicada    = $esLabor ? ($pctCSGlobal > 0 ? $pctCSGlobal : $pctCSRecurso) : 0;
    $montoCS       = $esLabor && $csAplicada > 0
                        ? (($nodo->precio_unitario ?? $nodo->precio_usd ?? 0) * ($csAplicada / 100) * ($nodo->cantidad ?? 1))
                        : 0;
    $nivel = $nivel ?? 0;
    $bgFila = $bgFila ?? '';
    $colorTexto = $colorTexto ?? 'text-white';
    $dotColor = $dotColor ?? 'bg-gray-400';
    $itemsComposicionLocal = collect($nodo->recurso?->items ?? []);

    // compute per unit recursively (sum of contents per single unidad)
    $computePerUnit = function ($node) use (&$computePerUnit) {
        $precioPropio = $node->precio_usd ?? $node->precio_unitario ?? 0;

        if (!empty($node->composicion_items) && count($node->composicion_items)) {
            $apuTotal = 0;
            foreach ($node->composicion_items as $ci) {
                $apuTotal += ($ci->cantidad * ($ci->recurso->precio_unitario ?? 0));
            }
            $precioPropio += $apuTotal;
        }

        if (!empty($node->hijos) && count($node->hijos)) {
            foreach ($node->hijos as $h) {
                if (($h->es_recurso ?? false)) {
                    // child is a resource: its contribution to parent's per-unit is child.cantidad * child.unit_price
                    $precioPropio += ($h->cantidad ?? 1) * ($h->precio_usd ?? $h->precio_unitario ?? 0);
                } else {
                    // child is subrubro: its per-unit value contributes multiplied by its own cantidad
                    $precioPropio += $computePerUnit($h) * ($h->cantidad ?? 1);
                }
            }
        }

        return $precioPropio;
    };

    // per-unit and subtotal displayed in row
    $perUnitNodo = $computePerUnit($nodo);
    $subtotalNodo = $perUnitNodo * ($nodo->cantidad ?? 1);

    // indentation classes based on level
    $indent = match($nivel) {
        0 => 'pl-0',
        1 => 'pl-3',
        default => 'pl-6',
    };

    // Carga social del nodo: si es labor → su propio monto; si es subrubro → suma de hijos; si es composición → suma de mano de obra interna
    $calcCSHijos = function($nodos, float $mult = 1) use (&$calcCSHijos, $proyecto) {
        $total = 0;
        $pctGlobal = isset($proyecto) ? $proyecto->carga_social : null;
        foreach ($nodos as $n) {
            $esLaborN = !is_null($n->recurso_id) && in_array($n->recurso?->tipo, ['labor', 'mano_obra']);
            if ($esLaborN) {
                $pct    = !is_null($pctGlobal)
                    ? (float)$pctGlobal
                    : (float)($n->recurso?->social_charges_percentage ?? 0);
                $precio = $n->precio_unitario ?? $n->precio_usd ?? 0;
                $total += $precio * ($pct / 100) * ($n->cantidad ?? 1) * $mult;
            }
            if ($n->hijos && $n->hijos->count() > 0) {
                $total += $calcCSHijos($n->hijos, $mult * ($n->cantidad ?? 1));
            }
        }
        return $total;
    };

    // Si es composición, sumar CS de items labor internos
    if ($esLabor) {
        $csNodo = $montoCS;
    } elseif ($esComposicion) {
        $csNodo = 0;
        foreach ($itemsComposicionLocal as $item) {
            $base = $item->recursoBase;
            if ($base && in_array($base->tipo, ['labor', 'mano_obra'])) {
                $pct = !is_null($proyecto->carga_social)
                    ? (float)$proyecto->carga_social
                    : (float)($base->social_charges_percentage ?? 0);
                $pUnit = $base->precio_usd ?? 0;
                $csNodo += $pUnit * ($pct / 100) * $item->cantidad * ($nodo->cantidad ?? 1);
            }
        }
    } elseif (!$esRecurso) {
        $csNodo = $calcCSHijos($hijos);
    } else {
        $csNodo = 0;
    }
@endphp

<div class="border-t border-white/[0.025]" wire:key="{{ 'node-' . $nodo->id }}">

    {{-- FILA DEL NODO --}}
    <div class="{{ $bgFila }} grid grid-cols-12 px-3 py-1.5 items-center group hover:brightness-110 transition-all {{ !$modoLectura ? 'cursor-grab active:cursor-grabbing' : '' }}"
         data-node-id="{{ $nodo->id }}"
         data-parent-id="{{ $nodo->parent_id ?? '' }}"
         @if(!$modoLectura) draggable="true" @endif>

        <div class="col-span-1"></div>

        <div class="col-span-4 flex items-center justify-between pr-2 {{ $indent }}">
            <div class="flex items-center gap-2 min-w-0 flex-1">

                {{-- Ícono izquierdo según tipo --}}
                @if(!$esRecurso)
                    <button onclick="_lwToggle('{{ $nodeKey }}')" class="shrink-0">
                        <svg id="chv-{{ $nodeKey }}" class="w-3.5 h-3.5 text-gray-600"
                             style="transition:transform .2s;{{ $nodoAbierto ? 'transform:rotate(90deg)' : '' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @elseif($esComposicion)
                    <button onclick="_lwToggle('{{ $nodeKey }}')" class="shrink-0">
                        <svg id="chv-{{ $nodeKey }}" class="w-3.5 h-3.5 text-amber-500"
                             style="transition:transform .2s;{{ $nodoAbierto ? 'transform:rotate(90deg)' : '' }}"
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
                <div class="flex items-center gap-1 min-w-0 flex-1 {{ (!$esRecurso || $esComposicion) ? 'cursor-pointer' : '' }}"
                    @if(!$esRecurso || $esComposicion) onclick="_lwToggle('{{ $nodeKey }}')" @endif>
                    <p class="text-xs {{ $colorTexto }} font-semibold uppercase truncate">
                        {{ $nodo->nombre }}
                    </p>
                    @if($esLabor && $csAplicada > 0)
                        <span class="shrink-0 text-[9px] font-bold text-blue-300/70 bg-blue-500/10 border border-blue-500/20 rounded px-1 leading-4 whitespace-nowrap">
                            CS {{ number_format($csAplicada, 1) }}%
                        </span>
                    @endif
                </div>
                {{-- Drag handle --}}
                @if(!$modoLectura)
                <div data-drag-handle data-node-id="{{ $nodo->id }}"
                     title="Arrastrar para reordenar"
                     class="w-5 h-5 flex items-center justify-center text-gray-600 hover:text-gray-300 cursor-grab active:cursor-grabbing shrink-0 transition-colors select-none">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7 4a1 1 0 110-2 1 1 0 010 2zm6 0a1 1 0 110-2 1 1 0 010 2zM7 9a1 1 0 110-2 1 1 0 010 2zm6 0a1 1 0 110-2 1 1 0 010 2zM7 14a1 1 0 110-2 1 1 0 010 2zm6 0a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                </div>
                @endif

                {{-- Copy --}}
                @if(!$modoLectura)
                <button wire:click.stop="copiarNodo({{ $nodo->id }})"
                    title="Copiar nodo"
                    class="w-5 h-5 flex items-center justify-center rounded transition shrink-0
                        {{ isset($nodoCopiadoId) && $nodoCopiadoId == $nodo->id
                            ? 'bg-purple-500/30 text-purple-300'
                            : 'bg-white/10 text-gray-400 hover:bg-white/20' }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
                @endif

                {{-- Paste (solo cuando hay algo copiado) --}}
                @if(!$modoLectura && isset($nodoCopiadoId) && $nodoCopiadoId)
                <button wire:click.stop="pegarNodo({{ $nodo->id }})"
                    title="Pegar aquí (como hermano)"
                    class="w-5 h-5 flex items-center justify-center bg-purple-500/20 text-purple-400 rounded hover:bg-purple-500/40 transition shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </button>
                @endif
                @if(!$esRecurso)
                <button wire:click.stop="abrirModalSubrubro({{ $nodo->id }}, '{{ $nombreCategoria }}', '{{ addslashes($nodo->nombre) }}')"
                    title="+ Sub-rubro"
                    class="w-5 h-5 flex items-center justify-center bg-purple-500/20 text-purple-400 rounded hover:bg-purple-500/40 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </button>
                <button wire:click.stop="abrirModalRecursos({{ $nodo->id }}, '{{ $nombreCategoria }}', '{{ addslashes($nodo->nombre) }}')"
                    title="+ Recurso"
                    class="w-5 h-5 flex items-center justify-center bg-blue-500/20 text-blue-400 rounded hover:bg-blue-500/40 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </button>
                @endif
                <button wire:click.stop="abrirModalEditar({{ $nodo->id }})"
                    title="Editar"
                    class="w-5 h-5 flex items-center justify-center bg-yellow-500/20 text-yellow-400 rounded hover:bg-yellow-500/40 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button wire:click.stop="abrirModalEliminar({{ $nodo->id }})"
                    title="Eliminar"
                    class="w-5 h-5 flex items-center justify-center bg-red-500/20 text-red-400 rounded hover:bg-red-500/40 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>

        </div>

        @php
            // determine display per-unit: for resource nodes use explicit price, otherwise use computed per-unit
            if ($esRecurso && !$esComposicion) {
                $perUnitMostrar = $nodo->precio_unitario ?? $nodo->precio_usd ?? 0;
            } else {
                $perUnitMostrar = $perUnitNodo ?? $computePerUnit($nodo);
            }
        @endphp

        <div class="col-span-1 text-xs text-gray-600 text-center uppercase">
            {{ $nodo->unidad }}
        </div>

        <div class="col-span-1 flex justify-center">
            @if(!$modoLectura)
                <input type="number"
                       wire:change="updateCantidad({{ $nodo->id }}, $event.target.value)"
                       value="{{ $nodo->cantidad }}"
                       step="0.01"
                       class="w-14 bg-[#0a0a0a] border border-white/5 rounded px-1 py-0.5 text-xs text-center text-white font-bold focus:border-white/20 focus:outline-none">
            @else
                <span class="text-xs text-gray-500 font-mono">{{ number_format($nodo->cantidad, 2) }}</span>
            @endif
        </div>

        <div class="col-span-2 text-center text-xs text-gray-600 font-mono">
            {{ number_format($perUnitMostrar, 2, ',', '.') }}
        </div>

        <div class="col-span-1 text-center text-xs font-mono {{ $csNodo > 0 ? 'text-blue-400' : 'text-gray-700' }}">
            {{ $csNodo > 0 ? number_format($csNodo, 2, ',', '.') : '—' }}
        </div>

        <div class="col-span-2 text-right text-xs font-bold {{ $colorTexto }} font-mono">
            {{ number_format($subtotalNodo, 2, ',', '.') }}
        </div>
    </div>

    {{-- HIJOS recursivos (solo sub-rubros) --}}
    @if(!$esRecurso && $hijos->count() > 0)
        <div id="children-{{ $nodeKey }}" style="{{ $nodoAbierto ? '' : 'display:none' }}">
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
    @if($esComposicion && $itemsComposicionLocal->count())
        <div id="children-{{ $nodeKey }}-apu" class="px-8 pb-4" style="{{ $nodoAbierto ? '' : 'display:none' }}">
            <div class="bg-[#0f0f0f] border border-white/5 rounded-lg overflow-hidden">

                {{-- Encabezado --}}
                <div class="grid grid-cols-12 px-4 py-2 bg-white/5 border-b border-white/5 items-center">
                    <div class="col-span-6">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-widest">
                            Análisis de Precios Unitarios (APU)
                        </span>
                    </div>
                    <div class="col-span-6 flex items-center justify-end gap-3">
                        <span class="text-sm text-gray-400 font-bold">
                            Cant: {{ number_format($nodo->cantidad ?? 1, 2) }} — P.Unit: {{ number_format($perUnitNodo ?? 0, 2, ',', '.') }}
                        </span>
                        @if($csNodo > 0)
                            <span class="text-xs font-bold text-blue-300/70 bg-blue-500/10 border border-blue-500/20 rounded px-2 py-1 ml-2">
                                C. Social total: {{ number_format($csNodo, 2, ',', '.') }}
                            </span>
                        @endif
                        @if(!$modoLectura)
                        <button wire:click="abrirModalRecursosParaApu({{ $nodo->recurso_id }}, '{{ addslashes($nodo->nombre) }}')"
                            title="Agregar recurso al APU"
                            class="flex items-center gap-1 px-2 py-1 rounded bg-purple-500/20 text-purple-400 hover:bg-purple-500/30 text-xs font-bold transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Cabecera columnas --}}
                <div class="grid grid-cols-12 px-4 py-1.5 bg-black/40 border-b border-white/[0.03]">
                    <div class="col-span-5 text-xs text-gray-500 uppercase">Recurso</div>
                    <div class="col-span-1 text-xs text-gray-500 uppercase text-center">Cant.</div>
                    <div class="col-span-2 text-xs text-gray-500 uppercase text-center">Carga Social</div>
                    <div class="col-span-1 text-xs text-gray-500 uppercase text-center">P. Unit</div>
                    <div class="col-span-2 text-xs text-gray-500 uppercase text-right">Subtotal</div>
                    <div class="col-span-1"></div>
                </div>

                {{-- Filas agrupadas por tipo --}}
                @foreach($itemsComposicionLocal->filter(fn($i) => !is_null($i->recursoBase))->groupBy(fn($i) => $i->recursoBase->tipo) as $tipo => $items)
                    <div class="border-b border-white/[0.02] last:border-0">

                        {{-- Título del grupo --}}
                        <div class="px-4 py-1 bg-white/[0.02]">
                            <span class="text-sm font-bold text-gray-400 uppercase italic">
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
                                    $subtotalItem = ($nodo->cantidad ?? 1) * $item->cantidad * $pUnit;
                                    $cantidadMostrada = $item->cantidad * ($nodo->cantidad ?? 1);
                                @endphp
                                <div class="grid grid-cols-12 px-4 py-2 items-center hover:bg-white/[0.02] transition group/apuitem">
                                    <div class="col-span-5 pl-4 text-sm text-gray-400 italic">
                                        {{ $base->nombre }}
                                    </div>
                                    <div class="col-span-1 text-center text-sm text-white">
                                        {{ number_format($cantidadMostrada, 2) }}
                                    </div>
                                    <div class="col-span-2 text-center text-sm text-gray-500">
                                        {{ $cargaSocial > 0 ? number_format($cargaSocial, 2) : '-' }}
                                    </div>
                                    <div class="col-span-1 text-center text-sm text-gray-500">
                                        {{ number_format($pUnit, 2) }}
                                    </div>
                                    <div class="col-span-2 text-right text-sm text-gray-300 font-bold">
                                        {{ number_format($subtotalItem, 2) }}
                                    </div>
                                    {{-- Acciones (solo si no es modo lectura) --}}
                                    <div class="col-span-1 flex items-center justify-end gap-1 opacity-0 group-hover/apuitem:opacity-100 transition-opacity">
                                        @if(!$modoLectura)
                                        <button wire:click="abrirModalEditarItemApu({{ $item->id }}, {{ $nodo->cantidad ?? 1 }})"
                                            title="Editar"
                                            class="p-1 rounded text-gray-500 hover:text-yellow-400 hover:bg-yellow-500/10 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="abrirModalEliminarItemApu({{ $item->id }})"
                                            title="Eliminar"
                                            class="p-1 rounded text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                        @endif
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
