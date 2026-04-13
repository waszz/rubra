<div class="h-screen flex flex-col overflow-hidden bg-[#0a0a0a]">

{{-- NAVBAR --}}
<nav class="shrink-0 border-b border-white/5 bg-[#0d0d0d]">

    {{-- Fila superior: back + nombre + acciones --}}
    <div class="flex items-center justify-between px-3 py-1.5 gap-2">

        {{-- IZQUIERDA: back + nombre --}}
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-white transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-white font-black text-xs uppercase tracking-widest truncate">{{ $proyecto->nombre_proyecto }}</h1>
                    <span class="bg-green-500/10 text-green-500 text-[10px] font-black px-1.5 py-0 rounded border border-green-500/20 flex items-center gap-1 shrink-0">
                        <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span> ONLINE
                    </span>
                </div>
                <p class="text-[10px] text-gray-600 uppercase tracking-widest font-bold">PRESUPUESTO DETALLADO ▾</p>
            </div>
        </div>

        {{-- DERECHA: undo/redo + usuarios + compartir --}}
        <div class="flex items-center gap-2 shrink-0">

            {{-- Undo / Redo --}}
            <div class="flex items-center gap-0.5 bg-white/5 rounded-lg p-0.5 border border-white/5">
                <button
                    wire:click="deshacer"
                    {{ $indexHistorial <= 0 ? 'disabled' : '' }}
                    class="p-1 rounded {{ $indexHistorial <= 0 ? 'text-gray-600 cursor-not-allowed' : 'text-gray-500 hover:text-white hover:bg-white/10' }} transition-all"
                    title="Deshacer (Ctrl+Z)">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </button>
                <button
                    wire:click="rehacer"
                    {{ $indexHistorial >= count($historialEstados) - 1 ? 'disabled' : '' }}
                    class="p-1 rounded {{ $indexHistorial >= count($historialEstados) - 1 ? 'text-gray-600 cursor-not-allowed' : 'text-gray-500 hover:text-white hover:bg-white/10' }} transition-all"
                    title="Rehacer (Ctrl+Y)">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6"/>
                    </svg>
                </button>
            </div>

            {{-- Avatares (ocultos en xs) --}}
                <div class="hidden sm:flex items-center">
                <div class="flex -space-x-1.5">
                    @foreach($proyecto->usuarios as $user)
                        <div class="w-5 h-5 rounded-full bg-purple-500 border-2 border-[#0d0d0d] flex items-center justify-center text-[10px] font-black text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endforeach
                </div>
                @if(auth()->id() === $proyecto->user_id)
                    <button wire:click="abrirModalInvitar" class="ml-1.5 text-sm font-black text-gray-600 hover:text-white transition-colors uppercase">
                        + Invitar
                    </button>
                @endif
            </div>

            {{-- Compartir (oculto en xs) --}}
            @if(auth()->id() === $proyecto->user_id)
            <button wire:click="abrirModalCompartir" class="hidden sm:flex items-center gap-1 px-2 py-1 rounded-lg bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 text-[11px] font-black uppercase tracking-wider transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                Compartir
            </button>
            @endif

        </div>
    </div>

    {{-- Fila inferior: tabs de navegación (scroll horizontal en mobile) --}}
    <div class="flex items-center gap-0.5 px-3 pb-1 overflow-x-auto scrollbar-none">
        @foreach([
            ['label' => 'Presupuesto', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z', 'active' => true],
            ['label' => 'Gantt',        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'active' => false, 'route' => route('proyectos.gantt', $proyecto)],
            ['label' => 'Diario',       'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'active' => false, 'route' => route('proyectos.diario', $proyecto)],
            ['label' => 'Bitácora',     'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'active' => false, 'route' => route('proyectos.bitacora', $proyecto)],
            ['label' => 'Estadísticas', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'active' => false, 'route' => route('estadisticas', ['proyectoId' => $proyecto->id])],
        ] as $tab)
            @if(($tab['label'] === 'Gantt' && !auth()->user()->puede('mapa')) || ($tab['label'] === 'Estadísticas' && !auth()->user()->puede('estadisticas')))
                @continue
            @endif
            <a href="{{ $tab['route'] ?? '#' }}"
               class="flex items-center gap-1 px-2 py-1 rounded text-[11px] font-black uppercase tracking-wider transition-all whitespace-nowrap shrink-0
                   {{ $tab['active'] ? 'bg-white text-black' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                </svg>
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

</nav>

{{-- BARRA DE HERRAMIENTAS --}}
<div class="shrink-0 relative z-30 flex flex-col sm:flex-row sm:items-center gap-1.5 px-3 py-1.5 border-b border-gray-200 dark:border-white/5 bg-white/80 dark:bg-[#0d0d0d]/80 backdrop-blur-sm text-black dark:text-white">

    {{-- IZQUIERDA: toggle presupuesto/ejecución + buscador --}}
    <div class="flex items-center gap-2 flex-wrap">
        
        {{-- Toggle Presupuesto / Ejecución --}}
        <div class="flex items-center bg-gray-100 dark:bg-white/5 rounded-lg p-0.5 border border-gray-200 dark:border-white/5">
            <button
                wire:click="cambiarVista('presupuesto')"
                class="px-2 py-0.5 rounded text-[11px] font-black uppercase tracking-wider transition-all {{ $vistaActiva === 'presupuesto' ? 'bg-white text-black dark:bg-white/10 dark:text-white' : 'text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
                Presupuesto
            </button>
            <button
                wire:click="cambiarVista('ejecucion')"
                @disabled(in_array($proyecto->estado_obra, ['en_revision', 'activo', 'pausado']))
                class="px-2 py-0.5 rounded text-[11px] font-black uppercase tracking-wider transition-all {{ $vistaActiva === 'ejecucion' ? 'bg-orange-500 text-white' : 'text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-white' }} {{ in_array($proyecto->estado_obra, ['en_revision', 'activo', 'pausado']) ? 'opacity-50 cursor-not-allowed' : '' }}">
                Ejecución
            </button>
        </div>

        {{-- % Carga Social --}}
        <div class="flex items-center gap-1.5 bg-blue-500/10 border border-blue-500/30 rounded-lg px-2 py-1">
            <span class="text-[10px] text-blue-300 font-bold uppercase tracking-wider whitespace-nowrap">C. Social</span>
            <input type="number"
                   wire:change="actualizarCargaSocial($event.target.value)"
                   value="{{ number_format($proyecto->carga_social ?? 0, 1, '.', '') }}"
                   min="0" max="100" step="0.5"
                   class="w-16 rounded bg-white/10 border border-blue-500/40 text-blue-100 text-xs font-bold text-center outline-none focus:border-blue-400 px-1 py-0.5"
                   title="Porcentaje global de carga social">
            <span class="text-[10px] text-blue-300 font-bold">%</span>
        </div>

        {{-- Buscador --}}
        <div class="relative flex-1 sm:flex-none">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
                 <input type="text"
                     placeholder="Filtrar rubros..."
                     class="pl-7 pr-3 py-1 bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/5 rounded-lg text-xs text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 outline-none focus:border-gray-300 dark:focus:border-white/20 w-full sm:w-40 transition-all">
        </div>

        {{-- Botón Agregar Rubro --}}
        @if(!$modoLectura && $vistaActiva === 'presupuesto' && !in_array($proyecto->estado_obra, ['ejecucion', 'en_ejecucion']))
        <button wire:click="abrirModalRubro"
            class="flex items-center gap-1 px-2 py-1 rounded-lg bg-purple-500/20 dark:bg-purple-500/10 border border-purple-500/30 text-purple-700 dark:text-purple-400 hover:bg-purple-500/30 dark:hover:bg-purple-500/20 text-[11px] font-black uppercase tracking-wider transition-all">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Agregar Rubro
        </button>
        <button wire:click="abrirModalEliminarTodo"
            class="flex items-center gap-1 px-2 py-1 rounded-lg bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 hover:bg-red-500/20 text-[11px] font-black uppercase tracking-wider transition-all">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Eliminar Todo
        </button>
        @endif
    </div>

    {{-- DERECHA: beneficio + exportar --}}
    <div class="flex items-center gap-2 sm:ml-auto flex-wrap">

        {{-- Toggle Beneficio --}}
        <button
            wire:click="toggleBeneficio"
            class="flex items-center gap-1 px-2 py-1 rounded-lg border text-[11px] font-black uppercase tracking-wider transition-all
                {{ $mostrarBeneficio
                    ? 'bg-green-500/20 dark:bg-green-500/10 border-green-500/30 text-green-700 dark:text-green-400' 
                    : 'bg-gray-100 dark:bg-white/5 border-gray-200 dark:border-white/5 text-gray-700 dark:text-gray-400' }}">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Beneficio ({{ number_format($proyecto->beneficio ?? 0, 0) }}%)
        </button>

        {{-- Dropdown Exportar / Importar --}}
        <div class="relative">
            <button wire:click="toggleDropdownExportar" class="flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/5 text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-white hover:bg-gray-200 dark:hover:bg-white/10 text-[11px] font-black uppercase tracking-wider transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar
            </button>

            {{-- Dropdown Menu --}}
            @if($mostrarDropdownExportar)
            <div class="absolute top-full right-0 mt-2 bg-[#1a1a1a] border border-gray-700 rounded-lg shadow-2xl z-50 min-w-[200px] overflow-hidden">
                <button wire:click="abrirModalPDF" class="w-full flex items-center gap-2 px-4 py-2.5 text-white hover:bg-white/10 transition-all text-left text-sm font-black uppercase tracking-wider border-b border-gray-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    PDF
                </button>
                @if(auth()->user()->plan !== 'gratis')
                <button wire:click="abrirModalExcel" class="w-full flex items-center gap-2 px-4 py-2.5 text-white hover:bg-white/10 transition-all text-left text-sm font-black uppercase tracking-wider border-b border-gray-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </button>
                @endif
                <button wire:click="abrirModalImportarPresupuesto" class="w-full flex items-center gap-2 px-4 py-2.5 text-orange-400 hover:bg-orange-500/10 transition-all text-left text-sm font-black uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Importar Presupuesto
                </button>
            </div>
            @endif
        </div>

    </div>
</div>

   <div class="flex-1 flex min-h-0 overflow-hidden">

{{-- ═══ PANEL PRESUPUESTO (ancho arrastrable) ═══ --}}
<div id="budget-panel" class="flex flex-col min-h-0 overflow-hidden" style="flex:1 1 0%">

{{-- PANEL SUPERIOR: banner + stats (colapsable / arrastrable) --}}
<div id="stats-panel" class="shrink-0 px-6 pt-3 overflow-hidden" style="height:130px">

{{-- BANNER MODO LECTURA --}}
@if(($modoLectura || in_array($proyecto->estado_obra, ['ejecucion', 'en_ejecucion'])) && $vistaActiva === 'presupuesto')
<div class="flex items-center gap-3 {{ $proyecto->estado_obra === 'finalizado' ? 'bg-gray-500/10 border border-gray-500/20' : 'bg-orange-500/10 border border-orange-500/20' }} rounded-xl px-4 py-3">
    <svg class="w-4 h-4 {{ $proyecto->estado_obra === 'finalizado' ? 'text-gray-400' : 'text-orange-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
    <div>
        @if($proyecto->estado_obra === 'finalizado')
            <p class="text-gray-400 font-black text-sm uppercase tracking-widest">Proyecto finalizado — Presupuesto bloqueado</p>
            <p class="text-gray-500 text-sm">El proyecto ha terminado. No se pueden realizar modificaciones. Solo modo lectura.</p>
        @elseif($proyecto->estado_obra === 'pausado')
            <p class="text-orange-400 font-black text-sm uppercase tracking-widest">Proyecto pausado — Presupuesto bloqueado</p>
            <p class="text-gray-500 text-sm">El proyecto está pausado. No se pueden realizar modificaciones en el presupuesto.</p>
        @else
            <p class="text-orange-400 font-black text-sm uppercase tracking-widest">Proyecto en ejecución — Presupuesto bloqueado</p>
            <p class="text-gray-500 text-sm">El presupuesto es solo lectura. Usá la vista de <strong class="text-orange-400">Ejecución</strong> para registrar costos reales.</p>
        @endif
    </div>
</div>
@endif

{{-- CARDS TOTALES ACTUALIZADAS --}}
@php
    // Función recursiva para calcular subtotal de todos los recursos en el árbol
    // Ahora calcula el costo "por unidad" de cada nodo y luego multiplica por su cantidad,
    // de forma que la cantidad de un subrubro multiplique lo que tenga agregado.
    function calcularSubtotalRecursivo($nodos) {
        $computePerUnit = function($node) use (&$computePerUnit) {
            $perUnit = 0;
            $precioUnitario = $node->precio_unitario ?? $node->precio_usd ?? 0;
            $perUnit += $precioUnitario;

            // Hijos: su contribución se agrega en función de su cantidad por unidad.
            // Para APUs (compositions), precio_usd ya incluye el costo total de sus items
            // (asignado al crear la composición), por lo que no se re-expanden aquí.
            if ($node->hijos && $node->hijos->count() > 0) {
                foreach ($node->hijos as $child) {
                    if (is_null($child->recurso_id)) {
                        $perUnit += $computePerUnit($child) * ($child->cantidad ?? 1);
                    } else {
                        $pChild = $child->precio_unitario ?? $child->precio_usd ?? 0;
                        $cantChild = $child->cantidad ?? 1;
                        $perUnit += $cantChild * $pChild;
                    }
                }
            }

            return $perUnit;
        };

        $total = 0;
        foreach ($nodos as $nodo) {
            $total += ($nodo->cantidad ?? 1) * $computePerUnit($nodo);
        }

        return $total;
    }

    // Función recursiva para calcular carga social
    // $pctGlobal: si > 0 sobreescribe el % individual de cada recurso (viene de $proyecto->carga_social)
    function calcularCargaSocialRecursiva($nodos, float $multiplier = 1, float $pctGlobal = 0) {
        $totalCS = 0;
        foreach ($nodos as $nodo) {
            $cantNodo       = $nodo->cantidad ?? 1;
            $precioUnitario = $nodo->precio_unitario ?? $nodo->precio_usd ?? 0;

            if (($nodo->recurso && $nodo->recurso->tipo === 'labor') || $nodo->tipo === 'labor') {
                $porcentajeCS = $pctGlobal > 0
                    ? $pctGlobal
                    : ($nodo->recurso->social_charges_percentage ?? $nodo->social_charges_percentage ?? 0);
                $totalCS += $multiplier * $cantNodo * $precioUnitario * ($porcentajeCS / 100);
            }

            if ($nodo->recurso && $nodo->recurso->tipo === 'composition') {
                $itemsInternos = \App\Models\ComposicionItem::where('composicion_id', $nodo->recurso_id)->get();
                foreach ($itemsInternos as $interno) {
                    $resBase = $interno->recursoBase;
                    if (!$resBase) continue;
                    if (in_array($resBase->tipo, ['labor', 'mano_obra'])) {
                        $pBase        = $resBase->precio_usd ?? 0;
                        $porcentajeCS = $pctGlobal > 0
                            ? $pctGlobal
                            : ($resBase->social_charges_percentage ?? 0);
                        $totalCS += $multiplier * $cantNodo * $interno->cantidad * $pBase * ($porcentajeCS / 100);
                    }
                }
            }

            if ($nodo->hijos && $nodo->hijos->count() > 0) {
                $totalCS += calcularCargaSocialRecursiva($nodo->hijos, $multiplier * $cantNodo, $pctGlobal);
            }
        }
        return $totalCS;
    }


    $subtotalBase = 0;
    $cargaSocialCalculada = 0;
    $pctCSGlobal = (float) ($proyecto->carga_social ?? 0);

    foreach ($categorias as $nodosRaiz) {
        foreach ($nodosRaiz as $nodoPadre) {
            $subtotalBase += calcularSubtotalRecursivo($nodoPadre->hijos);
            $cargaSocialCalculada += calcularCargaSocialRecursiva($nodoPadre->hijos, 1, $pctCSGlobal);
        }
    }

    // NOTA: El subtotal y el beneficio NO deben sumar la carga social.
    // La carga social solo se muestra como dato aparte.


   // Cálculos Finales (sin carga social en subtotal ni beneficio)
$beneficioCalculado = $mostrarBeneficio
    ? $subtotalBase * (($proyecto->beneficio ?? 0) / 100)
    : 0;
$subtotalConBeneficio = $subtotalBase + $beneficioCalculado;
$iva = $subtotalConBeneficio * (($proyecto->impuestos ?? 22) / 100);
$totalFinal = $subtotalConBeneficio + $iva;
@endphp

    {{-- Header: Resumen --}}
    <div class="flex items-center justify-between mb-2">
        <p class="text-xs text-gray-600 font-black uppercase tracking-widest">Resumen</p>
    </div>

  {{-- Cards scroll horizontal en mobile, grid en desktop --}}
  <div id="stats-cards">
  <div class="flex gap-3 overflow-x-auto pb-1 md:grid md:grid-cols-3 {{ $mostrarBeneficio ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} md:overflow-visible">

    {{-- Subtotal --}}
    <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-xl p-2.5 text-center shrink-0 w-32 md:w-auto text-black dark:text-white">
        <p class="text-[10px] text-gray-500 font-black uppercase mb-1">Subtotal</p>
        <p class="text-sm font-black text-white leading-tight">
            USD {{ number_format($subtotalBase, 0, ',', '.') }}
        </p>
    </div>

    {{-- Beneficio --}}
    @if($mostrarBeneficio)
    <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-xl p-2.5 text-center shrink-0 w-36 md:w-auto text-black dark:text-white">
        <p class="text-[10px] text-gray-500 font-black uppercase mb-1">
            Benef. ({{ number_format($proyecto->beneficio ?? 0, 0) }}%)
        </p>
        <p class="text-sm font-black text-orange-400 leading-tight">
            USD {{ number_format($beneficioCalculado, 0, ',', '.') }}
        </p>
    </div>
    @endif

    {{-- Impuestos --}}
    <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-xl p-2.5 text-center shrink-0 w-36 md:w-auto text-black dark:text-white">
        <p class="text-[10px] text-gray-500 font-black uppercase mb-1">
            IVA ({{ $proyecto->impuestos ?? 22 }}%)
        </p>
        <p class="text-sm font-black text-white leading-tight">
            USD {{ number_format($iva, 0, ',', '.') }}
        </p>
    </div>

    {{-- Precio Final --}}
    <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-xl p-2.5 text-center shadow-lg shadow-white/5 shrink-0 w-40 md:w-auto text-black dark:text-white">
        <p class="text-[10px] text-gray-400 font-black uppercase mb-1">Precio Final</p>
        <p class="text-sm font-black text-black dark:text-white leading-tight">
            USD {{ number_format($totalFinal, 0, ',', '.') }}
        </p>
    </div>

    {{-- Carga Social --}}
    <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-xl p-2.5 text-center shrink-0 w-32 md:w-auto text-black dark:text-white {{ $cargaSocialCalculada > 0 ? '' : 'opacity-50' }}">
        <p class="text-[10px] text-gray-500 font-black uppercase mb-1">C. Social</p>
        <p class="text-sm font-black text-blue-400 leading-tight">
            USD {{ number_format($cargaSocialCalculada, 0, ',', '.') }}
        </p>
    </div>

</div>
  </div>{{-- /stats-cards --}}
</div>{{-- /panel superior --}}

{{-- DRAG HANDLE VERTICAL (resize stats vs tabla) --}}
<div id="v-handle" class="shrink-0 h-2 flex items-center justify-center cursor-ns-resize select-none relative z-10"
    onmousedown="_vResizeStart(event)"
    ontouchstart="_vResizeStart(event.touches[0]);event.preventDefault()">
  <div id="v-handle-line" class="w-12 h-0.5 rounded-full bg-white/10" style="transition:background .15s"></div>
</div>

{{-- AREA SCROLLABLE: tabla / ejecución --}}
<div class="flex-1 min-h-0 overflow-auto px-6 pb-6 pt-2">

    {{-- TABLA PRESUPUESTO --}}
    @if($vistaActiva === 'presupuesto')
    <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl overflow-hidden text-black dark:text-white">
        <div class="overflow-x-auto scrollbar-thin scrollbar-track-transparent scrollbar-thumb-white/10">
        <div class="min-w-[580px]">

        <div class="grid grid-cols-12 px-3 py-1.5 border-b border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-white/[0.01]">
            <div class="col-span-1 text-xs text-gray-600 font-black">#</div>
            <div class="col-span-4 text-xs text-gray-600 font-black uppercase tracking-widest">Descripción</div>
            <div class="col-span-1 text-xs text-gray-600 font-black text-center">Ud.</div>
            <div class="col-span-1 text-xs text-gray-600 font-black text-center">Cant.</div>
            <div class="col-span-2 text-xs text-gray-600 font-black text-center">P. Unit.</div>
            <div class="col-span-1 text-xs text-gray-600 font-black text-center">C. Social</div>
            <div class="col-span-2 text-right text-xs text-gray-600 font-black">Total</div>
        </div>

        @forelse($categorias as $nombreCategoria => $nodosRaiz)
            @php
                $nodoPadre   = $nodosRaiz->first();
                $nodosReales = $nodoPadre?->hijos ?? collect();
                $totalCategoria = calcularSubtotalRecursivo($nodosReales);
                $catKey         = 'cat_' . $nombreCategoria;
                $catAbierta     = in_array($catKey, $nodosAbiertos ?? []);

                // Carga social total de todos los recursos labor dentro de esta categoría
                $calcularCSRecursivo = function($nodos, float $mult = 1) use (&$calcularCSRecursivo, $proyecto) {
                    $total = 0;
                    $pctGlobal = (float)($proyecto->carga_social ?? 0);
                    foreach ($nodos as $n) {
                        $esLaborN = !is_null($n->recurso_id) && in_array($n->recurso?->tipo, ['labor', 'mano_obra']);
                        if ($esLaborN) {
                            $pct    = $pctGlobal > 0 ? $pctGlobal : (float)($n->recurso?->social_charges_percentage ?? 0);
                            $precio = $n->precio_unitario ?? $n->precio_usd ?? 0;
                            $total += $precio * ($pct / 100) * ($n->cantidad ?? 1) * $mult;
                        }
                        if ($n->hijos && $n->hijos->count() > 0) {
                            $total += $calcularCSRecursivo($n->hijos, $mult * ($n->cantidad ?? 1));
                        }
                    }
                    return $total;
                };
                $csCategoria = $calcularCSRecursivo($nodosReales);
            @endphp

            {{-- CATEGORÍA --}}
            <div class="border-b border-gray-200 dark:border-white/5" wire:key="{{ 'cat-' . Str::slug($nombreCategoria) }}">

                <div class="grid grid-cols-12 px-3 py-2 bg-gray-100 dark:bg-white/[0.02] items-center group {{ !$modoLectura && !in_array($proyecto->estado_obra, ['ejecucion','en_ejecucion']) ? 'cursor-grab active:cursor-grabbing' : '' }}"
                     data-node-id="{{ $nodosRaiz->first()->id }}"
                     data-parent-id=""
                     @if(!$modoLectura && !in_array($proyecto->estado_obra, ['ejecucion','en_ejecucion'])) draggable="true" @endif>

                    <div class="col-span-1 text-xs text-gray-600 font-mono">
                        {{ $loop->iteration }}
                    </div>

                    <div class="col-span-4 flex items-center justify-between pr-2">

                        {{-- IZQUIERDA --}}
                        <div onclick="_lwToggle('{{ $catKey }}')" class="flex items-center gap-1.5 cursor-pointer min-w-0">
                            <svg id="chv-{{ $catKey }}" class="w-2.5 h-2.5 text-gray-500 shrink-0" style="transition:transform .2s;{{ $catAbierta ? 'transform:rotate(90deg)' : '' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-xs font-black uppercase tracking-widest truncate">
                                    {{ $nombreCategoria }}
                                </p>
                                <p class="text-[10px] text-gray-700 dark:text-gray-600 font-bold uppercase">
                                    {{ $nodosReales->count() }} rubros
                                </p>
                            </div>
                        </div>

                        {{-- BOTONES icono-only (ocultos en modo lectura) --}}
                        @if(!$modoLectura && !in_array($proyecto->estado_obra, ['ejecucion', 'en_ejecucion']))
                        <div class="flex items-center gap-0.5 shrink-0 ml-1">
                            {{-- Drag handle --}}
                            <div title="Arrastrar para reordenar"
                                 class="w-6 h-6 flex items-center justify-center text-gray-600 hover:text-gray-300 cursor-grab active:cursor-grabbing shrink-0 transition-colors select-none">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 4a1 1 0 110-2 1 1 0 010 2zm6 0a1 1 0 110-2 1 1 0 010 2zM7 9a1 1 0 110-2 1 1 0 010 2zm6 0a1 1 0 110-2 1 1 0 010 2zM7 14a1 1 0 110-2 1 1 0 010 2zm6 0a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </div>
                            {{-- Copy --}}
                            <button wire:click.stop="copiarNodo({{ $nodosRaiz->first()->id }})"
                                title="Copiar"
                                class="w-6 h-6 flex items-center justify-center rounded transition
                                    {{ isset($nodoCopiadoId) && $nodoCopiadoId == $nodosRaiz->first()->id ? 'bg-purple-500/30 text-purple-300' : 'bg-white/10 text-gray-400 hover:bg-white/20' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                            {{-- Paste --}}
                            @if(isset($nodoCopiadoId) && $nodoCopiadoId)
                            <button wire:click.stop="pegarNodo({{ $nodosRaiz->first()->id }})"
                                title="Pegar aquí"
                                class="w-6 h-6 flex items-center justify-center bg-purple-500/20 text-purple-400 rounded hover:bg-purple-500/40 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </button>
                            @endif
                            <button wire:click.stop="abrirModalSubrubro({{ $nodosRaiz->first()->id }}, '{{ $nombreCategoria }}', '{{ $nombreCategoria }}')"
                                title="+ Rubro"
                                class="w-6 h-6 flex items-center justify-center bg-purple-500/20 text-purple-400 rounded hover:bg-purple-500/40 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            </button>
                            <button wire:click.stop="abrirModalRecursos({{ $nodosRaiz->first()->id }}, '{{ $nombreCategoria }}', '{{ $nombreCategoria }}')"
                                title="+ Recurso"
                                class="w-6 h-6 flex items-center justify-center bg-blue-500/20 text-blue-400 rounded hover:bg-blue-500/40 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </button>
                            <button wire:click.stop="abrirModalEditar({{ $nodosRaiz->first()->id }})"
                                title="Editar"
                                class="w-6 h-6 flex items-center justify-center bg-yellow-500/20 text-yellow-400 rounded hover:bg-yellow-500/40 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click.stop="abrirModalEliminar({{ $nodosRaiz->first()->id }})"
                                title="Eliminar"
                                class="w-6 h-6 flex items-center justify-center bg-red-500/20 text-red-400 rounded hover:bg-red-500/40 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        @endif

                    </div>

                    <div class="col-span-1"></div>
                    <div class="col-span-1"></div>
                    <div class="col-span-2"></div>
                    <div class="col-span-1 text-center text-xs font-bold text-blue-300 font-mono">
                        @if($csCategoria > 0)
                            {{ number_format($csCategoria, 2, ',', '.') }}
                        @endif
                    </div>
                    <div class="col-span-2 text-right text-xs font-black text-white font-mono">
                        {{ number_format($totalCategoria, 2, ',', '.') }}
                    </div>

                </div>

                {{-- HIJOS --}}
                @if($nodosReales->count() > 0)
                    <div id="children-{{ $catKey }}" style="{{ $catAbierta ? '' : 'display:none' }}">
                        @foreach($nodosReales as $nodo)
                            @include('livewire.proyecto.partials.nodo-presupuesto', [
                                'nodo'             => $nodo,
                                'nombreCategoria'  => $nombreCategoria,
                                'nivel'            => 1,
                                'nodosAbiertos'    => $nodosAbiertos,
                                'modoLectura'      => $modoLectura || in_array($proyecto->estado_obra, ['ejecucion', 'en_ejecucion']),
                            ])
                        @endforeach
                    </div>
                @endif

            </div>

        @empty
            <div class="py-20 text-center space-y-4">
                <p class="text-gray-700 text-sm uppercase font-bold tracking-widest">Sin rubros cargados</p>
                @if(!$modoLectura && !in_array($proyecto->estado_obra, ['ejecucion', 'en_ejecucion']))
                <button wire:click="abrirModalRubro"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-500/20 border border-purple-500/30 text-purple-400 hover:bg-purple-500/30 text-xs font-black uppercase tracking-wider transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Agregar primer rubro
                </button>
                @endif
            </div>
        @endforelse

        </div>{{-- /min-w --}}
        </div>{{-- /overflow-x-auto --}}
    </div>

    @else
    {{-- ═══════════════════════════════════════════════════════
         VISTA EJECUCIÓN: comparación presupuestado vs real
    ═══════════════════════════════════════════════════════ --}}

    {{-- BANNER MODO LECTURA EN EJECUCIÓN --}}
    @if($modoLectura && $vistaActiva === 'ejecucion')
    <div class="flex items-center gap-3 {{ $proyecto->estado_obra === 'finalizado' ? 'bg-gray-500/10 border border-gray-500/20' : 'bg-orange-500/10 border border-orange-500/20' }} rounded-xl px-4 py-3 mb-6">
        <svg class="w-4 h-4 {{ $proyecto->estado_obra === 'finalizado' ? 'text-gray-400' : 'text-orange-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <div>
            @if($proyecto->estado_obra === 'finalizado')
                <p class="text-gray-400 font-black text-sm uppercase tracking-widest">Proyecto finalizado — Ejecución bloqueada</p>
                <p class="text-gray-500 text-sm">El proyecto ha terminado. No se pueden registrar nuevos costos reales. Solo modo lectura.</p>
            @elseif($proyecto->estado_obra === 'pausado')
                <p class="text-orange-400 font-black text-sm uppercase tracking-widest">Proyecto pausado — Ejecución bloqueada</p>
                <p class="text-gray-500 text-sm">El proyecto está pausado. No se pueden registrar nuevos costos reales.</p>
            @endif
        </div>
    </div>
    @endif

    @php
        // Función para recolectar solo los ítems hoja (con recurso_id)
        function recolectarHojas($nodos, $categoria = '') {
            $hojas = [];
            foreach ($nodos as $nodo) {
                $cat = $categoria ?: ($nodo->categoria ?? 'Sin categoría');
                if (!is_null($nodo->recurso_id)) {
                    $presupuestado = ($nodo->cantidad ?? 1) * ($nodo->precio_usd ?? 0);
                    $hojas[] = [
                        'id'            => $nodo->id,
                        'nombre'        => $nodo->nombre,
                        'unidad'        => $nodo->unidad ?? '',
                        'cantidad'      => $nodo->cantidad ?? 1,
                        'precio_usd'    => $nodo->precio_usd ?? 0,
                        'presupuestado' => $presupuestado,
                        'costo_real'    => $nodo->costo_real,
                        'categoria'     => $cat,
                    ];
                }
                if ($nodo->hijos && $nodo->hijos->count() > 0) {
                    $subHojas = recolectarHojas($nodo->hijos, $cat);
                    $hojas = array_merge($hojas, $subHojas);
                }
            }
            return $hojas;
        }

        $todasLasHojas = [];
        foreach ($categorias as $nombreCat => $nodosRaiz) {
            $nodoPadreEj = $nodosRaiz->first();
            $hijosEj = $nodoPadreEj?->hijos ?? collect();
            $subHojas = recolectarHojas($hijosEj, $nombreCat);
            $todasLasHojas = array_merge($todasLasHojas, $subHojas);
        }

        $hojasPorCategoria = collect($todasLasHojas)->groupBy('categoria');
        $totalPresupuestado = collect($todasLasHojas)->sum('presupuestado');
        $totalReal = collect($todasLasHojas)->sum(fn($h) => $h['costo_real'] ?? 0);
        
        // Cálculos de IVA y Precio Final basados en ambos totales
        $pctImpuestos = (float) ($proyecto->impuestos ?? 22);
        
        // Presupuesto: Total + IVA
        $ivaPresupuestado = $totalPresupuestado * ($pctImpuestos / 100);
        $precioFinalPresupuestado = $totalPresupuestado + $ivaPresupuestado;
        
        // Ejecución: Total Real + IVA
        $ivaEjecutado = $totalReal * ($pctImpuestos / 100);
        $precioFinalEjecutado = $totalReal + $ivaEjecutado;
        
        // Diferencia: Precio Final Presupuestado - Precio Final Ejecutado
        $diferencia = $precioFinalPresupuestado - $precioFinalEjecutado;
    @endphp

    {{-- Resumen ejecución (cards) --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        {{-- Total Presupuestado --}}
        <div class="bg-[#111] border border-white/5 rounded-2xl p-4 text-center">
            <p class="text-xs text-gray-500 font-black uppercase mb-1">Total Presupuestado</p>
            <p class="text-base font-black text-white font-mono">USD {{ number_format($totalPresupuestado, 0, ',', '.') }}</p>
        </div>

        {{-- IVA sobre Ejecutado --}}
        <div class="bg-[#111] border border-white/5 rounded-2xl p-4 text-center">
            <p class="text-xs text-gray-500 font-black uppercase mb-1">IVA ({{ number_format($pctImpuestos, 0) }}%)</p>
            <p class="text-base font-black text-white font-mono">USD {{ number_format($ivaEjecutado, 0, ',', '.') }}</p>
        </div>

        {{-- Precio Final Ejecutado --}}
        <div class="bg-white rounded-2xl p-4 text-center shadow-lg shadow-white/5 shrink-0">
            <p class="text-xs text-gray-400 font-black uppercase mb-2">Precio Final</p>
            <p class="text-xl font-black text-black leading-tight">
                USD {{ number_format($precioFinalEjecutado, 0, ',', '.') }}
            </p>
        </div>

        {{-- Total Ejecutado --}}
        <div class="bg-[#111] border border-white/5 rounded-2xl p-4 text-center">
            <p class="text-xs text-gray-500 font-black uppercase mb-1">Total Ejecutado</p>
            <p class="text-base font-black {{ $totalReal > $totalPresupuestado ? 'text-red-400' : 'text-green-400' }} font-mono">
                USD {{ number_format($totalReal, 0, ',', '.') }}
            </p>
        </div>

        {{-- Diferencia --}}
        <div class="bg-[#111] border border-white/5 rounded-2xl p-4 text-center">
            <p class="text-xs text-gray-500 font-black uppercase mb-1">Diferencia</p>
            <p class="text-base font-black {{ $diferencia > 0 ? 'text-green-400' : ($diferencia < 0 ? 'text-red-400' : 'text-gray-400') }} font-mono">
                {{ $diferencia >= 0 ? '+' : '' }}USD {{ number_format($diferencia, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Tabla de comparación --}}
    <div class="bg-[#111] border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin scrollbar-track-transparent scrollbar-thumb-white/10">
        <div class="min-w-[900px]">

        {{-- Cabecera --}}
        <div class="grid px-4 py-3 border-b border-white/5 bg-white/[0.01]" style="grid-template-columns: 2fr 60px 70px 120px 140px 110px 90px;">
            <div class="text-sm text-gray-600 font-black uppercase tracking-widest">Descripción</div>
            <div class="text-sm text-gray-600 font-black text-center">Ud.</div>
            <div class="text-sm text-gray-600 font-black text-center">Cant.</div>
            <div class="text-sm text-gray-600 font-black text-right">Presupuestado</div>
            <div class="text-sm text-orange-500 font-black text-right pr-2">Costo Real</div>
            <div class="text-sm text-gray-600 font-black text-right">Diferencia</div>
            <div class="text-sm text-gray-600 font-black text-right">Desvío</div>
        </div>

        @forelse($hojasPorCategoria as $cat => $items)
            @php
                $catPresupuestado = collect($items)->sum('presupuestado');
                $catReal = collect($items)->sum(fn($i) => $i['costo_real'] ?? 0);
                $catDiff = $catReal - $catPresupuestado;
                $catPct  = $catPresupuestado > 0 ? (($catReal - $catPresupuestado) / $catPresupuestado) * 100 : 0;
            @endphp

            {{-- Fila categoría --}}
            <div class="grid px-4 py-2.5 bg-white/[0.025] border-b border-white/5" style="grid-template-columns: 2fr 60px 70px 120px 140px 110px 90px;">
                <div class="text-base text-white font-black uppercase tracking-widest">{{ $cat }}</div>
                <div></div>
                <div></div>
                <div class="text-right text-base text-gray-300 font-black font-mono">{{ number_format($catPresupuestado, 0, ',', '.') }}</div>
                <div class="text-right text-base {{ $catReal > $catPresupuestado ? 'text-red-400' : 'text-green-400' }} font-black font-mono pr-2">
                    {{ $catReal > 0 ? number_format($catReal, 0, ',', '.') : '—' }}
                </div>
                <div class="text-right text-base {{ $catDiff > 0 ? 'text-red-400' : ($catDiff < 0 ? 'text-green-400' : 'text-gray-500') }} font-black font-mono">
                    {{ $catReal > 0 ? (($catDiff >= 0 ? '+' : '') . number_format($catDiff, 0, ',', '.')) : '—' }}
                </div>
                <div class="text-right text-base {{ $catPct > 0 ? 'text-red-400' : ($catPct < 0 ? 'text-green-400' : 'text-gray-500') }} font-black">
                    {{ $catReal > 0 ? (($catPct >= 0 ? '+' : '') . number_format($catPct, 1) . '%') : '—' }}
                </div>
            </div>

            {{-- Filas de ítems --}}
            @foreach($items as $hoja)
                @php
                    $hjPres = $hoja['presupuestado'];
                    $hjReal = $hoja['costo_real'] ?? null;
                    $hjDiff = $hjReal !== null ? $hjReal - $hjPres : null;
                    $hjPct  = ($hjReal !== null && $hjPres > 0) ? (($hjReal - $hjPres) / $hjPres) * 100 : null;
                @endphp
                <div class="grid px-4 py-2 border-b border-white/[0.025] hover:bg-white/[0.01] items-center"
                     style="grid-template-columns: 2fr 60px 70px 120px 140px 110px 90px;"
                     wire:key="ej-{{ $hoja['id'] }}">
                    {{-- Nombre --}}
                    <div class="pl-4 text-base text-gray-300 font-medium truncate">{{ $hoja['nombre'] }}</div>

                    {{-- Unidad --}}
                    <div class="text-sm text-gray-600 text-center uppercase">{{ $hoja['unidad'] }}</div>

                    {{-- Cantidad --}}
                    <div class="text-sm text-gray-500 text-center font-mono">{{ number_format($hoja['cantidad'], 2) }}</div>

                    {{-- Presupuestado --}}
                    <div class="text-right text-base text-gray-400 font-mono">{{ number_format($hjPres, 2, ',', '.') }}</div>

                    {{-- Costo Real (input editable) --}}
                    <div class="flex justify-end pr-2">
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0,00"
                            value="{{ $hjReal !== null ? number_format($hjReal, 2, '.', '') : '' }}"
                            wire:change="actualizarCostoReal({{ $hoja['id'] }}, $event.target.value)"
                            @disabled(!in_array($proyecto->estado_obra, ['ejecucion', 'en_ejecucion']))
                            class="w-32 bg-[#0a0a0a] border {{ !in_array($proyecto->estado_obra, ['ejecucion', 'en_ejecucion']) ? 'border-gray-600/30 text-gray-600 cursor-not-allowed opacity-50' : 'border-orange-500/30 text-orange-300' }} rounded-lg px-2 py-1 text-base font-mono text-right focus:border-orange-500 focus:outline-none placeholder-gray-700">
                    </div>

                    {{-- Diferencia --}}
                    <div class="text-right text-base font-mono
                        {{ $hjDiff === null ? 'text-gray-700' :
                           ($hjDiff > 0 ? 'text-red-400' : ($hjDiff < 0 ? 'text-green-400' : 'text-gray-500')) }}">
                        @if($hjDiff !== null)
                            {{ ($hjDiff >= 0 ? '+' : '') . number_format($hjDiff, 2, ',', '.') }}
                        @else
                            —
                        @endif
                    </div>

                    {{-- % Desvío --}}
                    <div class="text-right text-sm font-black
                        {{ $hjPct === null ? 'text-gray-700' :
                           ($hjPct > 5 ? 'text-red-400' : ($hjPct < -5 ? 'text-green-400' : 'text-yellow-400')) }}">
                        @if($hjPct !== null)
                            {{ ($hjPct >= 0 ? '+' : '') . number_format($hjPct, 1) }}%
                        @else
                            —
                        @endif
                    </div>
                </div>
            @endforeach

        @empty
            <div class="py-16 text-center text-gray-700 text-sm uppercase font-bold tracking-widest">
                Sin recursos cargados
            </div>
        @endforelse

        </div>{{-- /min-w --}}
        </div>{{-- /overflow-x-auto --}}
    </div>

    @endif {{-- /vistaActiva --}}

</div>{{-- /area scrollable --}}
</div>{{-- /budgetPanel --}}

{{-- ═══ HANDLE HORIZONTAL (drag izq/der para redimensionar) ═══ --}}
<div id="h-handle" class="hidden sm:flex shrink-0 w-1.5 cursor-ew-resize relative items-stretch select-none z-20"
     onmousedown="_hResizeStart(event)"
     ontouchstart="_hResizeStart(event.touches[0]);event.preventDefault()">
  <div id="h-handle-bg" class="absolute inset-0" style="transition:background .15s"></div>
  <div id="h-handle-line" class="absolute inset-y-0 left-1/2 w-px bg-white/10" style="transform:translateX(-50%);transition:background .15s"></div>
</div>

{{-- ═══ AREA DERECHA: solo aparece cuando el panel está redimensionado ═══ --}}
<div id="right-area" class="bg-[#0a0a0a]" style="display:none"></div>

</div>{{-- /flex row externo --}}
    {{-- ══════════════════════════════════════════════════════
         MODAL: NUEVO RUBRO (CATEGORÍA RAÍZ)
    ══════════════════════════════════════════════════════ --}}
    @if($mostrarModalRubro)
    <div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">
        <div class="w-full max-w-md border border-white/10 rounded-2xl p-6 space-y-5 bg-[#0d0d0d] shadow-2xl">

            <div class="text-center">
                <p class="text-xs text-gray-600 uppercase font-black mb-1">Nuevo Rubro</p>
                <h2 class="text-purple-400 font-extrabold text-sm uppercase">Agregar Rubro</h2>
            </div>

            <div>
                <label class="text-sm text-gray-500 uppercase font-black">Nombre del Rubro</label>
                <input type="text" wire:model="nombreRubro"
                    placeholder="Ej: 01. Preliminares"
                    class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] border border-white/10 text-white text-sm outline-none focus:border-purple-500/50">
                @error('nombreRubro') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm text-gray-500 uppercase font-black">Unidad</label>
                <select wire:model="unidadRubro"
                    class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 text-sm outline-none focus:border-purple-500/50">
                    <option value="gl">gl (Global)</option>
                    <option value="un">und (Unidad)</option>
                    <option value="m">m (Metro)</option>
                    <option value="m2">m² (Metro cuadrado)</option>
                    <option value="m3">m³ (Metro cúbico)</option>
                    <option value="kg">kg (Kilogramo)</option>
                    <option value="l">l (Litro)</option>
                    <option value="h">h (Hora)</option>
                    <option value="d">d (Día)</option>
                    <option value="p2">p² (Pie cuadrado)</option>
                    <option value="ml">ml (Metro lineal)</option>
                    <option value="mes">mes</option>
                </select>
            </div>

            <div class="flex gap-3 pt-1">
                <button wire:click="$set('mostrarModalRubro', false)"
                    class="w-1/2 py-3 rounded-xl border border-white/10 text-white text-xs font-bold hover:bg-white/5 transition-all">
                    CANCELAR
                </button>
                <button wire:click="guardarRubro"
                    class="w-1/2 bg-purple-500 text-white py-3 rounded-xl font-black text-xs hover:bg-purple-600 transition-all">
                    GUARDAR
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         MODAL: NUEVO SUB-RUBRO
    ══════════════════════════════════════════════════════ --}}
    @if($mostrarModalSubrubro)
    <div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">
        <div class="w-full max-w-sm border border-white/10 rounded-2xl p-6 space-y-5 bg-[#0d0d0d] shadow-2xl">
            <div class="text-center">
                <p class="text-xs text-gray-600 uppercase font-black mb-1">Nuevo Sub-Rubro en</p>
                <h2 class="text-purple-400 font-extrabold text-sm uppercase">{{ $nombreCtx }}</h2>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500 uppercase font-black">Nombre</label>
                    <input type="text" wire:model.live="nombreSubrubro"
                        class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 text-sm focus:border-purple-500/50 outline-none"
                        placeholder="Ej: Mampostería">
                </div>
                <div>
                    <label class="text-sm text-gray-500 uppercase font-black">Unidad</label>
                    <select wire:model.live="unidadSubrubro"
                        class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 text-sm outline-none">
                        <option value="gl">gl (Global)</option>
                        <option value="un">und (Unidad)</option>
                        <option value="m">m (Metro)</option>
                        <option value="m2">m² (Metro cuadrado)</option>
                        <option value="m3">m³ (Metro cúbico)</option>
                        <option value="kg">kg (Kilogramo)</option>
                        <option value="l">l (Litro)</option>
                        <option value="h">h (Hora)</option>
                        <option value="d">d (Día)</option>
                        <option value="p2">p² (Pie cuadrado)</option>
                        <option value="ml">ml (Metro lineal)</option>
                        <option value="mes">mes</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button wire:click="$set('mostrarModalSubrubro', false)"
                    class="w-1/2 py-3 rounded-xl border border-white/10 text-white text-xs font-bold hover:bg-white/5 transition-all">
                    CANCELAR
                </button>
                <button wire:click="guardarSubrubro"
                    class="w-1/2 bg-white text-black py-3 rounded-xl font-black text-xs hover:bg-gray-100 transition-all">
                    GUARDAR
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         MODAL: AGREGAR RECURSOS DIRECTOS
    ══════════════════════════════════════════════════════ --}}
    @if($mostrarModalRecursos)
    <div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">
        <div class="w-full max-w-md border border-white/10 rounded-2xl p-6 space-y-5 bg-[#0d0d0d] shadow-2xl">
            <div class="text-center">
                <p class="text-xs text-gray-600 uppercase font-black mb-1">Recursos en</p>
                <h2 class="text-blue-400 font-extrabold text-sm uppercase">{{ $nombreCtx }}</h2>
            </div>

            <button wire:click="$set('modalSelectorRecursos', true)"
                class="w-full py-3 rounded-xl bg-blue-600/20 border border-blue-500/30 text-blue-400 text-sm font-black hover:bg-blue-600/30 transition-all">
                + BUSCAR Y AGREGAR RECURSOS
            </button>

            {{-- Items seleccionados --}}
            @if(count($itemsRecursos) > 0)
            <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                @foreach($itemsRecursos as $index => $item)
                    <div class="flex items-center gap-2 bg-white/5 rounded-xl px-3 py-2 border border-white/[0.02]"
                         wire:key="{{ 'ri-' . $index }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-base font-bold truncate uppercase">{{ $item['nombre'] }}</p>
                            <p class="text-gray-500 text-xs uppercase">USD {{ number_format($item['precio_usd'] ?? 0, 2) }}</p>
                        </div>
                        <input type="number" step="0.01" wire:model="itemsRecursos.{{ $index }}.cantidad"
                            class="w-16 bg-[#0f1115] border border-white/10 rounded-lg px-2 py-1 text-white text-base text-center">
                        <button wire:click="quitarItemRecurso({{ $index }})" class="text-gray-600 hover:text-red-400 transition-colors px-1 text-lg">×</button>
                    </div>
                @endforeach
            </div>
            @endif

            <div class="flex gap-3 pt-1">
                <button wire:click="cancelarModalRecursos"
                    class="w-1/2 py-3 rounded-xl border border-white/10 text-white text-xs font-bold hover:bg-white/5 transition-all">
                    CANCELAR
                </button>
                <button wire:click="guardarRecursos"
                    class="w-1/2 bg-white text-black py-3 rounded-xl font-black text-xs hover:bg-gray-100 transition-all">
                    GUARDAR
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         MODAL: SELECTOR DE RECURSOS (compartido)
    ══════════════════════════════════════════════════════ --}}
    @if($modalSelectorRecursos)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">
        <div class="bg-[#141414] border border-white/10 rounded-3xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[85vh]">
            <div class="px-8 pt-8 pb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-white font-black tracking-widest text-lg uppercase">Seleccionar Recursos</h3>
                        <p class="text-gray-500 text-sm uppercase font-bold mt-1">Para: <span class="text-blue-400">{{ $nombreCtx }}</span></p>
                    </div>
                    <button wire:click="$set('modalSelectorRecursos', false)" class="text-gray-500 hover:text-white transition-colors text-2xl leading-none">×</button>
                </div>

                <input type="text" wire:model.live.debounce.300ms="buscarSelector" placeholder="Buscar por nombre..."
                    class="w-full px-4 py-3.5 rounded-xl bg-[#1a1a1a] border border-white/5 text-white text-sm mb-6 outline-none">

                <div class="flex flex-wrap gap-2">
                    @foreach(['Todos', 'Materiales', 'Mano de Obra', 'Equipos', 'Composiciones'] as $f)
                        <button wire:click="setFiltro('{{ $f }}')"
                            class="px-4 py-1.5 rounded-lg text-sm font-black uppercase border transition-all {{ ($filtroTipo ?? 'Todos') == $f ? 'bg-white text-black border-white' : 'bg-[#1a1a1a] text-gray-400 border-white/5' }}">
                            {{ $f }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="overflow-y-auto flex-1 px-4 mb-4">
                <div class="space-y-2">
                    @foreach($recursosFiltrados as $recurso)
                        @php $yaAgregado = collect($itemsRecursos)->contains('recurso_id', $recurso->id); @endphp
                        <div class="flex items-center gap-4 px-4 py-3 bg-[#1a1a1a] border border-white/[0.03] rounded-2xl hover:border-white/10 transition-all"
                             wire:key="{{ 'rec-' . $recurso->id }}">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-[12px] font-black uppercase truncate">{{ $recurso->nombre }}</p>
                                <p class="text-gray-500 text-xs font-bold uppercase">{{ $recurso->unidad }} · {{ $recurso->tipo }}</p>
                            </div>
                            <div class="flex items-center gap-2 bg-black/20 p-1 rounded-xl border border-white/5">
                                <input type="number" id="qty-r-{{ $recurso->id }}" value="1"
                                    class="w-12 bg-transparent text-white text-xs font-bold text-center focus:outline-none">
                                <button onclick="let v = document.getElementById('qty-r-{{ $recurso->id }}').value; @this.toggleItemRecurso({{ $recurso->id }}, v)"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $yaAgregado ? 'bg-red-500/20 text-red-400 hover:bg-red-500/40' : 'bg-white/5 text-gray-400 hover:bg-white hover:text-black' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="{{ $yaAgregado ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-6 border-t border-white/5 bg-[#141414] rounded-b-3xl">
                <button wire:click="$set('modalSelectorRecursos', false)"
                    class="w-full py-4 bg-[#9333ea] text-white font-black text-xs rounded-xl uppercase tracking-[0.2em] hover:bg-purple-500 transition-all shadow-lg shadow-purple-500/20">
                    Confirmar ({{ count($itemsRecursos) }})
                </button>
            </div>
        </div>
    </div>
    @endif
@if($mostrarModalEditar)
<div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/80 backdrop-blur-md px-4"
    x-data="{ nombreLocal: @entangle('editNombre') }">
    <div class="w-full max-w-md border border-white/10 rounded-2xl p-6 space-y-5 bg-[#0d0d0d] shadow-2xl">

        <div class="text-center">
            <p class="text-xs text-gray-600 uppercase font-black mb-1">Editar</p>
            <h2 class="text-yellow-400 font-extrabold text-sm uppercase" x-text="nombreLocal || '{{ $editNombre }}'"></h2>
        </div>

        {{-- Nombre --}}
        <div>
            <label class="text-sm text-gray-500 uppercase font-black">Nombre</label>
            <input type="text"
                wire:model="editNombre"
                @input="nombreLocal = $event.target.value"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] border border-white/10 text-white text-sm outline-none focus:border-yellow-500/50">
        </div>

        {{-- Unidad --}} 
        <div>
            <label class="text-sm text-gray-500 uppercase font-black">Unidad</label>
            <select wire:model="editUnidad"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 text-sm outline-none focus:border-yellow-500/50">
                <option value="gl">gl (Global)</option>
                <option value="un">und (Unidad)</option>
                <option value="m">m (Metro)</option>
                <option value="m2">m² (Metro cuadrado)</option>
                <option value="m3">m³ (Metro cúbico)</option>
                <option value="kg">kg (Kilogramo)</option>
                <option value="l">l (Litro)</option>
                <option value="h">h (Hora)</option>
                <option value="d">d (Día)</option>
                <option value="p2">p² (Pie cuadrado)</option>
                <option value="ml">ml (Metro lineal)</option>
                <option value="mes">mes</option>
            </select>
        </div>

        <div class="flex gap-3 pt-1">
            <button wire:click="$set('mostrarModalEditar', false)"
                class="w-1/2 py-3 rounded-xl border border-white/10 text-white text-xs font-bold hover:bg-white/5 transition-all">
                CANCELAR
            </button>
            <button wire:click="guardarEdicion"
                class="w-1/2 bg-white text-black py-3 rounded-xl font-black text-xs hover:bg-gray-100 transition-all">
                GUARDAR
            </button>
        </div>

    </div>
</div>
@endif

@if($mostrarModalEliminar)
<div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">

    <div class="w-full max-w-md border border-red-500/20 rounded-2xl p-6 space-y-5 bg-[#0d0d0d] shadow-2xl text-center">

        <div>
            <p class="text-xs text-gray-600 uppercase font-black mb-1">Confirmación</p>
            <h2 class="text-red-400 font-extrabold text-sm uppercase">Eliminar</h2>
        </div>

        

        <div class="flex gap-3 pt-1">
            <button wire:click="$set('mostrarModalEliminar', false)"
                class="w-1/2 py-3 rounded-xl border border-white/10 text-white text-xs font-bold hover:bg-white/5 transition-all">
                CANCELAR
            </button>

            <button wire:click="confirmarEliminar"
                class="w-1/2 bg-red-500 text-white py-3 rounded-xl font-black text-xs hover:bg-red-600 transition-all">
                ELIMINAR
            </button>
        </div>

    </div>
</div>
@endif

@if($mostrarModalInvitar)
<div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-[#1a1a1a] p-4 rounded-xl w-96">

        <h2 class="text-white font-bold mb-3">Invitar usuarios</h2>

        <input
            type="text"
            wire:model="buscarUsuario"
            wire:keyup="cargarUsuarios"
            class="w-full p-2 rounded bg-black text-white mb-3"
            placeholder="Buscar usuario..."
        />

        <div class="space-y-1 max-h-60 overflow-auto">

            @foreach($usuariosDisponibles as $u)
                <label class="flex items-center gap-2 text-sm text-gray-300 bg-[#111] p-2 rounded">
                    <input 
                        type="checkbox"
                        value="{{ $u->id }}"
                        wire:model="usuariosSeleccionados"
                        class="rounded"
                    >
                    <span>{{ $u->name }}</span>
                </label>
            @endforeach

        </div>

        <button
            wire:click="invitarUsuariosSeleccionados"
            class="mt-3 text-xs bg-green-600 px-3 py-1 rounded text-white">
            Invitar seleccionados
        </button>

        <button
            wire:click="$set('mostrarModalInvitar', false)"
            class="mt-2 text-xs text-gray-400">
            Cerrar
        </button>

    </div>
</div>
@endif

@if($mostrarModalCompartir)
<div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 px-4" x-data="{ copiado: @js($linkCopiado) }">
    <div class="bg-[#1a1a1a] border border-gray-700 rounded-2xl w-full max-w-md p-6 shadow-2xl">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-[#d15330]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                Compartir Proyecto
            </h2>
            <button wire:click="cerrarModalCompartir" class="text-gray-500 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            {{-- Descripción --}}
            <div class="text-sm text-gray-400">
                <p class="mb-3">Comparte este enlace con otros usuarios para que accedan a este proyecto:</p>
            </div>

            {{-- Selector de rol --}}
            @if($linkCompartible === '')
                <div class="bg-[#0f0f0f] border border-gray-800 rounded-lg p-4 space-y-3" x-data="{ rol: @js($rolCompartir) }">
                    <label class="block text-xs font-bold text-gray-300 uppercase">Rol del usuario invitado:</label>
                    <div class="space-y-2.5">
                        <label @click="rol = 'supervisor'" class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-colors" :class="rol === 'supervisor' ? 'border-[#d15330] bg-[#d15330]/10' : 'border-gray-800 hover:bg-white/5'">
                            <input type="radio" name="rol_compartir" value="supervisor" :checked="rol === 'supervisor'" class="w-4 h-4" />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-white">Supervisor</div>
                                <div class="text-sm text-gray-500">Acceso completo a todo</div>
                            </div>
                        </label>
                        <label @click="rol = 'presupuestador'" class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-colors" :class="rol === 'presupuestador' ? 'border-[#d15330] bg-[#d15330]/10' : 'border-gray-800 hover:bg-white/5'">
                            <input type="radio" name="rol_compartir" value="presupuestador" :checked="rol === 'presupuestador'" class="w-4 h-4" />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-white">Presupuestador</div>
                                <div class="text-sm text-gray-500">Editar presupuestos y recursos</div>
                            </div>
                        </label>
                        <label @click="rol = 'jefe_obra'" class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-colors" :class="rol === 'jefe_obra' ? 'border-[#d15330] bg-[#d15330]/10' : 'border-gray-800 hover:bg-white/5'">
                            <input type="radio" name="rol_compartir" value="jefe_obra" :checked="rol === 'jefe_obra'" class="w-4 h-4" />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-white">Jefe de Obra</div>
                                <div class="text-sm text-gray-500">Seguimiento de ejecución</div>
                            </div>
                        </label>
                    </div>

                    {{-- Botón generar --}}
                    <button @click="$wire.rolCompartir = rol; $wire.generarLinkCompartir()" class="w-full py-2.5 bg-[#d15330] hover:bg-[#c74620] text-white text-xs font-bold uppercase rounded-lg transition-colors mt-1">
                        Generar Link
                    </button>
                </div>
            @else
                {{-- Campo con link (solo después de generar) --}}
                <div class="flex items-center gap-2 bg-[#0f0f0f] border border-gray-800 rounded-lg p-3">
                    <input 
                        type="text"
                        readonly
                        id="linkCompartible"
                        value="{{ $linkCompartible }}"
                        class="flex-1 bg-transparent text-xs text-gray-300 outline-none truncate font-mono"
                    />
                    <button
                        @click="
                            const link = document.getElementById('linkCompartible');
                            navigator.clipboard.writeText(link.value);
                            copiado = true;
                            setTimeout(() => { copiado = false; }, 2000);
                        "
                        :class="{ 'text-green-400': copiado, 'text-[#d15330] hover:text-orange-400': !copiado }"
                        class="text-xs font-bold transition-colors px-2 py-1 rounded bg-[#1a1a1a] hover:bg-white/5 whitespace-nowrap"
                    >
                        <span x-show="!copiado">Copiar</span>
                        <span x-show="copiado">✓ Copiado</span>
                    </button>
                </div>

                {{-- Info --}}
                <div class="text-xs text-gray-500 space-y-1">
                    <p>📌 Este enlace expira en <strong>24 horas</strong></p>
                    <p>👤 Rol: <strong>{{ $rolCompartir === 'supervisor' ? 'Supervisor' : ($rolCompartir === 'presupuestador' ? 'Presupuestador' : 'Jefe de Obra') }}</strong></p>
                    <p>📝 Si no tiene cuenta, se la crearemos antes de acceder</p>
                </div>

                {{-- Botón generar nuevo --}}
                <button wire:click="abrirModalCompartir" class="w-full py-2 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white text-xs font-bold uppercase rounded-lg transition-colors">
                    Generar Otro Link
                </button>
            @endif
        </div>

        {{-- Botones --}}
        <div class="flex gap-2 mt-6">
            <button
                wire:click="cerrarModalCompartir"
                class="flex-1 px-3 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold transition-colors">
                Cerrar
            </button>
        </div>

    </div>
</div>
@endif

{{-- MODAL EXPORTACIÓN PDF --}}
@if($mostrarModalPDF)
<div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 px-4">
    <div class="bg-[#1a1a1a] border border-gray-700 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

        {{-- HEADER --}}
        <div class="sticky top-0 bg-[#1a1a1a] border-b border-gray-700 p-6 flex items-center justify-between">
            <div>
                <h2 class="text-white font-black text-lg uppercase tracking-widest">Opciones de Exportación PDF</h2>
                <p class="text-gray-400 text-xs mt-1">📋 Proyecto: <span class="font-bold text-white">{{ $proyecto->nombre_proyecto }}</span></p>
            </div>
            <button wire:click="cerrarModalPDF" class="text-gray-500 hover:text-white text-2xl">&times;</button>
        </div>

        {{-- CONTENIDO --}}
        <div class="p-6 space-y-6">

            {{-- TÍTULO DEL REPORTE --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-gray-400 text-xs font-bold uppercase tracking-wider">Título del Reporte</label>
                <input 
                    type="text" 
                    wire:model="tituloReporte" 
                    placeholder="Ej: REPORTE DE PRESUPUESTO"
                    class="bg-[#0d0d0d] border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:border-orange-500 focus:outline-none"
                >
            </div>

            {{-- ALCANCE DEL PRESUPUESTO --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-gray-400 text-xs font-bold uppercase tracking-wider">Alcance del Presupuesto</label>
                <textarea 
                    wire:model="alcancePresupuesto" 
                    placeholder="Ej: Incluye materiales, mano de obra y equipos para la fase de cimentación..."
                    class="bg-[#0d0d0d] border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:border-orange-500 focus:outline-none resize-none h-24"
                ></textarea>
            </div>

            {{-- CONDICIONES GENERALES --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-gray-400 text-xs font-bold uppercase tracking-wider">Condiciones Generales</label>
                <textarea 
                    wire:model="condicionesGenerales" 
                    placeholder="Ej: No se incluyen permisos municipales. El cliente debe proveer acceso a agua y luz..."
                    class="bg-[#0d0d0d] border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:border-orange-500 focus:outline-none resize-none h-24"
                ></textarea>
            </div>

            {{-- VALIDEZ DEL PRESUPUESTO --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-gray-400 text-xs font-bold uppercase tracking-wider">Validez del Presupuesto</label>
                <input 
                    type="text" 
                    wire:model="validezPresupuesto" 
                    placeholder="Ej: 15 días calendario a partir de la fecha de emisión"
                    class="bg-[#0d0d0d] border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:border-orange-500 focus:outline-none"
                >
            </div>

            {{-- EMAIL DEL CLIENTE --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-gray-400 text-xs font-bold uppercase tracking-wider">Email del Cliente (Opcional)</label>
                <input 
                    type="email" 
                    wire:model="emailCliente" 
                    placeholder="ejemplo@cliente.com"
                    class="bg-[#0d0d0d] border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:border-orange-500 focus:outline-none"
                >
            </div>

            {{-- CHECKBOXES DE INCLUSIÓN --}}
            <div class="border-t border-gray-700 pt-4">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-3">Qué Incluir en el PDF</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="incluirEmailCliente" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Incluir Email Cliente</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="incluirAlcance" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Incluir Alcance</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="incluirCondiciones" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Incluir Condiciones</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="incluirValidez" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Incluir Validez</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="incluirUnidad" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Incluir Unidad</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="incluirCantidad" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Incluir Cantidad</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="incluirPrecio" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Incluir Precio</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="incluirCargaSocial" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Incluir Carga Social</span>
                    </label>
                </div>
            </div>

            {{-- ALCANCE DE EXPORTACIÓN --}}
            <div class="border-t border-gray-700 pt-4">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-3">Alcance de Exportación</p>
                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="exportScope" wire:model="exportScope" value="rubros_subrubros" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-sm">VER RUBROS y SUB RUBROS (solo totales por rubro/subrubro)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="exportScope" wire:model="exportScope" value="completo" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-sm">VER COMPLETO (detalle completo: rubros, subrubros y recursos)</span>
                    </label>
                </div>
            </div>

        </div>

        {{-- FOOTER / BOTONES --}}
        <div class="sticky bottom-0 bg-[#1a1a1a] border-t border-gray-700 p-6 flex items-center gap-3 justify-end">
            <button
                wire:click="cerrarModalPDF"
                class="px-6 py-2 rounded-lg bg-gray-700 text-white font-bold text-xs uppercase tracking-wider hover:bg-gray-600 transition-all">
                Cancelar
            </button>
            <button 
                wire:click="exportarPDF"
                class="px-6 py-2 rounded-lg bg-orange-500 text-white font-black text-xs uppercase tracking-wider hover:bg-orange-600 transition-all">
                Generar y Exportar PDF
            </button>
        </div>

    </div>
</div>
@endif

{{-- MODAL EXPORTACIÓN EXCEL --}}
@if($mostrarModalExcel)
<div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 px-4">
    <div class="bg-[#1a1a1a] border border-gray-700 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

        {{-- HEADER --}}
        <div class="sticky top-0 bg-[#1a1a1a] border-b border-gray-700 p-6 flex items-center justify-between">
            <div>
                <h2 class="text-white font-black text-lg uppercase tracking-widest">Opciones de Exportación Excel</h2>
                <p class="text-gray-400 text-xs mt-1">📊 Proyecto: <span class="font-bold text-white">{{ $proyecto->nombre_proyecto }}</span></p>
            </div>
            <button wire:click="cerrarModalExcel" class="text-gray-500 hover:text-white text-2xl">&times;</button>
        </div>

        {{-- CONTENIDO --}}
        <div class="p-6 space-y-6">

            {{-- TÍTULO --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-gray-400 text-xs font-bold uppercase tracking-wider">Título del Reporte</label>
                <input
                    type="text"
                    wire:model="tituloExcel"
                    placeholder="Ej: REPORTE DE PRESUPUESTO"
                    class="bg-[#0d0d0d] border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:border-green-500 focus:outline-none"
                >
            </div>

            {{-- ALCANCE --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-gray-400 text-xs font-bold uppercase tracking-wider">Alcance del Presupuesto</label>
                <p class="text-gray-500 text-sm">Lo que se consideró presupuestar y lo que no</p>
                <textarea
                    wire:model="alcanceExcel"
                    placeholder="Ej: Incluye materiales, mano de obra y equipos para la fase de cimentación..."
                    class="bg-[#0d0d0d] border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:border-green-500 focus:outline-none resize-none h-24"
                ></textarea>
            </div>

            {{-- CONDICIONES --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-gray-400 text-xs font-bold uppercase tracking-wider">Condiciones</label>
                <p class="text-gray-500 text-sm">Modo de pago, moneda y condiciones comerciales</p>
                <textarea
                    wire:model="condicionesExcel"
                    placeholder="Ej: Pago 50% anticipado, saldo contra entrega. Precios en USD..."
                    class="bg-[#0d0d0d] border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:border-green-500 focus:outline-none resize-none h-24"
                ></textarea>
            </div>

            {{-- VALIDEZ --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-gray-400 text-xs font-bold uppercase tracking-wider">Validez</label>
                <p class="text-gray-500 text-sm">Tiempo de vigencia del presupuesto</p>
                <input
                    type="text"
                    wire:model="validezExcel"
                    placeholder="Ej: 30 días calendario a partir de la fecha de emisión"
                    class="bg-[#0d0d0d] border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:border-green-500 focus:outline-none"
                >
            </div>

            {{-- CHECKBOXES --}}
            <div class="border-t border-gray-700 pt-4">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-3">Columnas a incluir</p>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="excelIncluirUnidad" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Unidad</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="excelIncluirCantidad" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Cantidad</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="excelIncluirPrecio" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Precio</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="excelIncluirCargaSocial" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-xs">Carga Social</span>
                    </label>
                </div>
            </div>

            {{-- ALCANCE DE EXPORTACIÓN --}}
            <div class="border-t border-gray-700 pt-4">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-3">Alcance de Exportación</p>
                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="exportScope" wire:model="exportScope" value="rubros_subrubros" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-sm">VER RUBROS y SUB RUBROS (solo totales por rubro/subrubro)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="exportScope" wire:model="exportScope" value="completo" class="w-4 h-4 rounded">
                        <span class="text-gray-300 text-sm">VER COMPLETO (detalle completo: rubros, subrubros y recursos)</span>
                    </label>
                </div>
            </div>

        </div>

        {{-- FOOTER / BOTONES --}}
        <div class="sticky bottom-0 bg-[#1a1a1a] border-t border-gray-700 p-6 flex items-center gap-3 justify-end">
            <button
                wire:click="cerrarModalExcel"
                class="px-6 py-2 rounded-lg bg-gray-700 text-white font-bold text-xs uppercase tracking-wider hover:bg-gray-600 transition-all">
                Cancelar
            </button>
            <button
                wire:click="exportarExcel"
                class="px-6 py-2 rounded-lg bg-green-600 text-white font-black text-xs uppercase tracking-wider hover:bg-green-700 transition-all">
                Exportar a Excel
            </button>
        </div>

    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════
     MODAL: EDITAR ITEM APU
══════════════════════════════════════════════════════ --}}
@if($modalEditarItemApu)
<div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">
    <div class="bg-[#141414] border border-white/10 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <h2 class="text-white font-black text-sm uppercase tracking-widest mb-5">Editar recurso APU</h2>

        {{-- Buscador de recurso --}}
        <div class="mb-3 relative" x-data="{ q: @entangle('editItemApuNombre') }">
            <label class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-1 block">Recurso</label>
            <input
                type="text"
                x-model="q"
                x-on:input.debounce.300ms="$wire.set('editItemApuNombre', q); $wire.buscarRecursosEditarApu()"
                placeholder="Buscar recurso..."
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-purple-500/50">
            @error('editItemApuRecursoId') <p class="text-red-400 text-[11px] mt-1">Seleccioná un recurso de la lista.</p> @enderror

            @if(count($editItemApuSugeridos))
                <div class="absolute z-[9999] w-full mt-1 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl overflow-hidden">
                    @foreach($editItemApuSugeridos as $s)
                        <button
                            x-on:mousedown.prevent="q = '{{ addslashes($s['nombre']) }}'; $wire.seleccionarRecursoEditarApu({{ $s['id'] }}, '{{ addslashes($s['nombre']) }}')"
                            class="w-full text-left px-4 py-2.5 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors flex items-center justify-between">
                            <span>{{ $s['nombre'] }}</span>
                            <span class="text-gray-600 text-xs">{{ $s['unidad'] }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        @if($editItemApuRecursoId)
            @php $rEdit = \App\Models\Recurso::find($editItemApuRecursoId) @endphp
            @if($rEdit)
            <p class="text-xs text-purple-400 mb-3">✓ {{ $rEdit->nombre }} — USD {{ number_format($rEdit->precio_usd, 2) }} / {{ $rEdit->unidad }}</p>
            @endif
        @endif

        {{-- Cantidad por unidad --}}
        <div class="mb-5">
            <label class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-1 block">
                Cantidad por unidad de APU
            </label>
            <input type="text" inputmode="decimal" wire:model="editItemApuCantidad"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-purple-500/50">
            @error('editItemApuCantidad') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
            @if($editItemApuNodoCantidad != 1)
                @php $efectivo = (float) str_replace(',', '.', $editItemApuCantidad) * $editItemApuNodoCantidad @endphp
                <p class="text-gray-500 text-[11px] mt-1">
                    × {{ number_format($editItemApuNodoCantidad, 2, ',', '.') }} unidades APU
                    = <span class="text-gray-300 font-bold">{{ number_format($efectivo, 2, ',', '.') }} total</span>
                </p>
            @endif
        </div>

        <div class="flex gap-3">
            <button wire:click="cerrarModalEditarItemApu"
                class="flex-1 py-2.5 rounded-xl border border-white/10 text-gray-400 text-xs font-bold hover:bg-white/5 transition-all">
                Cancelar
            </button>
            <button wire:click="guardarItemApu"
                class="flex-1 py-2.5 rounded-xl bg-yellow-500 hover:bg-yellow-400 text-black text-xs font-black transition-all">
                Guardar
            </button>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════
     MODAL: AGREGAR ITEM APU
══════════════════════════════════════════════════════ --}}
@if($modalAgregarItemApu)
<div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">
    <div class="bg-[#141414] border border-white/10 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <h2 class="text-white font-black text-sm uppercase tracking-widest mb-5">Agregar recurso al APU</h2>

        <div class="mb-3 relative" x-data="{ q: '' }">
            <label class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-1 block">Buscar recurso</label>
            <input
                type="text"
                x-model="q"
                x-on:input.debounce.300ms="$wire.set('nuevoItemApuNombre', q); $wire.buscarRecursosAgregarApu()"
                placeholder="Ej: Arena Gruesa..."
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-purple-500/50">
            @error('nuevoItemApuRecursoId') <p class="text-red-400 text-[11px] mt-1">Seleccioná un recurso de la lista.</p> @enderror

            @if(count($nuevoItemApuSugeridos))
                <div class="absolute z-[9999] w-full mt-1 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl overflow-hidden">
                    @foreach($nuevoItemApuSugeridos as $s)
                        <button
                            x-on:mousedown.prevent="q = '{{ addslashes($s['nombre']) }}'; $wire.seleccionarRecursoAgregarApu({{ $s['id'] }}, '{{ addslashes($s['nombre']) }}')"
                            class="w-full text-left px-4 py-2.5 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors flex items-center justify-between">
                            <span>{{ $s['nombre'] }}</span>
                            <span class="text-gray-600 text-xs">{{ $s['unidad'] }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        @if($nuevoItemApuRecursoId)
            @php $rNuevo = \App\Models\Recurso::find($nuevoItemApuRecursoId) @endphp
            @if($rNuevo)
            <p class="text-xs text-purple-400 mb-3">✓ {{ $rNuevo->nombre }} — USD {{ number_format($rNuevo->precio_usd, 2) }} / {{ $rNuevo->unidad }}</p>
            @endif
        @endif

        <div class="mb-5">
            <label class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-1 block">Cantidad por unidad de APU</label>
            <input type="text" inputmode="decimal" wire:model="nuevoItemApuCantidad"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-purple-500/50">
            @error('nuevoItemApuCantidad') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button wire:click="cerrarModalAgregarItemApu"
                class="flex-1 py-2.5 rounded-xl border border-white/10 text-gray-400 text-xs font-bold hover:bg-white/5 transition-all">
                Cancelar
            </button>
            <button wire:click="guardarNuevoItemApu"
                class="flex-1 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-black transition-all">
                Agregar
            </button>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════
     MODAL: ELIMINAR ITEM APU
══════════════════════════════════════════════════════ --}}
@if($modalEliminarItemApu)
<div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">
    <div class="bg-[#141414] border border-white/10 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <h2 class="text-white font-black text-sm uppercase tracking-widest mb-2">¿Eliminar recurso del APU?</h2>
        <p class="text-gray-400 text-sm mb-5">El precio total del APU se recalculará automáticamente.</p>
        <div class="flex gap-3">
            <button wire:click="cerrarModalEliminarItemApu"
                class="flex-1 py-2.5 rounded-xl border border-white/10 text-gray-400 text-xs font-bold hover:bg-white/5 transition-all">
                Cancelar
            </button>
            <button wire:click="confirmarEliminarItemApu"
                class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white text-xs font-black transition-all">
                Eliminar
            </button>
        </div>
    </div>
</div>
@endif

{{-- JS puro: toggles + resize panel (sin Alpine) --}}
<script>
/* ── Toggle rubros/subrubros ── */
function _lwToggle(key) {
    var el  = document.getElementById('children-' + key);
    var apu = document.getElementById('children-' + key + '-apu');
    var chv = document.getElementById('chv-' + key);
    var target = el || apu;
    if (!target) return;
    var open = target.style.display !== 'none';
    if (el)  el.style.display  = open ? 'none' : '';
    if (apu) apu.style.display = open ? 'none' : '';
    if (chv) chv.style.transform = open ? '' : 'rotate(90deg)';
}

/* ── Stats panel collapse ── */
var _statsOpen = true;
var _statsH    = 130;
var _statsMin  = 44;
var _statsMax  = 320;
function _statsApply() {
    var p   = document.getElementById('stats-panel');
    var chv = document.getElementById('stats-chv');
    var txt = document.getElementById('stats-txt');
    if (p)   p.style.height = (_statsOpen ? _statsH : _statsMin) + 'px';
    if (chv) chv.style.transform = _statsOpen ? 'rotate(180deg)' : '';
    if (txt) txt.textContent = _statsOpen ? 'Ocultar' : 'Mostrar';
}

/* ── Vertical resize (stats vs tabla) ── */
var _vActive = false, _vStartY = 0, _vStartH = 0;
function _vResizeStart(e) {
    _vActive = true; _vStartY = e.clientY; _vStartH = _statsH;
    document.body.style.cursor = 'ns-resize';
    document.body.style.userSelect = 'none';
    var ln = document.getElementById('v-handle-line');
    if (ln) ln.style.background = 'rgba(168,85,247,.6)';
}

/* ── Horizontal resize (ancho panel) ── */
var _hActive = false, _hStartX = 0, _hStartW = 0;
function _hResizeStart(e) {
    _hActive = true; _hStartX = e.clientX;
    var bp = document.getElementById('budget-panel');
    _hStartW = bp ? bp.offsetWidth : window.innerWidth;
    document.body.style.cursor = 'ew-resize';
    document.body.style.userSelect = 'none';
    var bg = document.getElementById('h-handle-bg');
    var ln = document.getElementById('h-handle-line');
    if (bg) bg.style.background = 'rgba(168,85,247,.2)';
    if (ln) ln.style.background = '#a855f7';
}

/* ── Eventos globales de mousemove / mouseup ── */
window.addEventListener('mousemove', function(e) {
    if (_vActive) {
        var delta = e.clientY - _vStartY;
        _statsH = Math.max(_statsMin, Math.min(_statsMax, _vStartH + delta));
        _statsOpen = _statsH > _statsMin + 10;
        var p = document.getElementById('stats-panel');
        if (p) p.style.height = (_statsOpen ? _statsH : _statsMin) + 'px';
    }
    if (_hActive) {
        var delta = e.clientX - _hStartX;
        var container = document.getElementById('budget-panel').parentElement;
        var maxW = container ? container.offsetWidth - 8 : window.innerWidth;
        var newW = Math.max(360, Math.min(maxW, _hStartW + delta));
        var bp = document.getElementById('budget-panel');
        var ra = document.getElementById('right-area');
        if (bp) { bp.style.flex = 'none'; bp.style.width = newW + 'px'; }
        if (ra) ra.style.cssText = 'flex:1 1 0%;background:#0a0a0a';
    }
});
window.addEventListener('mouseup', function() {
    if (_vActive) {
        _vActive = false;
        document.body.style.cursor = '';
        document.body.style.userSelect = '';
        var ln = document.getElementById('v-handle-line');
        if (ln) ln.style.background = '';
        _statsApply();
    }
    if (_hActive) {
        _hActive = false;
        document.body.style.cursor = '';
        document.body.style.userSelect = '';
        var bg = document.getElementById('h-handle-bg');
        var ln = document.getElementById('h-handle-line');
        if (bg) bg.style.background = '';
        if (ln) ln.style.background = 'rgba(255,255,255,.1)';
    }
});
window.addEventListener('touchmove', function(e) {
    if (_vActive || _hActive) {
        window.dispatchEvent(new MouseEvent('mousemove', { clientX: e.touches[0].clientX, clientY: e.touches[0].clientY }));
    }
}, { passive: true });
window.addEventListener('touchend', function() {
    window.dispatchEvent(new MouseEvent('mouseup'));
});

/* ── Hover en handles ── */
document.addEventListener('mouseover', function(e) {
    var h = e.target.closest('#v-handle');
    var ln = document.getElementById('v-handle-line');
    if (ln) ln.style.background = h ? 'rgba(255,255,255,.35)' : '';

    var hh = e.target.closest('#h-handle');
    var bg = document.getElementById('h-handle-bg');
    var hl = document.getElementById('h-handle-line');
    if (bg) bg.style.background = hh ? 'rgba(255,255,255,.07)' : '';
    if (hl) hl.style.background = hh ? 'rgba(255,255,255,.3)' : 'rgba(255,255,255,.1)';
});
</script>

{{-- Script para cerrar dropdown al hacer click en la página --}}
<script>
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('[wire\\:click="toggleDropdownExportar"]')?.parentElement;
    if (dropdown && !dropdown.contains(event.target)) {
        const showDropdown = @js($mostrarDropdownExportar);
        if (showDropdown) {
            Livewire.dispatch('toggleDropdownExportar');
        }
    }
});
</script>

{{-- ════════════════════════════════════════════════════════
     MODAL: IMPORTAR PRESUPUESTO
════════════════════════════════════════════════════════ --}}
@if($modalImportarPresupuesto)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
    <div class="w-full max-w-lg bg-[#111] border border-white/10 rounded-2xl shadow-2xl overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Importar Presupuesto</h2>
            </div>
            <button wire:click="cerrarModalImportarPresupuesto" class="text-gray-500 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6 space-y-4">

            {{-- Info --}}
            <div class="bg-orange-500/10 border border-orange-500/20 rounded-lg px-4 py-3 text-[11px] text-orange-300 leading-relaxed">
                Importa un presupuesto exportado desde Rubra en formato <strong>PDF</strong>.
                Los ítems se agregarán al proyecto manteniendo la estructura de categorías, rubros y subrubros.
                Si un recurso coincide con el catálogo, se vinculará automáticamente.
            </div>

            {{-- Formato fijo: PDF --}}
            <div class="flex gap-2">
                <div class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg border text-xs font-bold uppercase tracking-wider bg-red-500/10 border-red-500/20 text-red-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF (.pdf)
                </div>
            </div>

            {{-- Upload zone --}}
            <div class="relative">
                <label class="flex flex-col items-center justify-center gap-3 p-8 border-2 border-dashed border-white/10 rounded-xl cursor-pointer hover:border-orange-500/40 hover:bg-orange-500/5 transition-all group">
                    <svg class="w-8 h-8 text-gray-600 group-hover:text-orange-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    @if($archivoImportPresupuesto)
                        <span class="text-xs text-green-400 font-bold">{{ $archivoImportPresupuesto->getClientOriginalName() }}</span>
                        <span class="text-[10px] text-gray-500">Click para cambiar</span>
                    @else
                        <span class="text-xs text-gray-400 font-bold">Arrastrá o hacé click para seleccionar</span>
                        <span class="text-[10px] text-gray-600">.pdf — Máx. 10 MB</span>
                    @endif
                    <input type="file"
                           wire:model="archivoImportPresupuesto"
                           accept=".pdf"
                           class="absolute inset-0 opacity-0 cursor-pointer">
                </label>
                @error('archivoImportPresupuesto')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Resultado --}}
            @if(!empty($importPresupuestoResult))
                @if(isset($importPresupuestoResult['ok']))
                    <div class="bg-green-500/10 border border-green-500/20 rounded-lg px-4 py-3 text-xs text-green-300 font-bold">
                        ✓ Importación exitosa — {{ $importPresupuestoResult['recursos'] ?? $importPresupuestoResult['creados'] }} ítems creados.
                    </div>
                @elseif(isset($importPresupuestoResult['error']))
                    <div class="bg-red-500/10 border border-red-500/20 rounded-lg px-4 py-3 text-xs text-red-300">
                        {{ $importPresupuestoResult['error'] }}
                    </div>
                @endif
            @endif

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-white/5 flex justify-end gap-2">
            <button wire:click="cerrarModalImportarPresupuesto"
                class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-gray-400 hover:text-white text-xs font-bold uppercase tracking-wider transition-all">
                Cancelar
            </button>
            <button wire:click="importarPresupuesto"
                wire:loading.attr="disabled"
                wire:target="importarPresupuesto,archivoImportPresupuesto"
                class="px-5 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold uppercase tracking-wider transition-all disabled:opacity-50 flex items-center gap-2">
                <span wire:loading wire:target="importarPresupuesto">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                </span>
                Importar
            </button>
        </div>

    </div>
</div>
@endif

{{-- ════════════════════════════════════════════════════════
     MODAL: ELIMINAR TODO EL PRESUPUESTO
════════════════════════════════════════════════════════ --}}
@if($modalEliminarTodo)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
    <div class="w-full max-w-sm bg-[#111] border border-red-500/30 rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-white/5">
            <div class="w-8 h-8 rounded-full bg-red-500/15 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <h2 class="text-sm font-black text-white uppercase tracking-widest">Eliminar todo el presupuesto</h2>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm text-gray-300 leading-relaxed">
                Esto eliminará <strong class="text-white">todos los rubros, subrubros y recursos</strong> del presupuesto de forma irreversible.
            </p>
            <p class="text-xs text-red-400 mt-2 font-bold">Esta acción no se puede deshacer.</p>
        </div>
        <div class="px-6 py-4 border-t border-white/5 flex justify-end gap-2">
            <button wire:click="$set('modalEliminarTodo', false)"
                class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-gray-400 hover:text-white text-xs font-bold uppercase tracking-wider transition-all">
                Cancelar
            </button>
            <button wire:click="confirmarEliminarTodo"
                wire:loading.attr="disabled"
                wire:target="confirmarEliminarTodo"
                class="px-5 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase tracking-wider transition-all disabled:opacity-50 flex items-center gap-2">
                <span wire:loading wire:target="confirmarEliminarTodo">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                </span>
                Sí, eliminar todo
            </button>
        </div>
    </div>
</div>
@endif

<livewire:proyecto.chatbot-rubi :proyecto="$proyecto" />
</div>

@script
<script>
// ── DRAG & DROP para reordenar nodos ─────────────────────────────────────────
window._dndId   = null;
window._dndLine = null;

function _dndRemoveLine() {
    if (window._dndLine && window._dndLine.parentNode) {
        window._dndLine.parentNode.removeChild(window._dndLine);
    }
    window._dndLine = null;
}

function _dndShowLine(row, before) {
    _dndRemoveLine();
    var line = document.createElement('div');
    line.style.cssText = 'height:2px;background:#a855f7;border-radius:2px;pointer-events:none;position:relative;z-index:100;margin:0 12px;';
    window._dndLine = line;
    before ? row.parentNode.insertBefore(line, row)
           : row.parentNode.insertBefore(line, row.nextSibling);
}

document.addEventListener('dragstart', function(e) {
    // No arrastrar si el origen es un botón, input, select o link
    if (e.target.closest('button, input, select, a')) {
        e.preventDefault();
        return;
    }
    var row = e.target.closest('[data-node-id][draggable]');
    if (!row) return;
    window._dndId = row.dataset.nodeId;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', window._dndId);
    setTimeout(function() { if (row) row.style.opacity = '0.4'; }, 0);
});

document.addEventListener('dragend', function() {
    if (window._dndId) {
        var row = document.querySelector('[data-node-id="' + window._dndId + '"][draggable]');
        if (row) row.style.opacity = '';
        window._dndId = null;
    }
    _dndRemoveLine();
});

document.addEventListener('dragover', function(e) {
    if (!window._dndId) return;
    var row = e.target.closest('[data-node-id][draggable]');
    if (!row || row.dataset.nodeId === window._dndId) { _dndRemoveLine(); return; }
    var draggedRow = document.querySelector('[data-node-id="' + window._dndId + '"][draggable]');
    if (!draggedRow || row.dataset.parentId !== draggedRow.dataset.parentId) { _dndRemoveLine(); return; }
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    var rect = row.getBoundingClientRect();
    _dndShowLine(row, e.clientY < rect.top + rect.height / 2);
});

document.addEventListener('drop', function(e) {
    if (!window._dndId) return;
    var row = e.target.closest('[data-node-id][draggable]');
    if (!row || row.dataset.nodeId === window._dndId) return;
    var draggedRow = document.querySelector('[data-node-id="' + window._dndId + '"][draggable]');
    if (!draggedRow || row.dataset.parentId !== draggedRow.dataset.parentId) return;
    e.preventDefault();
    var rect = row.getBoundingClientRect();
    $wire.moverNodo(parseInt(window._dndId), parseInt(row.dataset.nodeId),
                    e.clientY < rect.top + rect.height / 2 ? 'before' : 'after');
    _dndRemoveLine();
    if (draggedRow) draggedRow.style.opacity = '';
    window._dndId = null;
});
</script>
@endscript
