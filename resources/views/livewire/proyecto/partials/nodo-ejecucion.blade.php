{{--
    Partial recursivo: nodo-ejecucion
    Variables:
      $nodo          - ProyectoRecurso actual
      $nivel         - Nivel de indentación (0 = rubro raíz, 1 = subrubro, etc.)
      $nodosAbiertos - Array de keys abiertas (compartido con presupuesto)
      $proyecto      - Proyecto actual
      $puedeEditar   - bool: puede ingresar costo real
--}}
@php
    $nivel        = $nivel ?? 0;
    $puedeEditar  = $puedeEditar ?? false;
    $nodeKey      = 'ej_' . $nodo->id;
    $nodoAbierto  = in_array($nodeKey, $nodosAbiertos ?? []);
    $hijos        = $nodo->hijos ?? collect([]);
    $esRecurso    = !is_null($nodo->recurso_id);

    // Indentación según nivel
    $padLeft = match(true) {
        $nivel <= 0 => '0px',
        $nivel === 1 => '16px',
        $nivel === 2 => '32px',
        default      => '48px',
    };

    // Mismo algoritmo que calcularSubtotalRecursivo en presupuesto:
    // computePerUnit propaga la cantidad de cada nodo padre hacia sus hijos.
    $computePerUnitEj = function($node) use (&$computePerUnitEj): float {
        $perUnit = (float)($node->precio_unitario ?? $node->precio_usd ?? 0);
        if ($node->hijos && $node->hijos->count() > 0) {
            foreach ($node->hijos as $child) {
                if (is_null($child->recurso_id)) {
                    $perUnit += $computePerUnitEj($child) * (float)($child->cantidad ?? 1);
                } else {
                    $perUnit += (float)($child->cantidad ?? 1) * (float)($child->precio_unitario ?? $child->precio_usd ?? 0);
                }
            }
        }
        return $perUnit;
    };

    // Presupuestado de este nodo (igual que la columna Total en presupuesto)
    $presupuestadoNodo = $computePerUnitEj($nodo) * (float)($nodo->cantidad ?? 1);
    $costoRealNodo = $nodo->costo_real;

    // Para subrubros/rubros: costo real = suma de hojas (no se multiplica por la jerarquía)
    if (!$esRecurso && $hijos->count() > 0) {
        $calcRealEj = function($nodos) use (&$calcRealEj): array {
            $real = 0; $tieneReal = false;
            foreach ($nodos as $n) {
                if (!is_null($n->recurso_id) && $n->costo_real !== null) {
                    $real += $n->costo_real;
                    $tieneReal = true;
                }
                if ($n->hijos && $n->hijos->count() > 0) {
                    [$sr, $st] = $calcRealEj($n->hijos);
                    if ($st) { $real += $sr; $tieneReal = true; }
                }
            }
            return [$real, $tieneReal];
        };
        [$realHijos, $tieneRealHijos] = $calcRealEj($hijos);
        $costoRealNodo = $tieneRealHijos ? $realHijos : null;
    }

    $diferenciaNodo = ($costoRealNodo !== null) ? ($costoRealNodo - $presupuestadoNodo) : null;
    $desvioPct      = ($diferenciaNodo !== null && $presupuestadoNodo > 0)
                        ? (($diferenciaNodo / $presupuestadoNodo) * 100)
                        : null;

    // Estilos visuales según nivel
    $bgFila = match(true) {
        !$esRecurso && $nivel === 0 => 'bg-white/[0.03]',
        !$esRecurso                 => 'bg-white/[0.015]',
        default                     => '',
    };
    $pesoTexto = !$esRecurso ? 'font-bold' : 'font-medium';
    $colorTexto = !$esRecurso ? 'text-gray-200' : 'text-gray-400';
@endphp

