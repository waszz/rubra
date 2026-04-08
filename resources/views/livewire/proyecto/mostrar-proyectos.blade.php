<div>
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    
    @if(session()->has('mensaje'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-2xl text-xs font-bold uppercase tracking-widest text-center animate-fade-in">
            {{ session('mensaje') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl text-xs font-bold uppercase tracking-widest text-center animate-fade-in">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-[#0d0d0d] border border-white/5 rounded-[2rem] p-10 mb-10 relative overflow-hidden group">
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-orange-500/5 blur-[80px] rounded-full"></div>
        <div class="relative z-10">
            <h2 class="text-xl font-black text-white uppercase tracking-[0.3em] mb-2">
                Bienvenido a <span class="text-orange-500">Rubra</span>
            </h2>
            <p class="text-sm text-gray-500 font-medium tracking-wide">
                Empieza creando tu primer proyecto para gestionar tu obra.
            </p>
        </div>
    </div>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase">Proyectos</h1>
                <span class="bg-green-500/10 text-green-500 text-[10px] font-black px-2 py-0.5 rounded border border-green-500/20 flex items-center gap-1">
                    <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span>
                    ONLINE
                </span>
            </div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Gestiona tus presupuestos y cronogramas de obra.</p>
        </div>

        <div class="flex items-center gap-3 mb-2">
           <div class="flex bg-[#111111] p-1 rounded-xl border border-gray-800">
    
    <!-- GRID -->
    <button 
        wire:click="cambiarVista('grid')"
        class="p-2 rounded-lg transition-all 
        {{ $vista === 'grid' ? 'bg-[#1a1a1a] text-white shadow-sm' : 'text-gray-600 hover:text-gray-400' }}">
        
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
    </button>

    <!-- LISTA -->
    <button 
        wire:click="cambiarVista('list')"
        class="p-2 rounded-lg transition-all 
        {{ $vista === 'list' ? 'bg-[#1a1a1a] text-white shadow-sm' : 'text-gray-600 hover:text-gray-400' }}">
        
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

</div>
            <button wire:click="abrirModal" class="bg-white text-black px-6 py-3 rounded-xl font-black text-xs uppercase tracking-tight hover:bg-gray-200 transition-all shadow-xl flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                Nuevo Proyecto
            </button>
        </div>
    </div>

    {{-- CARDS + GRÁFICA --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">

        {{-- CARDS --}}
        <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-[#111111] border border-gray-800/50 p-6 rounded-[2rem] flex items-center gap-5 group hover:border-blue-500/30 transition-all">
                <div class="bg-blue-600/10 p-4 rounded-2xl shrink-0">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-[0.15em] mb-1">Proyectos Totales</p>
                    <h3 class="text-3xl font-black text-white leading-none tracking-tighter">{{ $totalProyectos }}</h3>
                    <p class="text-[10px] text-green-500 font-bold uppercase mt-1">{{ $completados }} completados</p>
                </div>
            </div>

            <div class="bg-[#111111] border border-gray-800/50 p-6 rounded-[2rem] flex items-center gap-5 group hover:border-emerald-500/30 transition-all">
                <div class="bg-emerald-600/10 p-4 rounded-2xl shrink-0">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-[0.15em] mb-1">Total m² Presupuestados</p>
                    <div class="flex items-baseline gap-1">
                        <h3 class="text-3xl font-black text-white leading-none tracking-tighter">{{ number_format($totalM2, 0, ',', '.') }}</h3>
                        <span class="text-lg font-black text-white uppercase tracking-tighter">m²</span>
                    </div>
                </div>
            </div>

            <div class="bg-[#111111] border border-gray-800/50 p-6 rounded-[2rem] flex items-center gap-5 group hover:border-orange-500/30 transition-all">
                <div class="bg-orange-600/10 p-4 rounded-2xl shrink-0">
                    <span class="text-orange-500 text-2xl font-black">$</span>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-[0.15em] mb-1">Inversión Total Presupuestada</p>
                    <h3 class="text-3xl font-black text-white leading-none tracking-tighter">
                        $ {{ number_format($inversionTotal, 2, ',', '.') }}
                    </h3>
                </div>
            </div>

            <div class="bg-[#111111] border border-gray-800/50 p-6 rounded-[2rem] flex items-center gap-5 group hover:border-cyan-500/30 transition-all">
                <div class="bg-cyan-600/10 p-4 rounded-2xl shrink-0">
                    <svg class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-[0.15em] mb-1">Ganancias Totales</p>
                    <h3 class="text-3xl font-black text-green-500 leading-none tracking-tighter">
                        $ {{ number_format($gananciasTotal, 2, ',', '.') }}
                    </h3>
                </div>
            </div>

        </div>

        {{-- GRÁFICA ESTADO --}}
        <div class="lg:col-span-4">
            <div class="bg-[#111111] border border-gray-800/50 rounded-[2rem] p-8 h-full min-h-[300px]">
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">Estado de Proyectos</h2>
                </div>

                @if($totalProyectos > 0)
                    <div class="flex justify-center">
                        <canvas id="grafica-estados" width="180" height="180"
                            data-estados='@json($estadosData)'></canvas>
                    </div>
                    <div class="mt-5 space-y-2">
                        @php
                            $colores = [
                                'en_revision' => ['bg' => 'bg-yellow-500',  'label' => 'En Revisión'],
                                'activo'      => ['bg' => 'bg-green-500',   'label' => 'Activo'],
                                'ejecucion'   => ['bg' => 'bg-orange-500',  'label' => 'Ejecución'],
                                'pausado'     => ['bg' => 'bg-gray-500',    'label' => 'Pausado'],
                                'finalizado'  => ['bg' => 'bg-blue-500',    'label' => 'Finalizado'],
                            ];
                        @endphp
                        @foreach($estadosData as $estado => $cantidad)
                            @if($cantidad > 0)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full {{ $colores[$estado]['bg'] }}"></div>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $colores[$estado]['label'] }}</span>
                                </div>
                                <span class="text-[10px] text-white font-black">{{ $cantidad }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col justify-center items-center h-48 border-2 border-dashed border-gray-800 rounded-3xl">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest">Sin datos para mostrar</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ACCESOS RÁPIDOS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
       <a href="{{ route('estadisticas') }}"
   class="bg-[#111111] border border-gray-800/50 p-5 rounded-2xl flex items-center justify-center gap-4 hover:bg-[#161616] hover:border-blue-500/50 transition-all group">
    <svg class="w-5 h-5 text-blue-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    <span class="text-xs font-black text-gray-300 uppercase tracking-widest group-hover:text-white">Estadísticas Globales</span>
</a>
       <a href="{{ route('bitacora.global') }}"
   class="bg-[#111111] border border-gray-800/50 p-5 rounded-2xl flex items-center justify-center gap-4 hover:bg-[#161616] hover:border-orange-500/50 transition-all group">
    <svg class="w-5 h-5 text-orange-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span class="text-xs font-black text-gray-300 uppercase tracking-widest group-hover:text-white">Bitácora de Obra</span>
</a>
        <a href="{{ route('mapa.proyectos') }}"
   class="bg-[#111111] border border-gray-800/50 p-5 rounded-2xl flex items-center justify-center gap-4 hover:bg-[#161616] hover:border-emerald-500/50 transition-all group">
            <svg class="w-5 h-5 text-emerald-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="text-xs font-black text-gray-300 uppercase tracking-widest group-hover:text-white">Mapa de Proyectos</span>
        </a>
    </div>

    {{-- TABLA PROYECTOS --}}
   {{-- TABLA PROYECTOS --}}
<div class="bg-[#0f0f0f] border border-gray-800/50 rounded-[2.5rem] overflow-hidden shadow-2xl">

    <div class="p-8 border-b border-gray-800/50 flex justify-between items-center bg-[#111111]/50">
        <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">
            Proyectos en curso
        </h2>
    </div>

    @if($proyectos->isEmpty())

        <div class="flex flex-col items-center justify-center py-20 gap-3">
            <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest">
                No hay proyectos todavía
            </p>
        </div>

    @else

        {{-- ================= LISTA (TU CÓDIGO ORIGINAL INTACTO) ================= --}}
        @if($vista === 'list')

            <div class="divide-y divide-gray-800/50">

                @foreach($proyectos as $proyecto)

                    <div wire:key="list-{{ $proyecto->id }}" class="p-6 flex items-center justify-between hover:bg-white/[0.02] transition-all">

                        <a href="{{ route('proyectos.presupuesto', $proyecto->id) }}"
                           class="flex items-center gap-4 flex-1 min-w-0">

                            <div class="w-10 h-10 rounded-2xl bg-orange-500/10 flex items-center justify-center shrink-0">
                                <span class="text-orange-500 font-black text-sm">
                                    {{ strtoupper(substr($proyecto->nombre_proyecto, 0, 1)) }}
                                </span>
                            </div>

                            <div>
                                <p class="text-sm font-black text-white">
                                    {{ $proyecto->nombre_proyecto }}
                                </p>

                                <p class="text-[10px] text-gray-500 uppercase tracking-wider">
                                    {{ $proyecto->cliente ?? 'Sin cliente' }} · {{ $proyecto->metros_cuadrados }} m²
                                </p>
                            </div>

                        </a>

                        <div class="flex items-center gap-4 shrink-0">

                            <div class="text-right hidden md:block">
                                <p class="text-xs font-black text-white">
                                    USD {{ number_format($totalesPorProyecto[$proyecto->id] ?? 0, 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] text-gray-500 uppercase">
                                    Precio Final
                                </p>
                            </div>

                            @php
                                $badgeColor = match($proyecto->estado_obra) {
                                    'activo'     => 'bg-green-500/10 text-green-500 border-green-500/20',
                                    'pausado'    => 'bg-gray-500/10 text-gray-500 border-gray-500/20',
                                    'finalizado' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                    'ejecucion'  => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                                    default      => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
                                };

                                $badgeLabel = match($proyecto->estado_obra) {
                                    'activo'     => 'Activo',
                                    'pausado'    => 'Pausado',
                                    'finalizado' => 'Finalizado',
                                    'ejecucion'  => 'Ejecución',
                                    default      => 'En Revisión',
                                };
                            @endphp

                            <span class="text-[10px] font-black px-3 py-1 rounded-full border uppercase tracking-wider {{ $badgeColor }}">
                                {{ $badgeLabel }}
                            </span>

                            <div class="flex items-center gap-1">


                                <button wire:click="abrirModalEditar({{ $proyecto->id }})"
                                    class="p-2 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20 transition-all"
                                    title="Editar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <button wire:click="duplicarProyecto({{ $proyecto->id }})"
                                    class="p-2 rounded-lg {{ $limiteAlcanzado ? 'bg-gray-500/10 text-gray-400 cursor-not-allowed' : 'bg-blue-500/10 text-blue-400 hover:bg-blue-500/20' }} transition-all"
                                    title="{{ $limiteAlcanzado ? 'Límite de proyectos alcanzado' : 'Duplicar' }}"
                                    {{ $limiteAlcanzado ? 'disabled' : '' }}>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16h8M8 12h8m-8-4h8M4 6h16M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6"/>
                                    </svg>
                                </button>

                                <button wire:click="confirmarEliminar({{ $proyecto->id }})"
                                    class="p-2 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-all"
                                    title="Eliminar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        {{-- ================= GRID (NUEVO SIN TOCAR TU DISEÑO DE LISTA) ================= --}}
        @else

            {{-- ================= GRID MEJORADO ================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">

    @foreach($proyectos as $proyecto)

        @php
            $badgeColor = match($proyecto->estado_obra) {
                'activo'     => 'bg-green-500/10 text-green-500 border-green-500/20',
                'pausado'    => 'bg-gray-500/10 text-gray-500 border-gray-500/20',
                'finalizado' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                'ejecucion'  => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                default      => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
            };

            $badgeLabel = match($proyecto->estado_obra) {
                'activo'     => 'Activo',
                'pausado'    => 'Pausado',
                'finalizado' => 'Finalizado',
                'ejecucion'  => 'Ejecución',
                default      => 'En Revisión',
            };
        @endphp

        <div wire:key="grid-{{ $proyecto->id }}" class="bg-[#111] border border-gray-800/60 rounded-2xl p-5 hover:bg-white/[0.03] transition-all shadow-lg flex flex-col gap-4">

            {{-- HEADER CARD --}}
            <a href="{{ route('proyectos.presupuesto', $proyecto->id) }}"
               class="flex items-start gap-4">

                <div class="w-11 h-11 rounded-2xl bg-orange-500/10 flex items-center justify-center shrink-0">
                    <span class="text-orange-500 font-black text-sm">
                        {{ strtoupper(substr($proyecto->nombre_proyecto, 0, 1)) }}
                    </span>
                </div>

                <div class="min-w-0">
                    <p class="text-sm font-black text-white truncate">
                        {{ $proyecto->nombre_proyecto }}
                    </p>

                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">
                        {{ $proyecto->cliente ?? 'Sin cliente' }}
                        · {{ $proyecto->metros_cuadrados }} m²
                    </p>
                </div>

            </a>

            {{-- INFO --}}
            <div class="flex items-center justify-between">

                <p class="text-xs font-black text-white">
                    USD {{ number_format($totalesPorProyecto[$proyecto->id] ?? 0, 0, ',', '.') }}
                </p>

                <span class="text-[10px] font-black px-3 py-1 rounded-full border uppercase tracking-wider {{ $badgeColor }}">
                    {{ $badgeLabel }}
                </span>

            </div>

            {{-- ACTIONS (EDITAR / ELIMINAR) --}}
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-800/50">
                <button
                    wire:click="abrirModalEditar({{ $proyecto->id }})"
                    class="p-2 rounded-xl bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20 transition-all"
                    title="Editar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <button
                    wire:click="duplicarProyecto({{ $proyecto->id }})"
                    class="p-2 rounded-xl {{ $limiteAlcanzado ? 'bg-gray-500/10 text-gray-400 cursor-not-allowed' : 'bg-blue-500/10 text-blue-400 hover:bg-blue-500/20' }} transition-all"
                    title="{{ $limiteAlcanzado ? 'Límite de proyectos alcanzado' : 'Duplicar' }}"
                    {{ $limiteAlcanzado ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16h8M8 12h8m-8-4h8M4 6h16M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6"/>
                    </svg>
                </button>
                <button
                    wire:click="confirmarEliminar({{ $proyecto->id }})"
                    class="p-2 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-all"
                    title="Eliminar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>

            </div>

        </div>

    @endforeach

</div>

        @endif

    @endif

</div>

    {{-- MODAL --}}
    @if($mostrarModal)
    <div class="fixed inset-0 flex items-center justify-center p-4 z-[9999] overflow-y-auto">
        <div class="fixed inset-0 bg-black/95 backdrop-blur-md" wire:click="cerrarModal"></div>
        <div class="relative bg-[#111] w-full max-w-4xl rounded-[2.5rem] border border-gray-800 shadow-2xl z-[10000] p-10 animate-in fade-in zoom-in duration-200 overflow-y-auto max-h-[90vh]">
            <button wire:click="cerrarModal" class="absolute top-8 right-8 text-gray-500 hover:text-white transition-colors text-2xl font-bold">✕</button>
            <div class="relative">
                <livewire:proyecto.crear-proyecto />
            </div>
        </div>
    </div>
    @endif

    {{-- GRÁFICA DONA --}}
    <script>
    (function() {
        function dibujarGrafica() {
            const canvas = document.getElementById('grafica-estados');
            if (!canvas) return;

            const datos = JSON.parse(canvas.dataset.estados || '{}');

            const colores = {
                en_revision : '#eab308',
                activo      : '#22c55e',
                ejecucion   : '#f97316',
                pausado     : '#6b7280',
                finalizado  : '#3b82f6',
            };

            const total = Object.values(datos).reduce((a, b) => a + b, 0);
            if (total === 0) return;

            const ctx = canvas.getContext('2d');
            const cx = 90, cy = 90, r = 72, grosor = 24;
            let angulo = -Math.PI / 2;

            ctx.clearRect(0, 0, 180, 180);

            Object.entries(datos).forEach(([estado, cantidad]) => {
                if (cantidad === 0) return;
                const slice = (cantidad / total) * 2 * Math.PI;
                ctx.beginPath();
                ctx.arc(cx, cy, r, angulo, angulo + slice);
                ctx.arc(cx, cy, r - grosor, angulo + slice, angulo, true);
                ctx.closePath();
                ctx.fillStyle = colores[estado];
                ctx.fill();
                angulo += slice;
            });

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 26px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(total, cx, cy - 8);
            ctx.font = 'bold 8px sans-serif';
            ctx.fillStyle = '#6b7280';
            ctx.fillText('PROYECTOS', cx, cy + 13);
        }

        document.addEventListener('DOMContentLoaded', dibujarGrafica);
        document.addEventListener('livewire:updated', function() {
            setTimeout(dibujarGrafica, 50);
        });
    })();
    </script>
{{-- MODAL EDITAR --}}
@if($mostrarModalEditar)
<div class="fixed inset-0 flex items-center justify-center p-4 z-[9999] overflow-y-auto">
    <div class="fixed inset-0 bg-black/95 backdrop-blur-md" wire:click="cerrarModalEditar"></div>
    <div class="relative bg-[#111] w-full max-w-4xl rounded-[2.5rem] border border-gray-800 shadow-2xl z-[10000] p-10 overflow-y-auto max-h-[90vh]">
        <button wire:click="cerrarModalEditar" class="absolute top-8 right-8 text-gray-500 hover:text-white transition-colors text-2xl font-bold">✕</button>
        <livewire:proyecto.editar-proyecto :proyecto="$proyectoEditando" :key="$proyectoEditando?->id" />
    </div>
</div>
@endif

{{-- MODAL ELIMINAR --}}
@if($mostrarModalEliminar)
<div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-md px-4">
    <div class="w-full max-w-md border border-red-500/20 rounded-2xl p-8 space-y-5 bg-[#0d0d0d] shadow-2xl text-center">
        <h2 class="text-red-400 font-extrabold text-sm uppercase">¿Eliminar proyecto?</h2>
        <p class="text-gray-500 text-xs">Esta acción no se puede deshacer.</p>
        <div class="flex gap-3 pt-2">
            <button wire:click="cerrarModalEliminar"
                class="w-1/2 py-3 rounded-xl border border-white/10 text-white text-xs font-bold hover:bg-white/5 transition-all">
                CANCELAR
            </button>
            <button wire:click="eliminarProyecto"
                class="w-1/2 bg-red-500 text-white py-3 rounded-xl font-black text-xs hover:bg-red-600 transition-all">
                ELIMINAR
            </button>
        </div>
    </div>
</div>
@endif
</div>
</div>
