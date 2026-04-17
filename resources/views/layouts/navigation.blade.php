<nav x-data="{ open: false }" class="relative z-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

@auth
@php $user = auth()->user(); @endphp

<div x-data="{
    sidebarOpen: false,
    sidebarCollapsed: localStorage.getItem('sb_col') === '1',
    toggleCollapse() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('sb_col', this.sidebarCollapsed ? '1' : '0');
    }
}" @keydown.escape.window="sidebarOpen = false" class="contents">

{{-- ── Backdrop móvil ──────────────────────────────────────── --}}
<div
    x-show="sidebarOpen"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[150] lg:hidden"
    style="display: none;"
></div>



<aside
    :class="sidebarOpen ? 'translate-x-0 visible' : '-translate-x-full lg:translate-x-0 invisible lg:visible'"
    class="fixed lg:relative flex flex-col w-72 h-screen bg-[#0f0f0f] border-r border-gray-800
           text-gray-400 font-sans shrink-0 z-[200] transition-all duration-300 ease-in-out">

    {{-- ───────────── TOP ───────────── --}}
    <div class="flex-none">

        {{-- LOGO --}}
       <div class="p-6 flex flex-col items-center gap-1">
    <x-application-logo class="h-4 w-4 fill-current text-white" />
    
    <span class="text-[7px] text-gray-500 font-bold tracking-widest uppercase text-center">
        Budgeting & Control
    </span>
