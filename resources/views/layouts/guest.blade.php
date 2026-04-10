<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Rubra') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,900&display=swap" rel="stylesheet" />
      <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d15330">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Rubra">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <script>
        (function(){
            var t = localStorage.getItem('rubra_theme');
            var h = document.documentElement;
            if (t === 'light') { h.classList.remove('dark'); h.classList.add('light'); }
            else { h.classList.remove('light'); h.classList.add('dark'); }
        })();
    </script>

    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
    @stack('styles')

    <style>
        /* ── GUEST MODO CLARO ── */
        html.light body { background-color: #f0f0f0 !important; color: #18181b !important; }

        /* Card principal */
        html.light .guest-card { background-color: #ffffff !important; border-color: rgba(0,0,0,0.08) !important; }

        /* Textos */
        html.light .guest-card .text-white { color: #18181b !important; }
        html.light .guest-card .text-white\/40 { color: rgba(0,0,0,0.45) !important; }
        html.light .guest-card .text-white\/60 { color: rgba(0,0,0,0.55) !important; }
        html.light .guest-card .text-white\/20 { color: rgba(0,0,0,0.35) !important; }
        html.light .guest-card .text-white\/30 { color: rgba(0,0,0,0.4) !important; }
        html.light .guest-card .text-gray-500 { color: #71717a !important; }

        /* Inputs */
        html.light .guest-card input[type="email"],
        html.light .guest-card input[type="password"],
        html.light .guest-card input[type="text"] {
            background-color: #f4f4f5 !important;
            border-color: rgba(0,0,0,0.15) !important;
            color: #18181b !important;
        }
        html.light .guest-card input[type="email"]:focus,
        html.light .guest-card input[type="password"]:focus,
        html.light .guest-card input[type="text"]:focus {
            border-color: #d15330 !important;
        }
        html.light .guest-card input[type="checkbox"] {
            background-color: #e4e4e7 !important;
            border-color: rgba(0,0,0,0.2) !important;
        }

        /* Dividers y bordes */
        html.light .guest-card .border-white\/5,
        html.light .guest-card .border-white\/10,
        html.light [class*="border-t border-gray-800"] { border-color: rgba(0,0,0,0.1) !important; }
        html.light .guest-card .bg-white\/5 { background-color: rgba(0,0,0,0.04) !important; }
        html.light .guest-card .bg-white\/10 { background-color: rgba(0,0,0,0.07) !important; }
        html.light .guest-card .hover\:bg-white\/10:hover { background-color: rgba(0,0,0,0.07) !important; }

        /* Forgot password: input especial */
        html.light .guest-card .bg-\[\#111111\] { background-color: #f4f4f5 !important; border-color: rgba(0,0,0,0.15) !important; }
        html.light .guest-card .border-gray-800 { border-color: rgba(0,0,0,0.12) !important; }
        html.light .guest-card .border-gray-800\/50 { border-color: rgba(0,0,0,0.08) !important; }
        html.light .guest-card .text-gray-500 { color: #71717a !important; }

        /* Botón blanco (forgot-password) */
        html.light .guest-card .bg-white.text-black { background-color: #18181b !important; color: #ffffff !important; }
        html.light .guest-card .hover\:bg-gray-200:hover { background-color: #333 !important; }

        /* Logo y copyright */
        html.light .guest-logo span { color: #18181b !important; }
        html.light .guest-copyright { color: rgba(0,0,0,0.3) !important; }
    </style>
</head>
<body class="font-sans antialiased bg-[#050505] text-white selection:bg-[#d15330]/30">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
        
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[1px] bg-gradient-to-r from-transparent via-[#d15330]/20 to-transparent"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#d15330]/5 blur-[120px] rounded-full"></div>

        <div class="z-10 mb-8">
            <a href="/" class="guest-logo">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="RUBRA" class="h-10 w-auto saturate-0 brightness-200">
                    <span class="font-black uppercase tracking-tighter text-2xl text-white">RUBRA</span>
                </div>
            </a>
        </div>

        <div class="w-full sm:max-w-md px-10 py-12 bg-[#0a0a0a] border border-white/5 shadow-2xl sm:rounded-[40px] relative z-10 guest-card">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 blur-[50px] rounded-full -mr-16 -mt-16"></div>
            
            {{ $slot }}
        </div>

        <div class="mt-8 flex items-center gap-4 z-10">
            <span class="guest-copyright text-[9px] uppercase tracking-[0.3em] text-white/20">
                © {{ date('Y') }} Rubra — Del plano al precio.
            </span>
            <button onclick="toggleRubraTheme()" title="Cambiar tema"
                class="flex items-center justify-center w-7 h-7 rounded-full border border-white/10 text-white/30 hover:text-white hover:border-white/30 transition-all">
                <svg id="rubra-icon-moon-guest" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg id="rubra-icon-sun-guest" class="w-3.5 h-3.5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
    function toggleRubraTheme() {
        var h = document.documentElement;
        var isLight = h.classList.contains('light');
        if (isLight) { h.classList.remove('light'); h.classList.add('dark'); localStorage.setItem('rubra_theme', 'dark'); }
        else { h.classList.remove('dark'); h.classList.add('light'); localStorage.setItem('rubra_theme', 'light'); }
        updateRubraThemeUI();
    }
    function updateRubraThemeUI() {
        var isLight = document.documentElement.classList.contains('light');
        var moonG = document.getElementById('rubra-icon-moon-guest');
        var sunG  = document.getElementById('rubra-icon-sun-guest');
        if (moonG) { moonG.classList.toggle('hidden', isLight); sunG.classList.toggle('hidden', !isLight); }
    }
    document.addEventListener('DOMContentLoaded', updateRubraThemeUI);
    </script>
</body>
</html>