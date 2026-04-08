<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark"> <head>
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

    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-[#050505] text-white selection:bg-[#d15330]/30">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
        
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[1px] bg-gradient-to-r from-transparent via-[#d15330]/20 to-transparent"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#d15330]/5 blur-[120px] rounded-full"></div>

        <div class="z-10 mb-8">
            <a href="/">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="RUBRA" class="h-10 w-auto saturate-0 brightness-200">
                    <span class="font-black uppercase tracking-tighter text-2xl text-white">RUBRA</span>
                </div>
            </a>
        </div>

        <div class="w-full sm:max-w-md px-10 py-12 bg-[#0a0a0a] border border-white/5 shadow-2xl sm:rounded-[40px] relative z-10">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 blur-[50px] rounded-full -mr-16 -mt-16"></div>
            
            {{ $slot }}
        </div>

        <div class="mt-8 text-[9px] uppercase tracking-[0.3em] text-white/20 z-10">
            © {{ date('Y') }} Rubra — Del plano al precio.
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>