<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script>
        (function(){
            var t = localStorage.getItem('rubra_theme');
            var h = document.documentElement;
            if (t === 'light') { h.classList.remove('dark'); h.classList.add('light'); }
            else { h.classList.remove('light'); h.classList.add('dark'); }
        })();
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Rubra') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,900&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d15330">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Rubra">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    
{{-- ── LEAFLET — fuera del div Livewire para que no se re-procese ─────────── --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')

    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #ff4d00; }

        /* ── FONDO CUADRÍCULA ── */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background-image:
                linear-gradient(rgba(209,83,48,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(209,83,48,.03) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(circle at center, black, transparent 78%);
            opacity: .55;
        }

        /* ── GLOW QUE SIGUE EL MOUSE ── */
        #mouse-glow {
            position: fixed;
            width: 440px;
            height: 440px;
            pointer-events: none;
            background: radial-gradient(circle, rgba(209,83,48,.10), transparent 62%);
            z-index: 1;
            filter: blur(12px);
            transform: translate(-50%, -50%);
            top: 50%;
            left: 50%;
            transition: top .05s linear, left .05s linear;
        }

        /* ── MODO CLARO ── */
        html.light { color-scheme: light; }
        html.light body { background-color: #f4f4f5 !important; color: #18181b !important; }
        html.light body::before { opacity: 0.15; }
        html.light #mouse-glow { opacity: 0; }
        html.light ::-webkit-scrollbar-track { background: #e4e4e7; }
        html.light ::-webkit-scrollbar-thumb { background: #a1a1aa; }

        /* Fondos */
        html.light .bg-\[\#0a0a0a\], html.light .bg-\[\#090909\] { background-color: #f4f4f5 !important; }
        html.light .bg-\[\#0d0d0d\] { background-color: #ffffff !important; }
        html.light .bg-\[\#0f0f0f\] { background-color: #ebebeb !important; }
        html.light .bg-\[\#0f1115\] { background-color: #f8f8f8 !important; }
        html.light .bg-\[\#1a1a1a\] { background-color: #e4e4e7 !important; }
        html.light .bg-white\/\[0\.02\] { background-color: rgba(0,0,0,0.03) !important; }
        html.light .bg-white\/\[0\.03\] { background-color: rgba(0,0,0,0.04) !important; }
        html.light .bg-white\/\[0\.04\] { background-color: rgba(0,0,0,0.05) !important; }
        html.light .bg-white\/5  { background-color: rgba(0,0,0,0.05) !important; }
        html.light .bg-white\/10 { background-color: rgba(0,0,0,0.08) !important; }
        html.light .bg-black\/30 { background-color: rgba(0,0,0,0.04) !important; }
        html.light .bg-black\/80 { background-color: rgba(240,240,240,0.95) !important; }

        /* Hover fondos */
        html.light .hover\:bg-white\/5:hover  { background-color: rgba(0,0,0,0.05) !important; }
        html.light .hover\:bg-white\/10:hover { background-color: rgba(0,0,0,0.08) !important; }
        html.light .hover\:bg-white\/\[0\.04\]:hover { background-color: rgba(0,0,0,0.06) !important; }
        html.light .hover\:bg-white\/\[0\.03\]:hover { background-color: rgba(0,0,0,0.04) !important; }
        html.light .hover\:bg-\[\#1a1a1a\]:hover { background-color: #d4d4d8 !important; }

        /* Bordes */
        html.light .border-white\/5  { border-color: rgba(0,0,0,0.1) !important; }
        html.light .border-white\/10 { border-color: rgba(0,0,0,0.14) !important; }
        html.light .border-white\/\[0\.025\] { border-color: rgba(0,0,0,0.08) !important; }
        html.light .border-white\/\[0\.04\]  { border-color: rgba(0,0,0,0.1) !important; }
        html.light .border-white\/\[0\.05\]  { border-color: rgba(0,0,0,0.1) !important; }
        html.light .border-gray-800     { border-color: #d4d4d8 !important; }
        html.light .border-gray-800\/50 { border-color: rgba(212,212,216,0.5) !important; }

        /* Textos */
        html.light .text-white   { color: #18181b !important; }
        html.light .text-gray-200 { color: #3f3f46 !important; }
        html.light .text-gray-300 { color: #52525b !important; }
        html.light .text-gray-400 { color: #71717a !important; }
        html.light .text-gray-500 { color: #a1a1aa !important; }
        html.light .text-gray-600 { color: #71717a !important; }
        html.light .hover\:text-white:hover { color: #18181b !important; }
        html.light aside { background-color: #ebebeb !important; border-right-color: #d4d4d8 !important; }

        /* Landing: textos con opacidad */
        html.light .text-white\/40 { color: rgba(0,0,0,0.45) !important; }
        html.light .text-white\/60 { color: rgba(0,0,0,0.55) !important; }
        html.light .text-white\/80 { color: rgba(0,0,0,0.7) !important; }
        html.light .text-white\/90 { color: rgba(0,0,0,0.85) !important; }

        /* Landing header sticky fondo */
        html.light header.sticky { background-color: rgba(244,244,245,0.97) !important; }
        html.light .min-h-screen.bg-\[\#0a0a0a\] > header { background-color: rgba(244,244,245,0.97) !important; }

        /* Landing: cards fondos claros y texto oscuro */
        html.light .feature-card,
        html.light .impacto-card,
        html.light .testimonio-card,
        html.light .proceso-step {
            background-color: #ffffff !important;
            border-color: rgba(0,0,0,0.08) !important;
            color: #18181b !important;
        }
        html.light .price-card:not(.border-\[\#d15330\]\/60) {
            background-color: #ffffff !important;
            border-color: rgba(0,0,0,0.1) !important;
            color: #18181b !important;
        }
        html.light .price-card.border-\[\#d15330\]\/60 {
            background-color: #ffffff !important;
        }
        html.light .price-card:not(.bg-\[\#d15330\]) .text-white { color: #18181b !important; }

        /* Texto dentro de otros cards */
        html.light .feature-card .text-white,
        html.light .impacto-card .text-white,
        html.light .testimonio-card .text-white,
        html.light .proceso-step .text-white { color: #18181b !important; }
        html.light .feature-card .text-gray-400,
        html.light .impacto-card .text-gray-400,
        html.light .testimonio-card .text-gray-400,
        html.light .proceso-step .text-gray-400 { color: #52525b !important; }
        html.light .feature-card .text-gray-500,
        html.light .impacto-card .text-gray-500,
        html.light .testimonio-card .text-gray-500,
        html.light .proceso-step .text-gray-500 { color: #71717a !important; }
        html.light .impacto-card .text-white\/80,
        html.light .impacto-card .text-white\/60 { color: #3f3f46 !important; }
        html.light .testimonio-card .bg-white\/10 { background-color: rgba(0,0,0,0.06) !important; }

        /* Listas de checks en price cards */
        html.light .price-card:not(.bg-\[\#d15330\]) .text-white\/70,
        html.light .price-card:not(.bg-\[\#d15330\]) .text-white\/80,
        html.light .price-card:not(.bg-\[\#d15330\]) .text-white\/60 { color: #52525b !important; }
        html.light .price-card:not(.bg-\[\#d15330\]) .text-white\/30 { color: #a1a1aa !important; }
        html.light .price-card:not(.bg-\[\#d15330\]) .border-white\/10,
        html.light .price-card:not(.bg-\[\#d15330\]) .border-white\/5,
        html.light .price-card:not(.bg-\[\#d15330\]) .border-white\/20 { border-color: rgba(0,0,0,0.1) !important; }

        /* Proceso: número de paso */
        html.light .proceso-step .text-\[\#d15330\] { color: #d15330 !important; }

        /* Dashboard preview: side panel */
        html.light .dashboard-right {
            background-color: #ffffff !important;
            border-color: rgba(0,0,0,0.08) !important;
        }
        html.light .dashboard-right h4 { color: #18181b !important; }
        html.light .dashboard-right .text-white\/50 { color: rgba(0,0,0,0.5) !important; }
        html.light .dashboard-right .text-white\/70 { color: rgba(0,0,0,0.65) !important; }
        html.light .dashboard-right .border-white\/10 { border-color: rgba(0,0,0,0.12) !important; }
        html.light .dashboard-right button.border { color: #18181b !important; }
        html.light .dashboard-right .hover\:bg-white\/5:hover { background-color: rgba(0,0,0,0.05) !important; }

        /* Landing: secciones grandes con fondo oscuro */
        html.light #vision-card,
        html.light #narrativa > div,
        html.light #scroll-terminal-section > div,
        html.light #logo-3d-section > div,
        html.light #proceso > div {
            background-color: #ffffff !important;
            border-color: rgba(0,0,0,0.08) !important;
        }
        html.light #vision-card h2,
        html.light #narrativa h2,
        html.light #scroll-terminal-section h2,
        html.light #logo-3d-section h2,
        html.light #proceso h2 { color: #18181b !important; }

        html.light #vision-card p,
        html.light #narrativa p,
        html.light #scroll-terminal-section p,
        html.light #logo-3d-section p,
        html.light #proceso p { color: #52525b !important; opacity: 1 !important; }

        /* Vision words */
        html.light .vision-word { color: rgba(0,0,0,0.15) !important; }

        /* Scramble / narrativa texto */
        html.light #scramble-text { color: #18181b !important; }

        /* Terminal: mantener fondo negro */
        html.light .relative.bg-black { background-color: #1a1a1a !important; }

        /* Logo 3D canvas container */
        html.light #canvas-container { background: linear-gradient(135deg, #e4e4e7, #f4f4f5) !important; }

        /* Producto header */
        html.light #producto h2 { color: #18181b !important; }
        html.light #producto p.text-gray-400 { color: #52525b !important; opacity: 1 !important; }

        /* ── MOSTRAR PROYECTOS ── */
        /* Banner de bienvenida */
        html.light .bg-\[\#0d0d0d\].border.border-white\/5 { background-color: #ffffff !important; border-color: rgba(0,0,0,0.08) !important; }
        html.light .bg-\[\#0d0d0d\] h2 { color: #18181b !important; }
        html.light .bg-\[\#0d0d0d\] p { color: #71717a !important; }

        /* KPI cards, accesos rápidos y tabla */
        html.light .bg-\[\#111111\] {
            background-color: #ffffff !important;
        }
        html.light .bg-\[\#0f0f0f\] {
            background-color: #f4f4f5 !important;
        }
        html.light .border-gray-800\/50 { border-color: rgba(0,0,0,0.08) !important; }
        html.light .border-gray-800    { border-color: rgba(0,0,0,0.1) !important; }
        html.light .divide-gray-800\/50 > * { border-color: rgba(0,0,0,0.06) !important; }

        /* Textos dentro */
        html.light .bg-\[\#111111\] h3,
        html.light .bg-\[\#111111\] p.text-white,
        html.light .bg-\[\#111111\] span.text-white { color: #18181b !important; }
        html.light .bg-\[\#111111\] .text-gray-300,
        html.light .bg-\[\#111111\] .text-gray-400,
        html.light .bg-\[\#111111\] .text-gray-500 { color: #71717a !important; }
        html.light .bg-\[\#111111\] .hover\:text-white:hover { color: #18181b !important; }

        /* Hover de cards */
        html.light .hover\:bg-\[\#161616\]:hover { background-color: #f0f0f0 !important; }

        /* Tabla header */
        html.light .bg-\[\#111111\]\/50 { background-color: rgba(0,0,0,0.03) !important; }

        /* Número de cantidad en Estado de Proyectos */
        html.light .bg-\[\#111111\] span.text-\[10px\].text-white.font-black { color: #18181b !important; }
        html.light .bg-\[\#111111\] span.text-\[10px\].text-gray-400 { color: #71717a !important; }

        /* Filas de lista de proyectos: texto negro y hover visible */
        html.light .divide-gray-800\/50 > div .text-white { color: #18181b !important; }
        html.light .divide-gray-800\/50 > div .text-gray-500 { color: #71717a !important; }
        html.light .hover\:bg-white\/\[0\.02\]:hover { background-color: rgba(0,0,0,0.03) !important; }

        /* USD precio en lista */
        html.light .divide-gray-800\/50 > div p.text-xs.font-black.text-white { color: #18181b !important; }

        /* Footer */
        html.light footer {
            background-color: #ebebeb !important;
            border-top-color: rgba(0,0,0,0.08) !important;
        }
        html.light footer h3,
        html.light footer h4 { color: #18181b !important; }
        html.light footer .text-white\/40 { color: rgba(0,0,0,0.45) !important; }
        html.light footer .text-white\/50 { color: rgba(0,0,0,0.5) !important; }
        html.light footer .text-white\/25 { color: rgba(0,0,0,0.35) !important; }
        html.light footer .border-white\/5 { border-color: rgba(0,0,0,0.08) !important; }

        /* CTA final */
        html.light #cta-final .bg-gradient-to-br,
        html.light #cta-final [class*="from-\[#1a1a1a\]"] {
            background: #ffffff !important;
            border-color: rgba(0,0,0,0.10) !important;
        }
        html.light #cta-final h2 { color: #18181b !important; }
        html.light #cta-final p.text-gray-400 { color: #52525b !important; }
        html.light #cta-final .text-white\/60 { color: rgba(0,0,0,0.5) !important; }
        html.light #cta-final .text-white\/30 { color: rgba(0,0,0,0.35) !important; }
        html.light #cta-final .border-white\/10 { border-color: rgba(0,0,0,0.12) !important; }
        html.light #cta-final .hover\:text-white:hover { color: #18181b !important; }
    </style>
</head>
<body class="font-sans antialiased bg-[#0a0a0a] text-gray-200 overflow-x-hidden">
<div id="mouse-glow"></div>

  @if(auth()->check() && !request()->routeIs('home'))
    {{-- LAYOUT CON SIDEBAR para usuarios logueados (solo en páginas internas) --}}
    <div class="flex h-screen overflow-visible">

        @include('layouts.navigation') 

        <div class="flex flex-col flex-1 overflow-visible">

            {{-- BANNER IMPERSONACIÓN --}}
            @if(session('impersonating_from'))
            <div class="bg-purple-600 text-white text-[11px] font-bold tracking-widest uppercase text-center px-4 py-2 flex items-center justify-center gap-4 shrink-0">
                <span>👤 Estás viendo la cuenta de: <strong>{{ auth()->user()->email }}</strong></span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="underline underline-offset-2 hover:text-white/80 transition">
                        Volver a Mi Cuenta →
                    </button>
                </form>
            </div>
            @endif

            {{-- BANNER SOLO-LECTURA (trial expirado) --}}
            @auth
                @if(auth()->user()->trialExpired() && !session('impersonating_from'))
                <div class="bg-[#d15330] text-white text-[11px] font-bold tracking-widest uppercase text-center px-4 py-2 flex items-center justify-center gap-4 shrink-0">
                    <span>⚠ Tu período de prueba expiró — estás en modo solo-lectura.</span>
                    <a href="{{ route('pago.checkout', 'basico') }}" class="underline underline-offset-2 hover:text-white/80 transition">
                        Suscribirte ahora →
                    </a>
                </div>
                @endif
            @endauth

            <main class="flex-1 overflow-y-auto bg-[#0a0a0a]">
                {{ $slot }}
            </main>

        </div>
    </div>

  @else
        {{-- LAYOUT SIN SIDEBAR para la landing (visitantes y usuarios en home) --}}
        <div class="min-h-screen bg-[#0a0a0a]">

            <header class="bg-[#0a0a0a] border-b border-white/5 sticky top-0 z-[100]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-20">
                        
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="p-1.5 rounded-lg">
                                <x-application-logo class="h-5 w-5 fill-current text-white" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            @auth
                                {{-- Usuario logueado viendo la landing --}}
                                <span class="text-[10px] text-white/40 uppercase tracking-widest hidden sm:inline">
                                    {{ auth()->user()->name }}
                                </span>
                                <a href="{{ route('dashboard') }}"
                                   class="bg-[#d15330] text-white px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.2em] hover:bg-[#b04225] transition shadow-lg shadow-[#d15330]/20">
                                    Ir al Dashboard
                                </a>
                            @else
                                {{-- Visitante --}}
                                <nav class="hidden md:flex items-center gap-5">
                                    <a href="#vision"            class="text-[10px] uppercase tracking-[0.2em] font-black text-white/40 hover:text-[#d15330] transition">Visión</a>
                                    <a href="#producto"          class="text-[10px] uppercase tracking-[0.2em] font-black text-white/40 hover:text-[#d15330] transition">Producto</a>
                                    <a href="#proceso"           class="text-[10px] uppercase tracking-[0.2em] font-black text-white/40 hover:text-[#d15330] transition">Proceso</a>
                                    <a href="#dashboard-preview" class="text-[10px] uppercase tracking-[0.2em] font-black text-white/40 hover:text-[#d15330] transition">App</a>
                                    <a href="#precios"           class="text-[10px] uppercase tracking-[0.2em] font-black text-white/40 hover:text-[#d15330] transition">Precios</a>
                                </nav>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('login') }}" class="text-[10px] uppercase tracking-[0.2em] font-black text-white/90 hover:text-[#d15330] transition">Ingresar</a>
                                    <a href="{{ route('register') }}" class="bg-[#d15330] text-white px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.2em] hover:bg-[#b04225] transition shadow-lg shadow-[#d15330]/20">
                                        Empezar gratis
                                    </a>
                                </div>
                            @endauth

                            {{-- Toggle tema --}}
                            <button onclick="toggleRubraTheme()" title="Cambiar tema"
                                class="flex items-center justify-center w-8 h-8 rounded-full border border-white/10 text-gray-400 hover:text-white hover:border-white/30 transition-all">
                                <svg id="rubra-icon-moon-landing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <svg id="rubra-icon-sun-landing" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </button>
                        </div>

                    </div>
                </div>
            </header>

            <main class="w-full">
                {{ $slot }}
            </main>

        </div>
  @endif

    @livewireScripts
    @stack('scripts')
     <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

{{-- ── THEME TOGGLE GLOBAL ─────────────────────────────────────────────────── --}}
<script>
function toggleRubraTheme() {
    var h = document.documentElement;
    var isLight = h.classList.contains('light');
    if (isLight) {
        h.classList.remove('light'); h.classList.add('dark');
        localStorage.setItem('rubra_theme', 'dark');
    } else {
        h.classList.remove('dark'); h.classList.add('light');
        localStorage.setItem('rubra_theme', 'light');
    }
    updateRubraThemeUI();
}
function updateRubraThemeUI() {
    var isLight = document.documentElement.classList.contains('light');
    // Sidebar icons
    var moon  = document.getElementById('rubra-icon-moon');
    var sun   = document.getElementById('rubra-icon-sun');
    var label = document.getElementById('rubra-theme-label');
    if (moon)  { moon.classList.toggle('hidden', isLight);  sun.classList.toggle('hidden', !isLight); }
    if (label) { label.textContent = isLight ? 'Modo Oscuro' : 'Modo Claro'; }
    // Landing icons
    var moonL = document.getElementById('rubra-icon-moon-landing');
    var sunL  = document.getElementById('rubra-icon-sun-landing');
    if (moonL) { moonL.classList.toggle('hidden', isLight); sunL.classList.toggle('hidden', !isLight); }
}
document.addEventListener('DOMContentLoaded', updateRubraThemeUI);
</script>

{{-- ── CURSOR EFFECT ──────────────────────────────────────────────────────── --}}
<canvas id="cursor-canvas" style="position:fixed;inset:0;pointer-events:none;z-index:9999;"></canvas>
<script>
(function() {
    const canvas = document.getElementById('cursor-canvas');
    const ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const particles = [];
    const mouse = { x: -200, y: -200 };

    function resize() {
        canvas.width  = window.innerWidth  * dpr;
        canvas.height = window.innerHeight * dpr;
        canvas.style.width  = window.innerWidth  + 'px';
        canvas.style.height = window.innerHeight + 'px';
        ctx.scale(dpr, dpr);
    }
    resize();
    window.addEventListener('resize', resize);

    function spawnParticle() {
        particles.push({
            x: mouse.x, y: mouse.y,
            vx: (Math.random() - 0.5) * 1.8,
            vy: (Math.random() - 0.5) * 1.8,
            life: 1,
            size: Math.random() * 3 + 1
        });
        if (particles.length > 120) particles.shift();
    }

    function draw() {
        ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);
        for (let i = particles.length - 1; i >= 0; i--) {
            const p = particles[i];
            p.x += p.vx;
            p.y += p.vy;
            p.life -= 0.018;
            if (p.life <= 0) { particles.splice(i, 1); continue; }
            ctx.beginPath();
            ctx.fillStyle = 'rgba(209,83,48,' + (p.life * 0.55) + ')';
            ctx.arc(p.x, p.y, p.size * p.life, 0, Math.PI * 2);
            ctx.fill();
        }
        // Punto central
        ctx.beginPath();
        ctx.fillStyle = 'rgba(209,83,48,0.95)';
        ctx.arc(mouse.x, mouse.y, 6, 0, Math.PI * 2);
        ctx.fill();
        // Anillo exterior
        ctx.beginPath();
        ctx.strokeStyle = 'rgba(255,214,200,0.55)';
        ctx.lineWidth = 1;
        ctx.arc(mouse.x, mouse.y, 18, 0, Math.PI * 2);
        ctx.stroke();
        requestAnimationFrame(draw);
    }
    draw();

    window.addEventListener('mousemove', function(e) {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
        for (let i = 0; i < 3; i++) spawnParticle();
        // Mover glow
        var glow = document.getElementById('mouse-glow');
        if (glow) { glow.style.left = e.clientX + 'px'; glow.style.top = e.clientY + 'px'; }
    });
})();
</script>


     
<script>
function initMapa() {
    const el = document.getElementById('mapa-proyecto');
    if (!el || el._mapaInicializado) return;
    el._mapaInicializado = true;

    const mapa = L.map('mapa-proyecto').setView([-32.5, -56.0], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(mapa);

    setTimeout(() => mapa.invalidateSize(), 300);

    let marcador;

    mapa.on('click', function(e) {
        const { lat, lng } = e.latlng;

        if (marcador) {
            marcador.setLatLng(e.latlng);
        } else {
            marcador = L.marker(e.latlng, {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="
                        width:14px;height:14px;
                        background:#f97316;
                        border:2px solid #fff;
                        border-radius:50%;
                        box-shadow:0 0 6px rgba(249,115,22,.8)
                    "></div>`,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                })
            }).addTo(mapa);
        }

        document.getElementById('ubicacion_lat').value = lat.toFixed(6);
        document.getElementById('ubicacion_lng').value = lng.toFixed(6);
        document.getElementById('ubicacion_lat').dispatchEvent(new Event('input'));
        document.getElementById('ubicacion_lng').dispatchEvent(new Event('input'));

        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
            .then(r => r.json())
            .then(data => {
                const input = document.getElementById('ubicacion_texto');
                if (input) {
                    input.value = data.display_name ?? `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    input.dispatchEvent(new Event('input'));
                }
            });
    });
}

// Livewire v3: escuchar el evento modalAbierto
document.addEventListener('livewire:initialized', () => {
    Livewire.on('modalAbierto', () => {
        // El DOM todavía no tiene el div, esperamos a que aparezca
        const intervalo = setInterval(() => {
            const el = document.getElementById('mapa-proyecto');
            if (el) {
                clearInterval(intervalo);
                el._mapaInicializado = false; // resetear por si se cerró antes
                initMapa();
            }
        }, 100);
    });
});
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

{{-- ── PWA: Banner de instalación (solo planes profesional y enterprise) ──── --}}
@auth
@if(auth()->user()->canDownloadApp())
<div id="pwa-banner" style="display:none; position:fixed; bottom:0; left:0; right:0; z-index:9999;
    background:#1a1a1a; border-top:1px solid rgba(255,255,255,0.08);
    padding:14px 20px; display:none; align-items:center; justify-content:space-between; gap:12px;">
    <div style="display:flex; align-items:center; gap:12px;">
        <img src="/images/logo.png" alt="Rubra" style="width:36px; height:36px; object-fit:contain; filter:brightness(2) saturate(0);">
        <div>
            <div style="font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:1px; color:#fff;">Instalar Rubra</div>
            <div style="font-size:10px; color:rgba(255,255,255,0.4); margin-top:2px;">Agregá la app a tu pantalla de inicio</div>
        </div>
    </div>
    <div style="display:flex; gap:8px; shrink:0;">
        <button id="pwa-dismiss"
            style="padding:8px 14px; background:transparent; border:1px solid rgba(255,255,255,0.1);
            color:rgba(255,255,255,0.4); border-radius:100px; font-size:10px; font-weight:700;
            text-transform:uppercase; letter-spacing:1px; cursor:pointer;">
            No ahora
        </button>
        <button id="pwa-install"
            style="padding:8px 18px; background:#d15330; border:none; color:#fff;
            border-radius:100px; font-size:10px; font-weight:900; text-transform:uppercase;
            letter-spacing:1px; cursor:pointer;">
            Instalar
        </button>
    </div>
</div>

{{-- ── PWA: Service Worker + lógica de instalación ────────────── --}}
<script>
(function() {
    // Registrar service worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .catch(() => {});
        });
    }

    let deferredPrompt = null;
    const banner = document.getElementById('pwa-banner');

    // Capturar el evento de instalación del navegador
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        if (banner) banner.style.display = 'flex';
    });

    // Botón instalar
    const btnInstall = document.getElementById('pwa-install');
    if (btnInstall) {
        btnInstall.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
            if (banner) banner.style.display = 'none';
        });
    }

    // Botón descartar
    const btnDismiss = document.getElementById('pwa-dismiss');
    if (btnDismiss) {
        btnDismiss.addEventListener('click', () => {
            if (banner) banner.style.display = 'none';
            // No mostrar de nuevo por 7 días
            localStorage.setItem('pwa-dismissed', Date.now());
        });
    }

    // Si descartó hace menos de 7 días, no mostrar
    window.addEventListener('beforeinstallprompt', () => {
        const dismissed = localStorage.getItem('pwa-dismissed');
        if (dismissed && Date.now() - dismissed < 7 * 24 * 60 * 60 * 1000) {
            if (banner) banner.style.display = 'none';
        }
    });

    // Ocultar banner cuando ya está instalada
    window.addEventListener('appinstalled', () => {
        if (banner) banner.style.display = 'none';
    });
})();
</script>
@endif
@endauth
    
</body>
</html>