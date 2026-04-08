<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
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
    </style>
</head>
<body class="font-sans antialiased bg-[#0a0a0a] text-gray-200 overflow-x-hidden">

  @if(auth()->check() && !request()->routeIs('home'))
    {{-- LAYOUT CON SIDEBAR para usuarios logueados (solo en páginas internas) --}}
    <div class="flex h-screen overflow-hidden">

        @include('layouts.navigation') 

        <div class="flex flex-col flex-1 overflow-hidden">

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