<div wire:key="ej-node-{{ $nodo->id }}">

    {{-- FILA --}}
    <div class="grid border-b border-white/[0.025] px-4 py-2 items-center hover:bg-white/[0.015] transition {{ $bgFila }}"
         style="grid-template-columns: 2fr 60px 90px 140px 160px 130px 90px;">

        {{-- Descripción --}}
        <div class="flex items-center gap-1.5 min-w-0" style="padding-left: {{ $padLeft }}">
            @if(!$esRecurso)
                {{-- Toggle propio del nodo --}}
                <div onclick="_lwToggle('{{ $nodeKey }}')" class="flex items-center gap-1.5 cursor-pointer min-w-0">
                    <svg id="chv-{{ $nodeKey }}"
                         class="w-2.5 h-2.5 text-gray-500 shrink-0"
                         style="transition:transform .2s; {{ $nodoAbierto ? 'transform:rotate(90deg)' : '' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-xs {{ $colorTexto }} {{ $pesoTexto }} uppercase truncate" title="{{ $nodo->nombre }}">
                        {{ $nodo->nombre }}
                    </span>
                </div>
                {{-- Botón Sub: abre/cierra todos los subrubros directos de este rubro --}}
                @php
                    $subRubrosEj  = $hijos->whereNull('recurso_id');
                    $subKeysEjN   = $subRubrosEj->map(fn($n) => 'ej_' . $n->id)->toArray();
                    $todosSubAbjN = count($subKeysEjN) > 0 && count(array_intersect($subKeysEjN, $nodosAbiertos ?? [])) === count($subKeysEjN);
                @endphp
                @if(count($subKeysEjN) > 0)
                <button wire:click.stop="toggleSubrubrosDeRubroEjecucion({{ $nodo->id }}, '{{ $nodeKey }}')"
                    onclick="_lwEnsureCatOpen('{{ $nodeKey }}')"
                    class="shrink-0 flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider transition
                        {{ $todosSubAbjN ? 'bg-white/10 text-gray-300 hover:bg-white/20' : 'bg-white/5 text-gray-500 hover:bg-white/10 hover:text-gray-300' }}">
                    @if($todosSubAbjN)
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                    @else
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                    @endif
                    Sub
                </button>
                @endif
            @else
                <div class="w-4 h-4 shrink-0 flex items-center justify-center">
                    <div class="w-1 h-1 rounded-full bg-gray-600"></div>
                </div>
                <span class="text-xs {{ $colorTexto }} {{ $pesoTexto }} uppercase truncate" title="{{ $nodo->nombre }}">
                    {{ $nodo->nombre }}
                </span>
            @endif
        </div>

        {{-- Unidad --}}
        <div class="text-xs text-gray-600 text-center uppercase font-mono">
            {{ $esRecurso ? ($nodo->unidad ?? '') : '' }}
        </div>

        {{-- Cantidad --}}
        <div class="text-xs text-gray-500 text-center font-mono">
            {{ $esRecurso ? number_format($nodo->cantidad ?? 1, 2) : '' }}
        </div>

        {{-- Presupuestado --}}
        <div class="text-right text-xs text-gray-400 font-mono font-semibold">
            {{ number_format($presupuestadoNodo, 2, ',', '.') }}
        </div>

        {{-- Costo Real (input solo en hojas) --}}
        <div class="flex justify-end pr-1">
            @if($esRecurso)
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0,00"
                    value="{{ $costoRealNodo !== null ? number_format($costoRealNodo, 2, '.', '') : '' }}"
                    wire:change="actualizarCostoReal({{ $nodo->id }}, $event.target.value)"
                    @disabled(!$puedeEditar)
                    class="w-32 bg-[#0a0a0a] border
                        {{ !$puedeEditar
                            ? 'border-gray-600/30 text-gray-600 cursor-not-allowed opacity-50'
                            : 'border-orange-500/30 text-orange-300 focus:border-orange-500' }}
                        rounded-lg px-2 py-1 text-xs font-mono text-right outline-none placeholder-gray-700 transition">
            @else
                <span class="text-xs font-mono text-right {{ $costoRealNodo !== null ? 'text-orange-300' : 'text-gray-700' }}">
                    {{ $costoRealNodo !== null ? number_format($costoRealNodo, 2, ',', '.') : '—' }}
                </span>
            @endif
        </div>

        {{-- Diferencia --}}
        <div class="text-right text-xs font-mono
            {{ $diferenciaNodo === null ? 'text-gray-700' :
               ($diferenciaNodo > 0 ? 'text-red-400' : ($diferenciaNodo < 0 ? 'text-green-400' : 'text-gray-500')) }}">
            @if($diferenciaNodo !== null)
                {{ ($diferenciaNodo >= 0 ? '+' : '') . number_format($diferenciaNodo, 2, ',', '.') }}
            @else
                —
            @endif
        </div>

        {{-- % Desvío --}}
        <div class="text-right text-xs font-black
            {{ $desvioPct === null ? 'text-gray-700' :
               ($desvioPct > 5 ? 'text-red-400' : ($desvioPct < -5 ? 'text-green-400' : 'text-yellow-400')) }}">
            @if($desvioPct !== null)
                {{ ($desvioPct >= 0 ? '+' : '') . number_format($desvioPct, 1) }}%
            @else
                —
            @endif
        </div>

    </div>

    {{-- HIJOS (colapsables) --}}
    @if(!$esRecurso && $hijos->count() > 0)
        <div id="children-{{ $nodeKey }}" style="{{ $nodoAbierto ? '' : 'display:none' }}">
            @foreach($hijos as $hijo)
                @include('livewire.proyecto.partials.nodo-ejecucion', [
                    'nodo'          => $hijo,
                    'nivel'         => $nivel + 1,
                    'nodosAbiertos' => $nodosAbiertos,
                    'proyecto'      => $proyecto,
                    'puedeEditar'   => $puedeEditar,
                ])
            @endforeach
        </div>
    @endif

</div>