</div>

        {{-- PLAN --}}
        <div class="mx-4 mb-4 p-4 bg-[#1a1a1a] rounded-2xl border border-gray-800/50">
            <p class="text-[9px] text-gray-500 uppercase font-bold mb-1">Plan Actual</p>
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm font-bold text-gray-200">
                    @if($user->plan === 'gratis') Modo Prueba
                    @elseif($user->plan === 'basico') Básico
                    @elseif($user->plan === 'profesional') Profesional
                    @elseif($user->plan === 'enterprise') Enterprise
                    @else {{ ucfirst($user->plan) }}
                    @endif
                </span>
                @if($user->isOnTrial())
                    <span class="bg-amber-500/20 text-amber-400 text-[9px] px-2 py-0.5 rounded-full font-bold whitespace-nowrap">
                        {{ $user->trialDaysLeft() }}d restantes
                    </span>
                @elseif($user->trialExpired() && $user->plan === 'gratis')
                    <a href="{{ url('/#precios') }}" class="bg-red-500/20 text-red-400 text-[9px] px-2 py-0.5 rounded-full font-bold whitespace-nowrap hover:bg-red-500/30 transition-colors">
                        Vencido
                    </a>
                @else
                    <span class="bg-emerald-600/20 text-emerald-400 text-[9px] px-2 py-0.5 rounded-full font-bold">
                        Activo
                    </span>
                @endif
            </div>
            <p class="text-[9px] text-gray-600 mt-1 uppercase font-semibold">
                Rol: {{ ucfirst(str_replace('_', ' ', $user->role)) }}
            </p>
            @if($user->plan !== 'enterprise')
            <a href="{{ url('/#precios') }}" class="mt-2 flex items-center gap-1 text-[9px] text-[#e85d27] hover:text-orange-400 transition-colors font-bold uppercase tracking-wider">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                {{ $user->plan === 'profesional' ? 'Subir a Enterprise' : 'Mejorar plan' }}
            </a>
            @endif
        </div>

        {{-- MIS PROYECTOS --}}
        @if($user->puede('proyectos'))
        <div class="px-3 mb-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-[#eb5e28] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-[#1a1a1a] text-gray-400' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                </svg>
                <span class="font-bold text-sm">Mis Proyectos</span>
            </a>
        </div>
        @endif

        {{-- PANEL DE ADMINISTRACIÓN (solo para role: god) --}}
        @if($user->isGod())
        <div class="px-3 mb-1">
            <a href="{{ route('panel') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                      {{ request()->routeIs('panel') ? 'bg-purple-600/20 text-purple-400 shadow-lg shadow-purple-900/20 border border-purple-500/30' : 'hover:bg-[#1a1a1a] text-gray-400' }}">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 13h2v8H3zm4-8h2v16H7zm4-2h2v18h-2zm4-2h2v20h-2zm4 4h2v16h-2zm4-4h2v20h-2z"/>
                </svg>
                <span class="font-bold text-sm">Panel Administración</span>
            </a>
        </div>
        @endif

        {{-- PROYECTOS COMPARTIDOS --}}
        @php
            $proyectosCompartidos = \App\Models\Proyecto::whereHas('usuarios', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->whereNot('user_id', $user->id)->get();
        @endphp

        @if($proyectosCompartidos->count() && $user->puedeCompartido('proyectos'))
        <div class="px-3 mb-2">
            <details class="bg-[#1a1a1a] border border-gray-800/50 rounded-xl overflow-hidden">
                <summary class="px-4 py-2.5 flex items-center gap-2 cursor-pointer select-none list-none
                                hover:bg-[#222] transition-colors">
                    <svg class="w-3 h-3 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-5M9 20H4v-2a4 4 0 015-5m4-4a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                    <span class="text-[9px] text-blue-400 uppercase font-bold tracking-widest flex-1">
                        Compartidos ({{ $proyectosCompartidos->count() }})
                    </span>
                    <svg class="w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.085l3.71-3.755a.75.75 0 111.08 1.04l-4.25 4.3a.75.75 0 01-1.08 0l-4.25-4.3a.75.75 0 01.02-1.06z"/>
                    </svg>
                </summary>

                <div class="border-t border-gray-800/50 max-h-60 overflow-y-auto">
                    @foreach($proyectosCompartidos as $pc)
                    <div class="flex items-center border-b border-gray-800/30 last:border-0 transition-colors
                                {{ request()->is('proyectos/' . $pc->id . '*') ? 'bg-[#222]' : 'hover:bg-[#222]' }}">
                        <a href="{{ route('proyectos.presupuesto', $pc) }}"
                           class="flex items-center gap-2 flex-1 min-w-0 px-4 py-2.5
                                  {{ request()->is('proyectos/' . $pc->id . '*') ? 'text-white' : 'text-gray-500 hover:text-gray-300' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-400/60 shrink-0"></div>
                            <span class="text-xs font-medium truncate">{{ $pc->nombre_proyecto }}</span>
                        </a>
                    </div>
                    @endforeach
                </div>
            </details>
        </div>
        @endif

    </div>

    {{-- ───────────── BOTTOM ───────────── --}}
    <div class="mt-auto flex flex-col px-3 pb-4 space-y-1">

        @if($user->puede('recursos'))
        <a href="{{ route('recursos.index') }}"
           class="flex items-center gap-3 px-4 py-3 hover:bg-[#1a1a1a] rounded-xl transition-colors
                  {{ request()->routeIs('recursos.index') ? 'bg-[#1a1a1a] text-white' : 'text-gray-400' }}">
            <span class="text-sm font-semibold">Recursos</span>
        </a>
        @endif

        @if(isset($proyectosCompartidos) && $proyectosCompartidos->count() && $user->puedeCompartido('recursos_compartidos'))
        <a href="{{ route('recursos.compartidos') }}"
           class="flex items-center gap-3 px-4 py-3 hover:bg-[#1a1a1a] rounded-xl transition-colors
                  {{ request()->routeIs('recursos.compartidos') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-blue-500/70 hover:text-blue-400' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-5M9 20H4v-2a4 4 0 015-5m4-4a4 4 0 100-8 4 4 0 000 8z"/>
            </svg>
            <span class="text-sm font-semibold">Recursos Compartidos</span>
        </a>
        @endif

        @if($user->puede('usuarios'))
        <a href="{{ route('usuarios') }}"
           class="flex items-center gap-3 px-4 py-3 hover:bg-[#1a1a1a] rounded-xl transition-colors">
            <span class="text-sm font-semibold">Usuarios</span>
        </a>
        @endif

        @if($user->puede('configuracion'))
        <a href="{{ route('configuracion') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                  {{ request()->routeIs('configuracion') ? 'bg-[#e85d27] text-white' : 'hover:bg-[#1a1a1a]' }}">
            <span class="text-sm font-semibold">Configuración</span>
        </a>
        @endif

        {{-- BOTÓN SALIR DE PROYECTOS COMPARTIDOS --}}
        @if(isset($proyectosCompartidos) && $proyectosCompartidos->count())
        <button
            onclick="document.getElementById('modal-salir').classList.remove('hidden')"
            class="w-full flex items-center gap-2 px-4 py-2 text-[9px] text-gray-600 hover:text-red-400
                   hover:bg-red-950/20 transition-colors uppercase tracking-widest rounded-xl">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Salir de proyectos compartidos
        </button>
        @endif

        <div class="h-px bg-gray-800/50 mx-4 my-2"></div>

        {{-- Botón de instalar app (solo planes profesional y enterprise) --}}
        <x-install-app-button />

        <div class="h-px bg-gray-800/50 mx-4 my-2"></div>

        {{-- Toggle Modo Claro / Oscuro --}}
        <button onclick="toggleRubraTheme()" id="rubra-theme-btn"
            class="w-full flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-[#1a1a1a] hover:text-white rounded-xl transition-colors">
            <svg id="rubra-icon-moon" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg id="rubra-icon-sun" class="w-4 h-4 shrink-0 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span id="rubra-theme-label" class="text-sm font-bold">Modo Claro</span>
        </button>

        <div class="h-px bg-gray-800/50 mx-4 my-2"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-red-950/30 hover:text-red-500 rounded-xl transition-colors">
                <span class="text-sm font-bold">Cerrar Sesión</span>
            </button>
        </form>

        <script>
            // Safe global for navigation-only layouts
            if (typeof updateRubraThemeUI !== 'function') {
                function updateRubraThemeUI() {}
            }
            document.addEventListener('DOMContentLoaded', updateRubraThemeUI);
        </script>

    </div>

</aside>

{{-- ── MODAL SALIR DE PROYECTOS COMPARTIDOS ───────────────────────────── --}}
@if(isset($proyectosCompartidos) && $proyectosCompartidos->count())
<div id="modal-salir" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-[200]">
    <div class="bg-[#141414] border border-[#222] rounded-2xl p-6 w-full max-w-sm shadow-2xl mx-4">

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-neutral-100">Salir de proyectos</h3>
            <button onclick="cerrarModalSalir()" class="text-neutral-500 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <p class="text-xs text-neutral-500 mb-4">Seleccioná los proyectos de los que querés salir.</p>

        <form method="POST" action="{{ route('proyectos.salir.multiple') }}">
            @csrf
            <div class="flex flex-col gap-2 max-h-60 overflow-y-auto mb-4">
                @foreach($proyectosCompartidos as $pc)
                <label class="flex items-center gap-3 px-3 py-2.5 bg-[#1a1a1a] hover:bg-[#222] rounded-lg cursor-pointer transition-colors">
                    <input
                        type="checkbox"
                        name="proyectos[]"
                        value="{{ $pc->id }}"
                        class="w-4 h-4 rounded border-[#333] bg-[#111] accent-[#e85d27] cursor-pointer"
                    />
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-neutral-300 truncate">{{ $pc->nombre_proyecto }}</p>
                        <p class="text-[10px] text-neutral-600">{{ $pc->user->name ?? 'Propietario' }}</p>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    onclick="cerrarModalSalir()"
                    class="px-4 py-2 text-xs text-neutral-400 hover:text-white border border-[#2a2a2a] rounded-lg transition-colors">
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 text-xs bg-red-600 hover:bg-red-500 text-white rounded-lg transition-colors">
                    Salir de seleccionados
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function cerrarModalSalir() {
    document.getElementById('modal-salir').classList.add('hidden');
}
document.getElementById('modal-salir').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalSalir();
});
</script>
@endif

</div>{{-- /x-data sidebarOpen --}}
@endauth

        <div class="flex items-center space-x-4">

    @guest
        <div class="hidden sm:flex items-center space-x-8">
            <div class="flex space-x-6">
                <x-nav-link href="#vision" class="text-[10px] uppercase tracking-[0.2em] font-black text-white/50 border-none hover:text-[#d15330]">Visión</x-nav-link>
                <x-nav-link href="#dashboard-preview" class="text-[10px] uppercase tracking-[0.2em] font-black text-white/50 border-none hover:text-[#d15330]">App</x-nav-link>
                <x-nav-link href="#producto" class="text-[10px] uppercase tracking-[0.2em] font-black text-white/50 border-none hover:text-[#d15330]">Producto</x-nav-link>
                <x-nav-link href="#precios" class="text-[10px] uppercase tracking-[0.2em] font-black text-white/50 border-none hover:text-[#d15330]">Precios</x-nav-link>
            </div>
            <div class="h-4 w-[1px] bg-white/10 mx-2"></div>
            <div class="flex items-center gap-6">
                <x-nav-link :href="route('login')" class="text-[10px] uppercase tracking-[0.2em] font-black text-white/90 border-none hover:text-[#d15330]">Ingresar</x-nav-link>
                <a href="{{ route('register') }}" class="bg-[#d15330] text-white px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.2em] hover:bg-[#b04225] transition shadow-lg shadow-[#d15330]/20">
                    Empezar
                </a>
            </div>
        </div>
    @endguest

    @guest
    <button @click="open = !open" class="sm:hidden p-2 text-gray-400 hover:text-white transition">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    @endguest
        </div>
        </div>
    </div>

    <!-- MOBILE MENU (solo para visitantes) -->
    @guest
    <div
        x-show="open"
        x-transition
        @click.away="open = false"
        class="sm:hidden fixed top-16 left-0 w-full bg-[#0f0f0f] border-t border-gray-800 max-h-[85vh] overflow-y-auto shadow-xl z-[90]"
    >
        <div class="pt-4 pb-6 space-y-3 px-4">

            @if(false) @else
                <div class="space-y-2">
                    <x-responsive-nav-link @click="open = false" href="#vision">Visión</x-responsive-nav-link>
                    <x-responsive-nav-link @click="open = false" href="#dashboard-preview">App</x-responsive-nav-link>
                    <x-responsive-nav-link @click="open = false" href="#producto">Producto</x-responsive-nav-link>
                    <x-responsive-nav-link @click="open = false" href="#precios">Precios</x-responsive-nav-link>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                    <x-responsive-nav-link @click="open = false" :href="route('login')">Iniciar Sesión</x-responsive-nav-link>
                    <x-responsive-nav-link @click="open = false" :href="route('register')">Crear Cuenta</x-responsive-nav-link>
                </div>
            @endif

        </div>
    </div>
    @endguest

</nav>