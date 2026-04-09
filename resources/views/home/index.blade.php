<x-app-layout>
    
   <div class="relative min-h-screen bg-[#0a0a0a] text-white selection:bg-[#d15330] overflow-x-hidden font-sans antialiased">
    <!-- GRID BACKGROUND (responsive density) -->
    <div class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none"
        style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: clamp(20px, 4vw, 40px) clamp(20px, 4vw, 40px);">
    </div>

    <!-- BLOBS (mobile scaled down for performance) -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute -top-[20%] -left-[20%] w-[250px] h-[250px] sm:w-[400px] sm:h-[400px] md:w-[500px] md:h-[500px] bg-[#d15330]/20 rounded-full blur-[80px] sm:blur-[120px]"></div>

        <div class="absolute top-[40%] -right-[25%] w-[300px] h-[300px] sm:w-[500px] sm:h-[500px] md:w-[600px] md:h-[600px] bg-orange-900/15 rounded-full blur-[100px] sm:blur-[150px]"></div>
    </div>

    <!-- PARALLAX LETTERS (FIX MOBILE OVERFLOW) -->
    <div class="absolute inset-0 z-0 pointer-events-none select-none overflow-hidden opacity-[0.18]">

        <span class="parallax-letter absolute top-[10%] left-[-10vw]
            text-[12rem] sm:text-[20rem] md:text-[35rem] lg:text-[50rem]
            font-black text-gray-700 leading-none tracking-tighter">
            R
        </span>

        <span class="parallax-letter absolute top-[20%] left-[50%]
            text-[10rem] sm:text-[15rem] md:text-[25rem] lg:text-[30rem]
            font-black text-gray-700 leading-none tracking-tighter">
            A
        </span>

        <span class="parallax-letter absolute bottom-[-10%] left-[60%]
            text-[10rem] sm:text-[18rem] md:text-[28rem] lg:text-[35rem]
            font-black text-gray-700 leading-none tracking-tighter">
            B
        </span>

        <span class="parallax-letter absolute top-[40%] right-[-15vw]
            text-[8rem] sm:text-[12rem] md:text-[20rem] lg:text-[25rem]
            font-black text-gray-700 leading-none tracking-tighter">
            U
        </span>

        <!-- DOT (hidden on very small screens) -->
        <div class="hidden sm:block absolute top-[20%] left-[15%] w-2 h-2 bg-[#d15330] rounded-full shadow-[0_0_40px_15px_rgba(209,83,48,0.35)] animate-pulse"></div>
    </div>

    <!-- HERO SECTION -->
    <section class="relative z-10 max-w-[1400px] mx-auto px-5 sm:px-10 md:px-20 py-14 sm:py-16 md:py-24
        grid grid-cols-1 md:grid-cols-[1.1fr,0.9fr] gap-10 md:gap-12 items-center">

        <!-- LEFT -->
        <div id="hero-left-content" class="text-center md:text-left">

            <!-- TAG -->
            <div class="flex items-center justify-center md:justify-start gap-3 mb-5 sm:mb-6">
                <div class="h-[1px] w-5 sm:w-8 bg-[#d15330]"></div>

                <span class="text-[7px] sm:text-[9px] uppercase tracking-[0.25em] sm:tracking-[0.4em]
                    text-[#d15330] font-bold text-center md:text-left">
                    PLATAFORMA SaaS · CONSTRUCCIÓN · 2026
                </span>
            </div>

            <!-- TITLE (fluid safe scaling) -->
            <h1 class="text-3xl sm:text-5xl md:text-[6.5rem] font-black leading-[0.95] md:leading-[0.85]
                tracking-tighter uppercase mb-5 sm:mb-6 md:mb-8">

                RUBRA<br>

                <span class="block">
                    TRANS<span class="text-[#d15330]">FORMA</span>
                </span>

                TU NEGOCIO
            </h1>

            <!-- DESCRIPTION -->
            <p class="text-gray-400 text-xs sm:text-sm md:text-base max-w-md mx-auto md:mx-0
                leading-relaxed mb-8 sm:mb-10 md:mb-12 font-light opacity-80">

                Del plano al precio, en segundos. Una plataforma de presupuestación pensada para estudios,
                constructoras y profesionales que necesitan velocidad, orden y control real sobre cada obra.
            </p>

            <!-- BUTTONS -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-center justify-center md:justify-start">

                <a href="{{ auth()->check() ? route('dashboard') : route('register') }}"
                    class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-white text-black font-black uppercase
                    text-[9px] sm:text-[10px] tracking-[0.2em] hover:bg-[#d15330] hover:text-white
                    transition-all duration-300 transform hover:-translate-y-1 text-center">

                    Probar la App
                </a>

                <a href="#vision"
                    class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 border border-gray-700 text-gray-400
                    font-bold uppercase text-[9px] sm:text-[10px] tracking-[0.2em]
                    hover:bg-white hover:text-black transition-all">

                    Descubrir más
            </a>
            </div>
        </div>

     <div class="block relative group w-full max-w-[95vw] mx-auto md:mx-0 md:scale-95 md:origin-right">

    <div class="absolute -inset-1.5 bg-gradient-to-r from-[#d15330]/20 to-red-600/20 rounded-xl blur-2xl
        opacity-0 group-hover:opacity-100 transition duration-1000"></div>

    <div id="terminal-viva"
        class="relative bg-[#111111]/90 backdrop-blur-xl border border-white/5 rounded-xl
        p-4 sm:p-6 shadow-[0_35px_60px_-15px_rgba(0,0,0,0.9)]
        font-mono text-[10px] sm:text-[12px] leading-relaxed
        overflow-x-auto">

        <div class="flex items-center gap-1.5 mb-5 sm:mb-6">
            <div class="w-2.5 h-2.5 rounded-full bg-[#ff5f56]"></div>
            <div class="w-2.5 h-2.5 rounded-full bg-[#ffbd2e]"></div>
            <div class="w-2.5 h-2.5 rounded-full bg-[#27c93f]"></div>

            <span class="ml-3 sm:ml-4 text-[8px] sm:text-[9px] text-gray-600 uppercase tracking-widest font-medium">
                rubra / terminal
            </span>
        </div>

        <div class="space-y-2 text-gray-400 text-[11px] sm:text-[12px]">

            <p><span class="text-[#d15330] font-bold">></span> cargar proyecto
                <span class="text-orange-400 font-bold">Laguna Cisnes</span>
            </p>

            <p class="text-gray-600 italic">procesando rubrado...</p>

            <p class="text-green-500/80 font-bold">✓ Materiales OK</p>
            <p class="text-green-500/80 font-bold">✓ Gantt vinculado</p>

            <p><span class="text-[#d15330] font-bold">></span> generar presupuesto</p>

            <p class="text-[#d15330] font-black text-base sm:text-xl pt-2">
                → USD 128.460
            </p>

        </div>
    </div>
</div>
    </section>


      <section id="vision" class="relative py-16 md:py-20 overflow-hidden">

    <!-- 🔶 MARQUEE -->
    <div class="relative z-20 mb-16 md:mb-24">
        <div class="bg-[#d15330] py-2 md:py-3 scale-100 md:scale-105 border-y border-black/20 shadow-2xl">
            <div class="overflow-hidden">
                <div class="flex whitespace-nowrap animate-marquee items-center will-change-transform">

                    @php
                        $tags = [
                            "Control Presupuestario vs Real",
                            "Bitácora y Fotos de Obra",
                            "Gantt y Camino Crítico",
                            "Presupuestación Inteligente",
                            "Base de Materiales en Vivo",
                            "Rubrado por Partidas",
                            "Exportación PDF / Excel"
                        ];
                    @endphp

                    @foreach(array_merge($tags, $tags) as $tag)
                        <span class="flex items-center text-[9px] md:text-[10px] font-black uppercase tracking-[0.15em] md:tracking-[0.2em] text-white mx-6 md:mx-8 leading-[1]">
                            <span class="mr-6 md:mr-8 text-black/40 flex items-center">◆</span>
                            {{ $tag }}
                        </span>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    <!-- 🔷 VISIÓN -->
    <div class="relative z-10 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-10">

        <div id="vision-card"
            class="bg-[#111111]/40 backdrop-blur-sm border border-white/5 rounded-[24px] md:rounded-[40px] p-6 sm:p-10 md:p-20 relative overflow-hidden group">

            <div class="absolute top-0 right-0 w-48 md:w-64 h-48 md:h-64 bg-[#d15330]/5 rounded-full blur-[80px]"></div>

            <div class="relative z-10">

                <div class="flex items-center gap-3 mb-6 md:mb-8">
                    <div class="h-[1px] w-6 bg-[#d15330]"></div>
                    <span class="text-[10px] uppercase tracking-[0.4em] text-[#d15330] font-bold">Visión</span>
                </div>

                <!-- TITLE -->
                <h2 id="vision-title"
                    class="text-3xl sm:text-4xl md:text-7xl font-black uppercase leading-[0.95] tracking-tighter mb-8 md:mb-10">

                    <span class="word">Dejar</span>
                    <span class="word">atrás</span>
                    <span class="word">el</span>
                    <span class="word">presupuesto</span>
                    <span class="word text-[#d15330]">lento,</span><br>

                    <span class="word text-[#d15330]">suelto</span>
                    <span class="word text-[#d15330]">y</span>
                    <span class="word text-[#d15330]">desordenado</span>
                </h2>

                <p class="text-gray-400 text-sm md:text-lg max-w-2xl leading-relaxed mb-10 md:mb-16 opacity-70 font-light">
                    RUBRA nace para unir en una sola plataforma lo que hoy suele estar repartido entre planillas, mensajes, carpetas y memoria. Todo el ciclo del costo, del insumo al control de obra.
                </p>

                <!-- WORD GRID -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-x-6 md:gap-x-20 gap-y-6 md:gap-y-10" id="vision-words">

                    @foreach(['Precisión', 'Velocidad', 'Control', 'Rentabilidad', 'Datos', 'Equipo', 'Seguimiento', 'Escala', 'Exportación', 'Decisión', 'Obra', 'Orden'] as $word)
                        <span class="vision-word text-xl sm:text-2xl md:text-4xl font-black uppercase tracking-tighter text-white/20 cursor-default">
                            {{ $word }}
                        </span>
                    @endforeach

                </div>

            </div>
        </div>
    </div>

   <!--  NARRATIVA CINÉTICA -->
<div id="narrativa"
    class="relative z-10 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-10 mt-16 md:mt-24">

    <div class="bg-[#111111]/40 backdrop-blur-sm border border-white/5
        rounded-2xl sm:rounded-[32px] md:rounded-[40px]
        p-6 sm:p-10 md:p-16 lg:p-20
        relative overflow-hidden">

        <!-- glow -->
        <div class="absolute top-0 right-0 w-48 sm:w-64 md:w-96 h-48 sm:h-64 md:h-96
            bg-[#d15330]/5 rounded-full blur-[80px] md:blur-[120px] pointer-events-none"></div>

        <div class="relative z-10">

            <!-- TEXT CONTAINER -->
            <div class="flex items-center justify-center min-h-[6rem] sm:min-h-[8rem] md:min-h-[12rem]">

   <p id="scramble-text"
   class="text-2xl sm:text-4xl md:text-[5.5rem] 
   font-black uppercase 
   leading-[1.1] md:leading-[1] 
   tracking-tight md:tracking-tighter
   text-white text-center break-words py-4">
</p>

            </div>

        </div>
    </div>
</div>

    <!-- 🖥 TERMINAL -->
    <div id="scroll-terminal-section"
        class="relative z-10 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-10 mt-16 md:mt-24 pb-16 md:pb-24">

        <div class="bg-[#111111]/40 backdrop-blur-sm border border-white/5 rounded-[24px] md:rounded-[40px] p-6 sm:p-10 md:p-20 relative overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">

            <!-- TEXT -->
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-[1px] w-6 bg-[#d15330]"></div>
                    <span class="text-[10px] uppercase tracking-[0.4em] text-[#d15330] font-bold">Secuencia</span>
                </div>

                <h2 class="text-3xl sm:text-4xl md:text-6xl font-black uppercase leading-[0.95] tracking-tighter mb-6">
                    Una terminal que se <span class="text-[#d15330]">escribe con tu scroll</span>
                </h2>

                <p class="text-gray-400 text-sm md:text-base opacity-70 font-light max-w-sm">
                    Ideal para reforzar la sensación de sistema técnico, precisión operativa y lógica de software serio.
                </p>
            </div>

            <!-- TERMINAL -->
            <div class="relative group">

                <div class="absolute -inset-1 bg-[#d15330]/20 rounded-xl blur-xl opacity-0 group-hover:opacity-100 transition duration-700"></div>

                <div class="relative bg-black border border-white/10 rounded-xl p-6 font-mono text-xs md:text-sm min-h-[250px] md:min-h-[300px] shadow-2xl">

                    <div class="flex items-center gap-1.5 mb-6 border-b border-white/5 pb-4">
                        <div class="w-2.5 h-2.5 rounded-full bg-[#ff5f56]"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-[#ffbd2e]"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-[#27c93f]"></div>
                        <span class="ml-4 text-[9px] text-gray-600 uppercase tracking-widest">
                            rubra / live scroll typing
                        </span>
                    </div>

                    <div id="typing-content" class="text-gray-300 leading-relaxed">
                        <span class="text-[#d15330] font-bold">></span>
                        <span id="typed-text"></span>
                        <span class="animate-pulse bg-[#d15330] ml-1 px-1 text-transparent">|</span>
                    </div>

                </div>
            </div>

        </div>
    </div>

</section>

        <section id="logo-3d-section" class="relative z-10 max-w-[1200px] mx-auto px-10 mt-24 pb-24">
    <div class="bg-[#111111]/40 backdrop-blur-sm border border-white/5 rounded-[40px] p-12 md:p-20 relative overflow-hidden grid md:grid-cols-2 gap-12 items-center">
        
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-[1px] w-6 bg-[#d15330]"></div>
                <span class="text-[10px] uppercase tracking-[0.4em] text-[#d15330] font-bold">Logo 3D</span>
            </div>
            <h2 class="text-4xl md:text-6xl font-black uppercase leading-[0.9] tracking-tighter mb-8">
                La R Una Firma <br><span class="text-[#d15330]"> de Nueva Generación</span>
            </h2>
            <p class="text-gray-400 text-base opacity-70 font-light max-w-sm leading-relaxed">
            Geometría interactiva en tiempo real. Mediante el uso de WebGL, nuestra identidad visual evoluciona con tu navegación, fusionando la solidez de la ingeniería tradicional con la agilidad de un ecosistema digital de vanguardia
            </p>
        </div>

      <div id="canvas-container" class="relative h-[400px] md:h-[500px] w-full rounded-3xl overflow-hidden bg-gradient-to-br from-black to-[#111111]">
    <div class="absolute inset-0 z-0 opacity-20" 
         style="background-image: radial-gradient(#d15330 0.5px, transparent 0.5px); background-size: 20px 20px;">
    </div>

    <!-- glow ambiental detrás de la R -->
    <div class="absolute inset-0 z-0 pointer-events-none"
         style="background: radial-gradient(ellipse 60% 50% at 54% 50%, rgba(209,83,48,0.18) 0%, transparent 70%);">
    </div>
</div>


    </div>
</section>

{{-- SECCIÓN PRODUCTO --}}
<section id="producto" class="relative z-10 max-w-[1200px] mx-auto px-10 mt-24 pb-24">

    {{-- Header --}}
 <div class="mb-10 md:mb-14">

    <!-- LABEL -->
    <div class="flex items-center gap-3 mb-4 md:mb-6">
        <div class="h-[1px] w-6 bg-[#d15330]"></div>
        <span class="text-[10px] uppercase tracking-[0.3em] md:tracking-[0.4em] text-[#d15330] font-bold">
            Producto
        </span>
    </div>

    <!-- TITLE -->
    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-[5.5rem] font-black uppercase leading-[0.95] md:leading-[0.9] tracking-tighter mb-4 md:mb-6">

        Una herramienta técnica con estética<br class="hidden sm:block">

        <span class="text-[#d15330]">contemporánea</span>

    </h2>

    <!-- PARAGRAPH -->
    <p class="text-gray-400 text-sm sm:text-base max-w-2xl leading-relaxed font-light opacity-80">
        Pensada para que presupuestar no sea solo cargar números, sino construir una base confiable
        para vender mejor, ejecutar con criterio y controlar los desvíos durante toda la obra.
    </p>

</div>

    {{-- Grid de cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        @php
        $features = [
            [
                'num'   => '01',
                'icon'  => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
                'title' => 'Rubrado Completo',
                'desc'  => 'Crea rubros, subrubros y partidas con una lógica ordenada. Genera estructuras reutilizables y adapta cada presupuesto según tipología, sistema constructivo y escala.',
            ],
            [
                'num'   => '02',
                'icon'  => '<rect x="3" y="3" width="18" height="4" rx="1"/><rect x="3" y="10" width="18" height="4" rx="1"/><rect x="3" y="17" width="18" height="4" rx="1"/>',
                'title' => 'Materiales y Composiciones',
                'desc'  => 'Base propia con insumos precargados, precios históricos y composiciones editables. Importa, duplica, ajusta rendimientos y trabaja con criterios técnicos reales.',
            ],
            [
                'num'   => '03',
                'icon'  => '<path d="M12 2v10l4 4"/><circle cx="12" cy="12" r="10"/>',
                'title' => 'Control Presupuestado vs Real',
                'desc'  => 'Compara lo previsto con lo ejecutado, registra desvíos y corrige decisiones a tiempo. Una vista clara para gestión económica y operativa.',
            ],
            [
                'num'   => '04',
                'icon'  => '<path d="M7 16V4m0 0L3 8m4-4 4 4"/><path d="M17 8v12m0 0 4-4m-4 4-4-4"/>',
                'title' => 'Importación y Exportación',
                'desc'  => 'Lleva y trae datos por Excel, comparte presupuestos en PDF y mantiene trazabilidad documental por proyecto, cliente o etapa de obra.',
            ],
            [
                'num'   => '05',
                'icon'  => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4v5M16 4v5"/>',
                'title' => 'Gantt y Camino Crítico',
                'desc'  => 'Relaciona costos con tiempos, tareas y dependencias. Detecta impactos sobre el cronograma y visualiza prioridades para una ejecución más fina.',
            ],
            [
                'num'   => '06',
                'icon'  => '<circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>',
                'title' => 'Bitácora de Obra',
                'desc'  => 'Adjunta boletas, recibos, fotos, observaciones y respaldos. Todo queda centralizado para consulta, control y seguimiento del proyecto.',
            ],
        ];
        @endphp

        @foreach($features as $f)
        <div class="feature-card group relative bg-[#111111]/60 border border-white/5 rounded-2xl p-8 overflow-hidden
                    hover:border-[#d15330]/30 hover:bg-[#111111]/90 transition-all duration-500 cursor-default">

            {{-- Número grande de fondo --}}
            <span class="absolute top-4 right-6 text-[5.5rem] font-black text-white/[0.04] leading-none select-none
                         group-hover:text-[#d15330]/10 transition-colors duration-500 tracking-tighter">
                {{ $f['num'] }}
            </span>

            {{-- Ícono --}}
            <div class="relative z-10 mb-6 w-10 h-10 flex items-center justify-center
                        bg-[#d15330]/10 border border-[#d15330]/20 rounded-lg
                        group-hover:bg-[#d15330]/20 group-hover:border-[#d15330]/40 transition-all duration-300">
                <svg class="w-5 h-5 text-[#d15330]" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    {!! $f['icon'] !!}
                </svg>
            </div>

            {{-- Título --}}
            <h3 class="relative z-10 text-[11px] font-black uppercase tracking-[0.15em] text-white mb-3">
                {{ $f['title'] }}
            </h3>

            {{-- Descripción --}}
            <p class="relative z-10 text-gray-500 text-sm leading-relaxed font-light
                       group-hover:text-gray-400 transition-colors duration-300">
                {{ $f['desc'] }}
            </p>

            {{-- Línea inferior hover --}}
            <div class="absolute bottom-0 left-0 h-[2px] w-0 bg-[#d15330]
                        group-hover:w-full transition-all duration-500 ease-out"></div>

        </div>
        @endforeach

    </div>
</section>

{{-- SECCIÓN PROCESO --}}
<section id="proceso" class="relative z-10 max-w-[1200px] mx-auto px-10 mt-24 pb-24">
    <div class="bg-[#111111]/40 backdrop-blur-sm border border-white/5 rounded-[40px] p-12 md:p-20 relative overflow-hidden">
 
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#d15330]/5 rounded-full blur-[100px] pointer-events-none"></div>
 
        <div class="relative z-10 grid md:grid-cols-2 gap-16 items-start">
 
            {{-- LEFT: header + steps --}}
            <div>
                <div class="flex items-center gap-3 mb-8">
                    <div class="h-[1px] w-6 bg-[#d15330]"></div>
                    <span class="text-[10px] uppercase tracking-[0.4em] text-[#d15330] font-bold">Proceso</span>
                </div>
 
                <h2 class="text-3xl md:text-5xl font-black uppercase leading-[0.88] tracking-tighter mb-8">
                    Del dato disperso<br>
                    al <span class="text-[#d15330]">presupuesto</span><br>
                    <span class="text-[#d15330]">ejecutivo</span>
                </h2>
 
                <p class="text-gray-400 text-base leading-relaxed font-light opacity-80 mb-12 max-w-sm">
                    RUBRA organiza el trabajo en una secuencia clara para evitar rehacer,
                    perder información o depender de planillas aisladas.
                </p>
 
                {{-- Steps --}}
                <div class="space-y-3" id="proceso-steps">
                    @php
                    $steps = [
                        [
                            'num'   => '01',
                            'title' => 'Definición del Proyecto',
                            'desc'  => 'Alta de obra, cliente, sistema constructivo y estructura general del presupuesto.',
                        ],
                        [
                            'num'   => '02',
                            'title' => 'Carga Técnica y Económica',
                            'desc'  => 'Materiales, composiciones, mano de obra, importaciones y rendimientos asociados.',
                        ],
                        [
                            'num'   => '03',
                            'title' => 'Generación y Análisis',
                            'desc'  => 'Estadísticas, márgenes, cronograma, reportes y simulaciones para decisión comercial.',
                        ],
                        [
                            'num'   => '04',
                            'title' => 'Seguimiento de Obra',
                            'desc'  => 'Control de compras, respaldos, bitácora y desviaciones para una gestión activa del presupuesto.',
                        ],
                    ];
                    @endphp
 
                    @foreach($steps as $i => $step)
                    <div class="proceso-step group flex items-start gap-5 border border-white/5 rounded-2xl px-6 py-5
                                bg-[#0d0d0d]/60 hover:border-[#d15330]/30 hover:bg-[#111]/80
                                transition-all duration-300 cursor-default">
 
                        {{-- Badge número --}}
                        <div class="shrink-0 w-9 h-9 rounded-lg bg-[#d15330]/10 border border-[#d15330]/25
                                    flex items-center justify-center
                                    group-hover:bg-[#d15330]/20 group-hover:border-[#d15330]/50 transition-all duration-300">
                            <span class="text-[10px] font-black text-[#d15330] tracking-widest">{{ $step['num'] }}</span>
                        </div>
 
                        <div>
                            <h4 class="text-[11px] font-black uppercase tracking-[0.15em] text-white mb-1.5">
                                {{ $step['title'] }}
                            </h4>
                            <p class="text-gray-500 text-sm leading-relaxed font-light group-hover:text-gray-400 transition-colors duration-300">
                                {{ $step['desc'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
 
            {{-- RIGHT: curva SVG animada --}}
            <div class="hidden md:flex items-center justify-center pt-16">
                <svg id="proceso-curve" viewBox="0 0 400 320" class="w-full max-w-[420px]"
                     fill="none" xmlns="http://www.w3.org/2000/svg">
 
                    <defs>
                        <linearGradient id="curveGrad" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="#d15330" stop-opacity="0.3"/>
                            <stop offset="50%" stop-color="#d15330" stop-opacity="1"/>
                            <stop offset="100%" stop-color="#d15330" stop-opacity="0.6"/>
                        </linearGradient>
                    </defs>
 
                    {{-- Curva principal --}}
                    <path id="main-curve"
                          d="M 30 240 C 80 240 100 60 160 60 C 220 60 240 200 290 200 C 330 200 350 120 380 100"
                          stroke="url(#curveGrad)"
                          stroke-width="2.5"
                          stroke-linecap="round"
                          fill="none"
                          class="curve-path"/>
 
                    {{-- Puntos en la curva --}}
                    {{-- INPUT --}}
                    <circle cx="30" cy="240" r="7" fill="#d15330" class="curve-dot" style="animation-delay: 0s"/>
                    <circle cx="30" cy="240" r="14" fill="#d15330" fill-opacity="0.15" class="curve-dot-ring" style="animation-delay: 0s"/>
                    <text x="30" y="268" text-anchor="middle" fill="#888" font-size="10"
                          font-family="monospace" letter-spacing="0.1em" class="curve-label">INPUT</text>
 
                    {{-- RUBRADO --}}
                    <circle cx="160" cy="60" r="7" fill="#d15330" class="curve-dot" style="animation-delay: 0.3s"/>
                    <circle cx="160" cy="60" r="14" fill="#d15330" fill-opacity="0.15" class="curve-dot-ring" style="animation-delay: 0.3s"/>
                    <text x="160" y="40" text-anchor="middle" fill="#888" font-size="10"
                          font-family="monospace" letter-spacing="0.1em" class="curve-label">RUBRADO</text>
 
                    {{-- CONTROL --}}
                    <circle cx="290" cy="200" r="7" fill="#d15330" class="curve-dot" style="animation-delay: 0.6s"/>
                    <circle cx="290" cy="200" r="14" fill="#d15330" fill-opacity="0.15" class="curve-dot-ring" style="animation-delay: 0.6s"/>
                    <text x="290" y="228" text-anchor="middle" fill="#888" font-size="10"
                          font-family="monospace" letter-spacing="0.1em" class="curve-label">CONTROL</text>
 
                    {{-- SALIDA --}}
                    <circle cx="380" cy="100" r="7" fill="#d15330" class="curve-dot" style="animation-delay: 0.9s"/>
                    <circle cx="380" cy="100" r="14" fill="#d15330" fill-opacity="0.15" class="curve-dot-ring" style="animation-delay: 0.9s"/>
                    <text x="380" y="80" text-anchor="middle" fill="#888" font-size="10"
                          font-family="monospace" letter-spacing="0.1em" class="curve-label">SALIDA</text>
 
                </svg>
            </div>
 
        </div>
    </div>
</section>

<section id="impacto-section" class="max-w-[1200px] mx-auto px-10 py-24">
    <div class="mb-16">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-[1px] w-6 bg-[#d15330]"></div>
            <span class="text-[10px] uppercase tracking-[0.4em] text-[#d15330] font-bold">Impacto</span>
        </div>
        <h2 class="text-4xl md:text-6xl font-black uppercase leading-[0.9] tracking-tighter">
            Diseñada para mejorar tiempo, claridad y <span class="text-[#d15330]">rentabilidad</span>
        </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="impacto-card bg-[#111111]/40 backdrop-blur-sm border border-white/5 rounded-[40px] p-8 flex flex-col items-center text-center">
            <div class="relative w-32 h-32 mb-8">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5" />
                    <circle cx="64" cy="64" r="58" stroke="#d15330" stroke-width="8" fill="transparent" 
                        stroke-dasharray="364.4" stroke-dashoffset="364.4" class="progress-circle" data-percent="70" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-3xl font-black counter" data-target="70">0</span><span class="text-[#d15330] text-3xl font-black">%</span>
                </div>
            </div>
            <p class="text-[10px] uppercase tracking-widest font-bold opacity-50">Menos tiempo operativo</p>
        </div>

        <div class="impacto-card bg-[#111111]/40 backdrop-blur-sm border border-white/5 rounded-[40px] p-8 flex flex-col items-center text-center">
            <div class="relative w-32 h-32 mb-8">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5" />
                    <circle cx="64" cy="64" r="58" stroke="#d15330" stroke-width="8" fill="transparent" 
                        stroke-dasharray="364.4" stroke-dashoffset="364.4" class="progress-circle" data-percent="100" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-3xl font-black counter" data-target="3">0</span><span class="text-[#d15330] text-3xl font-black">x</span>
                </div>
            </div>
            <p class="text-[10px] uppercase tracking-widest font-bold opacity-50">Más velocidad para cotizar</p>
        </div>

        <div class="impacto-card bg-[#111111]/40 backdrop-blur-sm border border-white/5 rounded-[40px] p-8 flex flex-col items-center text-center">
            <div class="relative w-32 h-32 mb-8">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5" />
                    <circle cx="64" cy="64" r="58" stroke="#d15330" stroke-width="8" fill="transparent" 
                        stroke-dasharray="364.4" stroke-dashoffset="364.4" class="progress-circle" data-percent="100" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center text-3xl font-black">
                    <span class="counter" data-target="24">0</span><span class="text-[#d15330]">/</span><span class="counter" data-target="7">0</span>
                </div>
            </div>
            <p class="text-[10px] uppercase tracking-widest font-bold opacity-50">Acceso desde cualquier lugar</p>
        </div>

        <div class="impacto-card bg-[#111111]/40 backdrop-blur-sm border border-white/5 rounded-[40px] p-8 flex flex-col items-center text-center">
            <div class="relative w-32 h-32 mb-8">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5" />
                    <circle cx="64" cy="64" r="58" stroke="#d15330" stroke-width="8" fill="transparent" 
                        stroke-dasharray="364.4" stroke-dashoffset="364.4" class="progress-circle" data-percent="85" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-3xl font-black counter" data-target="100">0</span><span class="text-[#d15330] text-3xl font-black">+</span>
                </div>
            </div>
            <p class="text-[10px] uppercase tracking-widest font-bold opacity-50">Insumos y composiciones base</p>
        </div>
    </div>
</section>

<section id="dashboard-preview" class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-10 py-16 md:py-24">

    <!-- HEADER -->
    <div class="mb-10 md:mb-12">

        <h2 class="text-3xl sm:text-4xl md:text-6xl font-black uppercase leading-[0.95] tracking-tighter mb-4 md:mb-6">
            Una interfaz que se siente técnica, clara y
            <span class="text-[#d15330]">usable</span>
        </h2>

        <p class="text-gray-400 max-w-2xl text-sm md:text-base leading-relaxed">
            Lleva la presentación de tus presupuestos al siguiente nivel. Reportes claros, con marca propia y desgloses de carga social que generan confianza inmediata en tus clientes
        </p>

    </div>

    <!-- GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12 items-start">

        <!-- MOCKUP -->
        <div class="lg:col-span-7 dashboard-col rounded-[20px] md:rounded-[32px] border border-white/5 overflow-hidden shadow-2xl">

            <!-- TOP BAR -->
            <div class="bg-[#111111] px-4 md:px-6 py-3 md:py-4 flex items-center justify-between border-b border-white/5">
                <div class="flex gap-2">
                    <div class="w-2.5 md:w-3 h-2.5 md:h-3 rounded-full bg-[#ff5f56]"></div>
                    <div class="w-2.5 md:w-3 h-2.5 md:h-3 rounded-full bg-[#ffbd2e]"></div>
                    <div class="w-2.5 md:w-3 h-2.5 md:h-3 rounded-full bg-[#27c93f]"></div>
                </div>
                <div class="text-[9px] md:text-[10px] text-white/20 font-mono tracking-widest truncate">
                    app.rubra.uy/dashboard
                </div>
            </div>

            <!-- SCREENSHOT -->
            <img
                src="/images/dashboard-screenshot.png"
                alt="Dashboard Rubra"
                class="w-full object-cover object-top block"
            >

        </div>

        <!-- SIDE PANEL -->
        <div class="lg:col-span-5 dashboard-right bg-[#111111]/40 backdrop-blur-sm border border-white/5 rounded-[24px] md:rounded-[40px] p-6 md:p-10 relative overflow-hidden group">

            <div class="absolute top-0 right-0 w-24 md:w-32 h-24 md:h-32 bg-[#d15330]/10 blur-[80px] rounded-full pointer-events-none group-hover:bg-[#d15330]/20 transition-all duration-700"></div>

            <div class="flex items-center gap-3 mb-6 md:mb-8">
                <div class="h-[1px] w-6 bg-[#d15330]"></div>
                <span class="text-[10px] uppercase tracking-[0.3em] md:tracking-[0.4em] text-[#d15330] font-bold">
                    Qué muestra
                </span>
            </div>

            <h4 class="text-xl md:text-2xl font-black uppercase tracking-tighter mb-4 md:mb-6">
                Dashboard Principal
            </h4>

            <p class="text-white/50 text-sm leading-relaxed mb-6 md:mb-8">
                La demo concentra KPIs, partidas y navegación de obra en un sistema claro y vendible.
            </p>

            <ul class="space-y-3 md:space-y-4 mb-8 md:mb-10">

                <li class="flex items-start gap-3 text-xs text-white/70">
                    <span class="text-[#d15330]">•</span> UI alineada con RUBRA
                </li>

                <li class="flex items-start gap-3 text-xs text-white/70">
                    <span class="text-[#d15330]">•</span> Dashboard listo para SaaS
                </li>

                <li class="flex items-start gap-3 text-xs text-white/70">
                    <span class="text-[#d15330]">•</span> Sustituible por datos reales
                </li>

                <li class="flex items-start gap-3 text-xs text-white/70">
                    <span class="text-[#d15330]">•</span> Responsive real
                </li>

            </ul>

            <div class="flex flex-col sm:flex-row gap-3 md:gap-4">

                <a href="#precios"
                   class="bg-[#d15330] text-white px-6 md:px-8 py-3 md:py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:brightness-110 transition-all text-center">
                    Ver Planes
                </a>

                <button class="border border-white/10 text-white px-6 md:px-8 py-3 md:py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-white/5 transition-all">
                    Pedir Demo
                </button>

            </div>

        </div>

    </div>

</section>

<section id="prueba-social" class="max-w-[1200px] mx-auto px-10 py-24">
    <div class="mb-16">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-[1px] w-6 bg-[#d15330]"></div>
            <span class="text-[10px] uppercase tracking-[0.4em] text-[#d15330] font-bold"></span>
        </div>
        <h2 class="text-4xl md:text-6xl font-black uppercase leading-[0.9] tracking-tighter">
            Una propuesta pensada para profesionales que valoran <span class="text-[#d15330]">orden y precisión</span>
        </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="testimonio-card bg-[#111111]/40 backdrop-blur-md border border-white/5 rounded-[40px] p-10 relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-[#d15330]/5 blur-[60px] rounded-full group-hover:bg-[#d15330]/10 transition-all duration-700"></div>
            
            <div class="mb-8">
                <svg width="30" height="24" viewBox="0 0 30 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-20 group-hover:opacity-40 transition-opacity">
                    <path d="M0 24V11.232C0 7.712 0.832 4.96 2.496 2.976C4.192 0.992 6.688 0 9.984 0V5.184C8.448 5.184 7.328 5.6 6.624 6.432C5.92 7.232 5.568 8.416 5.568 9.984H9.984V24H0ZM19.2 24V11.232C19.2 7.712 20.032 4.96 21.696 2.976C23.392 0.992 25.888 0 29.184 0V5.184C27.648 5.184 26.528 5.6 25.824 6.432C25.12 7.232 24.768 8.416 24.768 9.984H29.184V24H19.2Z" fill="#d15330"/>
                </svg>
            </div>
            
            <p class="text-white/60 text-sm leading-relaxed italic mb-10">
                "Pasamos de tener varias planillas desordenadas a una estructura mucho más clara para cotizar y controlar compras."
            </p>
            
            <div class="pt-6 border-t border-white/5">
                <h5 class="text-[10px] font-black uppercase tracking-[0.2em] text-white/80">Estudio de Arquitectura</h5>
                <p class="text-[9px] uppercase tracking-widest text-white/30">Montevideo</p>
            </div>
        </div>

        <div class="testimonio-card bg-[#111111]/40 backdrop-blur-md border border-white/5 rounded-[40px] p-10 relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-[#d15330]/5 blur-[60px] rounded-full group-hover:bg-[#d15330]/10 transition-all duration-700"></div>
            
            <div class="mb-8">
                <svg width="30" height="24" viewBox="0 0 30 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-20 group-hover:opacity-40 transition-opacity">
                    <path d="M0 24V11.232C0 7.712 0.832 4.96 2.496 2.976C4.192 0.992 6.688 0 9.984 0V5.184C8.448 5.184 7.328 5.6 6.624 6.432C5.92 7.232 5.568 8.416 5.568 9.984H9.984V24H0ZM19.2 24V11.232C19.2 7.712 20.032 4.96 21.696 2.976C23.392 0.992 25.888 0 29.184 0V5.184C27.648 5.184 26.528 5.6 25.824 6.432C25.12 7.232 24.768 8.416 24.768 9.984H29.184V24H19.2Z" fill="#d15330"/>
                </svg>
            </div>
            
            <p class="text-white/60 text-sm leading-relaxed italic mb-10">
                "Lo más potente es la relación entre rubrado, composiciones y seguimiento de obra. No se siente como una planilla bonita, sino como un sistema."
            </p>
            
            <div class="pt-6 border-t border-white/5">
                <h5 class="text-[10px] font-black uppercase tracking-[0.2em] text-white/80">Constructora</h5>
                <p class="text-[9px] uppercase tracking-widest text-white/30">Montevideo</p>
            </div>
        </div>

        <div class="testimonio-card bg-[#111111]/40 backdrop-blur-md border border-white/5 rounded-[40px] p-10 relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-[#d15330]/5 blur-[60px] rounded-full group-hover:bg-[#d15330]/10 transition-all duration-700"></div>
            
            <div class="mb-8">
                <svg width="30" height="24" viewBox="0 0 30 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-20 group-hover:opacity-40 transition-opacity">
                    <path d="M0 24V11.232C0 7.712 0.832 4.96 2.496 2.976C4.192 0.992 6.688 0 9.984 0V5.184C8.448 5.184 7.328 5.6 6.624 6.432C5.92 7.232 5.568 8.416 5.568 9.984H9.984V24H0ZM19.2 24V11.232C19.2 7.712 20.032 4.96 21.696 2.976C23.392 0.992 25.888 0 29.184 0V5.184C27.648 5.184 26.528 5.6 25.824 6.432C25.12 7.232 24.768 8.416 24.768 9.984H29.184V24H19.2Z" fill="#d15330"/>
                </svg>
            </div>
            
            <p class="text-white/60 text-sm leading-relaxed italic mb-10">
                "La exportación y la lectura de estadísticas hacen mucho más simple justificar precios frente al cliente y tomar decisiones internas."
            </p>
            
            <div class="pt-6 border-t border-white/5">
                <h5 class="text-[10px] font-black uppercase tracking-[0.2em] text-white/80">Dirección Técnica</h5>
                <p class="text-[9px] uppercase tracking-widest text-white/30">Uruguay</p>
            </div>
        </div>
    </div>
</section>

<section id="precios" class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-10 py-16 md:py-24">

    <!-- HEADER -->
    <div class="mb-10 md:mb-16 text-left">

        <div class="flex items-center gap-3 mb-4 md:mb-6">
            <div class="h-[1px] w-6 bg-[#d15330]"></div>
            <span class="text-[10px] uppercase tracking-[0.3em] md:tracking-[0.4em] text-[#d15330] font-bold">
                Precios
            </span>
        </div>

        <h2 class="text-3xl sm:text-4xl md:text-6xl font-black uppercase leading-[0.95] tracking-tighter mb-4 md:mb-6">
            Planes para validar, crecer y
            <span class="text-[#d15330]">escalar</span>
        </h2>

        <p class="text-gray-400 max-w-2xl text-sm md:text-base leading-relaxed">
            Empezá gratis y crecé según tus necesidades. Todos los planes incluyen acceso completo a la plataforma.
        </p>

    </div>

    <!-- GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 items-stretch">

        <!-- PLAN GRATIS -->
        <div class="price-card bg-[#111111]/40 backdrop-blur-md border border-white/5 rounded-[24px] p-6 md:p-8 flex flex-col justify-between">

            <div>
                <span class="text-[9px] uppercase tracking-[0.3em] text-white/30 font-bold">
                    Modo Prueba
                </span>

                <div class="mt-4 mb-6">
                    <h3 class="text-2xl font-black uppercase">Gratis</h3>
                    <div class="flex items-baseline gap-1 mt-2">
                        <span class="text-4xl font-black italic">$0</span>
                    </div>
                    <p class="text-[10px] text-[#d15330] font-bold uppercase tracking-wider mt-1">1 mes gratis</p>
                </div>

                <p class="text-xs text-white/50 mb-6">
                    Acceso a la plataforma sin costo durante 30 días.
                </p>

                <ul class="space-y-3 text-[10px] text-white/70 uppercase tracking-wider font-medium">
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Acceso a la plataforma
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Hasta 1 colaborador
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Recursos, presupuestos, ejecución
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Gantt, estadísticas, bitácora y diario
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Exportación de presupuestos con marca de agua
                    </li>
                    <li class="flex items-center gap-2 font-black text-white">
                        <span class="text-[#d15330]">→</span> Hasta 3 proyectos
                    </li>
                </ul>
            </div>

            <a href="{{ auth()->check() ? route('dashboard') : route('register') }}"
               class="mt-8 block w-full py-3 border border-white/10 text-center text-[10px] font-black uppercase tracking-widest hover:text-[#d15330] hover:border-[#d15330]/40 transition-all">
                Empezar gratis
            </a>

        </div>

        <!-- PLAN BÁSICO -->
        <div class="price-card bg-[#111111]/40 backdrop-blur-md border border-white/5 rounded-[24px] p-6 md:p-8 flex flex-col justify-between">

            <div>
                <span class="text-[9px] uppercase tracking-[0.3em] text-white/30 font-bold">
                    Plan Básico
                </span>

                <div class="mt-4 mb-6">
                    <h3 class="text-2xl font-black uppercase">Básico</h3>
                    <div class="flex items-baseline gap-1 mt-2">
                        <span class="text-xs font-bold text-white/40">US$</span>
                        <span class="text-2xl font-black italic">9</span>
                        <span class="text-xs text-white/30">anual</span>
                        <span class="text-xs font-bold text-white/40 mx-2">|</span>
                        <span class="text-xs font-bold text-white/40">US$</span>
                        <span class="text-2xl font-black italic">12</span>
                        <span class="text-xs text-white/30">mes</span>
                    </div>
                </div>

                <p class="text-xs text-white/50 mb-6">
                    Para profesionales que gestionan proyectos de forma independiente.
                </p>

                <ul class="space-y-3 text-[10px] text-white/70 uppercase tracking-wider font-medium">
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Acceso completo a la plataforma
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Hasta 3 Colaboradores
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Recursos, Presupuestos, Ejecución
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Gantt, Estadísticas, Bitácora y Diario
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Soporte Base
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Exportación de presupuestos PDF y Excel sin marca de agua
                    </li>
                    <li class="flex items-center gap-2 font-black text-white">
                        <span class="text-[#d15330]">→</span> Hasta 10 proyectos
                    </li>
                </ul>
            </div>

            <a href="{{ auth()->check() ? route('pago.checkout', 'basico') : route('register') }}"
               class="mt-8 block w-full py-3 border border-white/10 text-center text-[10px] font-black uppercase tracking-widest hover:text-[#d15330] hover:border-[#d15330]/40 transition-all">
                Empezar
            </a>

        </div>

        <!-- PLAN PROFESIONAL (DESTACADO) -->
        <div class="price-card bg-[#d15330] rounded-[24px] p-6 md:p-8 flex flex-col justify-between relative shadow-[0_0_40px_rgba(209,83,48,0.25)] sm:-translate-y-2">

            <div class="absolute top-4 right-4 bg-white text-[#d15330] text-[8px] font-black uppercase px-3 py-1 rounded-full tracking-widest">
                Más elegido
            </div>

            <div>
                <span class="text-[9px] uppercase tracking-[0.3em] text-white/60 font-bold">
                    Plan Profesional
                </span>

                <div class="mt-4 mb-6 text-white">
                    <h3 class="text-2xl font-black uppercase">Pro</h3>
                    <div class="flex items-baseline gap-1 mt-2">
                        <span class="text-xs font-bold text-white/60">US$</span>
                        <span class="text-2xl font-black italic">24</span>
                        <span class="text-xs text-white/60">anual</span>
                        <span class="text-xs font-bold text-white/60 mx-2">|</span>
                        <span class="text-xs font-bold text-white/60">US$</span>
                        <span class="text-2xl font-black italic">29</span>
                        <span class="text-xs text-white/60">mes</span>
                    </div>
                </div>

                <p class="text-xs text-white/80 mb-6">
                    Control Total para Estudios y Constructoras.
                </p>

                <ul class="space-y-3 text-[10px] text-white uppercase tracking-wider font-bold">
                    <li class="flex items-center gap-2 border-b border-white/20 pb-2">
                        <span class="text-white">✓</span> Acceso completo a la plataforma
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/20 pb-2">
                        <span class="text-white">✓</span> Hasta 20 Colaboradores
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/20 pb-2">
                        <span class="text-white">✓</span> Recursos, Presupuestos, Ejecución
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/20 pb-2">
                        <span class="text-white">✓</span> Gantt, Estadísticas, Bitácora y Diario
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-white">✓</span> Soporte Estandar
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/20 pb-2">
                        <span class="text-white">✓</span> Exportación de presupuestos PDF y Excel sin marca de agua
                    </li>
                    <li class="flex items-center gap-2 font-black">
                        <span class="text-white">→</span> Hasta 25 proyectos
                    </li>
                </ul>
            </div>

            <a href="{{ auth()->check() ? route('pago.checkout', 'profesional') : route('register') }}"
               class="mt-8 block w-full py-3 bg-white text-[#d15330] text-center text-[10px] font-black uppercase tracking-widest hover:bg-white/90 transition-all rounded-lg">
                Empezar
            </a>

        </div>

        <!-- PLAN ENTERPRISE -->
        <div class="price-card bg-[#111111]/40 backdrop-blur-md border border-white/5 rounded-[24px] p-6 md:p-8 flex flex-col justify-between">

            <div>
                <span class="text-[9px] uppercase tracking-[0.3em] text-white/30 font-bold">
                    Enterprise
                </span>

                <div class="mt-4 mb-6">
                    <h3 class="text-2xl font-black uppercase">Enterprise</h3>
                    <div class="flex items-baseline gap-1 mt-2">
                        <span class="text-xs font-bold text-white/40">US$</span>
                        <span class="text-2xl font-black italic">59</span>
                        <span class="text-xs text-white/30">anual</span>
                        <span class="text-xs font-bold text-white/40 mx-2">|</span>
                        <span class="text-xs font-bold text-white/40">US$</span>
                        <span class="text-2xl font-black italic">65</span>
                        <span class="text-xs text-white/30">mes</span>
                    </div>
                </div>

                <p class="text-xs text-white/50 mb-6">
                    Para operaciones grandes con múltiples equipos y obras.
                </p>

                <ul class="space-y-3 text-[10px] text-white/70 uppercase tracking-wider font-medium">
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Acceso completo a la plataforma
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Hasta 50 Colaboradores
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Recursos, Presupuestos, Ejecución
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Gantt, estadísticas, bitácora y diario
                    </li>
                    <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Exportación de presupuestos PDF y Excel sin marca de agua
                    </li>
                     <li class="flex items-center gap-2 border-b border-white/5 pb-2">
                        <span class="text-[#d15330]">✓</span> Soporte Prioritario
                    </li>
                    <li class="flex items-center gap-2 font-black text-white">
                        <span class="text-[#d15330]">→</span> Hasta 100 proyectos
                    </li>
                </ul>
            </div>

            <a href="{{ auth()->check() ? route('pago.checkout', 'enterprise') : route('register') }}"
               class="mt-8 block w-full py-3 border border-white/10 text-center text-[10px] font-black uppercase tracking-widest hover:text-[#d15330] hover:border-[#d15330]/40 transition-all">
               Empezar
            </a>

        </div>

    </div>

</section>

<section id="cta-final" class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-10 py-20 md:py-32">

    <div class="bg-gradient-to-br from-[#1a1a1a] to-[#0a0a0a] rounded-[28px] md:rounded-[48px] p-6 sm:p-10 md:p-20 border border-white/5 relative overflow-hidden group">

        <!-- glow -->
        <div class="absolute -bottom-20 -left-20 w-60 md:w-80 h-60 md:h-80 bg-[#d15330]/10 blur-[100px] md:blur-[120px] rounded-full"></div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 md:gap-12 items-center relative z-10">

            <!-- LEFT -->
            <div class="relative z-10">

                <div class="flex items-center gap-3 mb-6 md:mb-8">
                    <div class="h-[1px] w-6 bg-[#d15330]"></div>
                   
                </div>

                <h2 class="text-3xl sm:text-5xl md:text-7xl font-black uppercase leading-[0.9] md:leading-[0.85] tracking-tighter mb-6 md:mb-8">
                    Convertí tu presupuesto en una
                    <span class="text-[#d15330]">herramienta de gestión</span>
                </h2>

                <p class="text-gray-400 text-sm md:text-base max-w-md leading-relaxed">
                    RUBRA combina velocidad, orden y criterio técnico en una experiencia pensada para vender mejor, ejecutar con más control y decidir con más información.
                </p>

            </div>

<div class="flex flex-col gap-3 md:gap-4 lg:items-end relative z-10">

    <a href="{{ auth()->check() ? route('dashboard') : route('register') }}"
       class="w-full lg:w-72 bg-[#d15330] hover:bg-[#e25a36] text-white text-center py-4 md:py-5 rounded-xl text-[10px] md:text-xs font-black uppercase tracking-[0.2em] transition-all shadow-lg shadow-[#d15330]/20">
        Empezar gratis
    </a>

    <a href="#precios"
       class="w-full lg:w-72 border-b border-white/10 py-4 md:py-5 text-center lg:text-right text-[10px] font-black uppercase tracking-[0.2em] text-white/60 hover:text-white transition-colors">
        Comparar Planes
    </a>

    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
            class="w-full lg:w-72 border-b border-white/10 py-4 md:py-5 text-center lg:text-right text-[10px] font-black uppercase tracking-[0.2em] text-white/30 hover:text-white transition-colors">
        Volver arriba
    </button>

</div>
        </div>
    </div>

</section>
    
    
     <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/SVGLoader.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            gsap.registerPlugin(ScrollTrigger);

            // ─── Hero entrance ───────────────────────────────────────────
            const tl = gsap.timeline();
            tl.from("#hero-left-content h1", { duration: 1.5, y: 80, opacity: 0, ease: "power4.out", skewY: 3 })
              .from("#terminal-viva", { duration: 1.2, x: 50, opacity: 0, ease: "expo.out" }, "-=1")
              .from("#hero-left-content p, #hero-left-content div", { duration: 0.8, opacity: 0, y: 20, stagger: 0.15, ease: "power3.out" }, "-=0.8");

            // ─── Vision card ─────────────────────────────────────────────
            gsap.from("#vision-card", {
                scrollTrigger: { trigger: "#vision", start: "top 75%" },
                y: 60, opacity: 0, duration: 1.2, ease: "power3.out"
            });

            gsap.to(".word", {
                scrollTrigger: {
                    trigger: "#vision-title",
                    start: "top 80%",
                    end: "top 40%",
                    toggleActions: "play none none none"
                },
                opacity: 1, y: 0, filter: "blur(0px)",
                stagger: 0.12, duration: 0.8, ease: "power3.out"
            });

 const visionWords = gsap.utils.toArray(".vision-word");

gsap.fromTo(visionWords,
  {
    color: "rgba(255,255,255,0.2)",
    y: 20,
  },
  {
    color: "#ffffff",
    y: 0,
    ease: "none",
    stagger: 0.12,
    scrollTrigger: {
      trigger: "#vision-words",
      start: "top 85%",
      end: "top 40%",
      scrub: true,
    }
  }
);

            

            // ─── Parallax mouse ──────────────────────────────────────────
            document.addEventListener("mousemove", (e) => {
                const xPos = (e.clientX / window.innerWidth - 0.5);
                const yPos = (e.clientY / window.innerHeight - 0.5);
                gsap.to("#terminal-viva", { x: xPos * 25, y: yPos * 25, duration: 1.5, ease: "power2.out" });
                gsap.to(".parallax-letter", {
                    x: (i) => xPos * (20 + i * 10),
                    y: (i) => yPos * (20 + i * 10),
                    duration: 2, ease: "power2.out"
                });
            });

            // ─── SCRAMBLE / MORPH ────────────────────────────────────────
            const phrases = [
                "PRESUPUESTAR\nEN SEGUNDOS",
                "CONTROLAR\nCOSTOS REALES",
                "GESTIONAR\nTU OBRA HOY",
                "ESCALAR\nSIN PLANILLAS",
                "DECIDIR\nCON DATOS",
            ];

            const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ$#@%&*[]{}";
            const el = document.getElementById("scramble-text");
            let currentIndex = 0;
            let isAnimating = false;

            function scrambleTo(newText) {
                if (isAnimating) return;
                isAnimating = true;

                const lines = newText.split("\n");
                let iteration = 0;
                const totalFrames = 18;

                const interval = setInterval(() => {
                    el.innerHTML = lines.map(line => {
                        return line.split("").map((char, i) => {
                            if (char === " ") return " ";
                            if (iteration > totalFrames * (i / line.length)) {
                                return `<span>${char}</span>`;
                            }
                            return `<span class="text-[#d15330]/60">${chars[Math.floor(Math.random() * chars.length)]}</span>`;
                        }).join("");
                    }).join('<br>');

                    iteration++;

                    if (iteration > totalFrames + 4) {
                        clearInterval(interval);
                        el.innerHTML = lines.map(line =>
                            line.split("").map(char =>
                                char === " " ? " " : `<span>${char}</span>`
                            ).join("")
                        ).join('<br>');
                        isAnimating = false;
                    }
                }, 40);
            }

            // Lanzar al entrar en viewport
            ScrollTrigger.create({
                trigger: "#narrativa",
                start: "top 70%",
                onEnter: () => scrambleTo(phrases[0]),
            });

            // Ciclar automáticamente cada 2.8s
            setInterval(() => {
                currentIndex = (currentIndex + 1) % phrases.length;
                scrambleTo(phrases[currentIndex]);
            }, 2800);
        });

        // ─── TERMINAL SCROLL TYPING ───────────────────────────────────────
const textToType = "inicializar rubrado --proyecto='Laguna de los Cisnes'\n> cargando base de datos de materiales...\n> sincronizando precios en tiempo real...\n> vinculando ruta crítica de Gantt...\n> cálculo finalizado con éxito.\n> presupuesto generado: USD 128.460\n> listo para exportar a PDF / Excel.";

const typedTextElement = document.getElementById("typed-text");

ScrollTrigger.create({
    trigger: "#scroll-terminal-section",
    start: "top 60%", 
    end: "bottom 20%",
    scrub: true,
    onUpdate: (self) => {
        // Calculamos cuántos caracteres mostrar según el progreso del scroll (0 a 1)
        const totalChars = textToType.length;
        const currentProgress = Math.floor(self.progress * totalChars);
        
        // El .replace(/\n/g, '<br>') es para que respete los saltos de línea del string
        typedTextElement.innerHTML = textToType.substring(0, currentProgress).replace(/\n/g, '<br>');
    }
});
// 1. Setup del Contenedor y Renderizador
const container = document.getElementById('canvas-container');
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(36, container.clientWidth / container.clientHeight, 0.1, 100);

const renderer = new THREE.WebGLRenderer({ 
    antialias: true, 
    alpha: true,
    powerPreference: "high-performance"
});

renderer.setSize(container.clientWidth, container.clientHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.outputColorSpace = THREE.SRGBColorSpace;
renderer.toneMapping = THREE.ReinhardToneMapping;
renderer.toneMappingExposure = 1.0;
container.appendChild(renderer.domElement);

// 2. SVG del logo RUBRA
const svgMarkup = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 885.75 885.75">
  <path d="M 29.992188 77.976562 L 470.867188 77.976562 C 593.832031 77.976562 695.804688 178.449219 695.804688 305.914062 C 695.804688 392.890625 643.320312 446.125 528.601562 508.355469 L 874.253906 797.773438 C 881.75 803.773438 883.25 809.023438 878.753906 813.519531 C 876.753906 815.019531 863.507812 815.769531 839.011719 815.769531 L 377.144531 428.878906 C 372.644531 425.378906 372.644531 421.382812 377.144531 416.882812 L 541.347656 307.414062 C 541.347656 251.929688 503.109375 209.941406 447.625 209.941406 L 29.992188 209.941406 C 21.992188 209.941406 17.996094 205.941406 17.996094 197.945312 L 17.996094 89.972656 C 17.996094 81.976562 21.992188 77.976562 29.992188 77.976562 Z" />
  <path d="M 24.742188 247.429688 L 196.445312 247.429688 C 205.441406 247.429688 209.941406 251.929688 209.941406 260.925781 L 209.941406 498.609375 L 24.742188 617.824219 C 16.246094 617.824219 11.996094 613.578125 11.996094 605.078125 L 11.996094 260.175781 C 11.996094 251.679688 16.246094 247.429688 24.742188 247.429688 Z" />
  <path d="M 13.496094 659.8125 L 13.496094 802.273438 C 13.496094 811.269531 17.996094 815.769531 26.992188 815.769531 L 196.445312 815.769531 C 205.441406 815.769531 209.941406 811.269531 209.941406 802.273438 L 209.941406 554.09375 C 209.941406 545.597656 205.691406 541.347656 197.195312 541.347656 Z" />
  <path d="M 237.683594 503.109375 L 334.40625 442.375 C 337.40625 440.375 340.402344 441.125 343.402344 444.625 L 769.28125 803.773438 C 775.28125 808.773438 775.28125 812.769531 769.28125 815.769531 L 580.335938 815.769531 L 236.183594 521.851562 C 232.683594 518.355469 230.933594 514.355469 230.933594 509.855469 C 230.933594 506.855469 233.183594 504.609375 237.683594 503.109375 Z" />
</svg>`;

// 3. Materiales
const mainOrange = 0xD15330;

const frontMaterial = new THREE.MeshStandardMaterial({
    color: mainOrange,
    metalness: 0.05,
    roughness: 0.55,
    side: THREE.DoubleSide
});

const sideMaterial = new THREE.MeshStandardMaterial({
    color: 0xA83D1E,
    metalness: 0.05,
    roughness: 0.65,
    side: THREE.DoubleSide
});

const edgeMaterial = new THREE.LineBasicMaterial({
    color: 0xffd2c5,
    transparent: true,
    opacity: 0.22
});

// 4. Extrusión
const extrudeSettings = {
    depth: 90,
    bevelEnabled: true,
    bevelSegments: 8,
    steps: 1,
    bevelSize: 10,
    bevelThickness: 10,
    curveSegments: 32
};

const group = new THREE.Group();
scene.add(group);

const svgData = new THREE.SVGLoader().parse(svgMarkup);
const logoGroup = new THREE.Group();
group.add(logoGroup);

svgData.paths.forEach((path) => {
    const shapes = THREE.SVGLoader.createShapes(path);
    shapes.forEach((shape) => {
        const geometry = new THREE.ExtrudeGeometry(shape, extrudeSettings);
        const mesh = new THREE.Mesh(geometry, [frontMaterial, sideMaterial]);
        logoGroup.add(mesh);

        const edges = new THREE.LineSegments(
            new THREE.EdgesGeometry(geometry, 35),
            edgeMaterial
        );
        logoGroup.add(edges);
    });
});

// Escala, centrado y rotación inicial
logoGroup.scale.set(0.0075, -0.0075, 0.0075);
const box = new THREE.Box3().setFromObject(logoGroup);
const center = box.getCenter(new THREE.Vector3());
logoGroup.position.sub(center);
logoGroup.rotation.x = -0.32;
logoGroup.rotation.y = 0.58;
logoGroup.rotation.z = -0.03;

// 5. Luces
scene.add(new THREE.AmbientLight(0xffffff, 0.5));

// Key light — blanca fuerte desde arriba-frente
const keyLight = new THREE.DirectionalLight(0xffffff, 3.5);
keyLight.position.set(3, 8, 10);
scene.add(keyLight);

// Top light — highlight del hombro superior
const topLight = new THREE.DirectionalLight(0xffffff, 1.5);
topLight.position.set(0, 12, 4);
scene.add(topLight);

// Fill — naranja desde la izquierda
const fillLight = new THREE.DirectionalLight(0xff5522, 1.2);
fillLight.position.set(-8, 0, 5);
scene.add(fillLight);

// Rim — separa la R del fondo oscuro
const rimLight = new THREE.PointLight(0xcc3300, 25, 20);
rimLight.position.set(0, -4, -6);
scene.add(rimLight);

// 6. Cámara
camera.position.set(0, 0, 7.5);

// 7. GSAP Animación
let scrollProgress = 0;
gsap.to({}, {
    scrollTrigger: {
        trigger: "#logo-3d-section",
        start: "top bottom",
        end: "bottom top",
        scrub: 2.5,
        onUpdate: (self) => {
            scrollProgress = self.progress;
        }
    }
});

// 8. Bucle de Renderizado
function animate() {
    requestAnimationFrame(animate);
    const t = performance.now();
    group.rotation.y += 0.002 + (scrollProgress * 0.008);
    group.rotation.x = Math.sin(t * 0.0005) * 0.03 + (scrollProgress * 0.1);
    group.position.y = Math.sin(t * 0.001) * 0.1;
    renderer.render(scene, camera);
}
animate();

// 9. Resize Handler
window.addEventListener('resize', () => {
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
});
    </script>

     
<script>
(function() {
    const section  = document.getElementById('proceso');
    const curve    = document.getElementById('main-curve');
    const dots     = document.querySelectorAll('.curve-dot');
    const rings    = document.querySelectorAll('.curve-dot-ring');
    const labels   = document.querySelectorAll('.curve-label');
    const steps    = document.querySelectorAll('.proceso-step');
    let triggered  = false;
 
    function triggerAnims() {
        if (triggered) return;
        triggered = true;
 
        // 1. Dibuja la curva
        curve.classList.add('drawn');
 
        // 2. Aparecen los puntos escalonados
        dots.forEach((dot, i) => {
            setTimeout(() => {
                dot.classList.add('visible');
                rings[i]?.classList.add('visible');
                labels[i]?.classList.add('visible');
            }, 400 + i * 280);
        });
 
        // 3. Steps entran escalonados
        steps.forEach((step, i) => {
            setTimeout(() => step.classList.add('visible'), 200 + i * 120);
        });
    }
 
    // Observer
    const observer = new IntersectionObserver(
        (entries) => { if (entries[0].isIntersecting) triggerAnims(); },
        { threshold: 0.2 }
    );
    if (section) observer.observe(section);
})();
</script>

<script>
    gsap.registerPlugin(ScrollTrigger);

// Animación de las tarjetas al entrar
gsap.from(".impacto-card", {
    scrollTrigger: {
        trigger: "#impacto-section",
        start: "top 80%",
    },
    y: 50,
    opacity: 0,
    duration: 1,
    stagger: 0.2,
    ease: "power4.out"
});

// Animación de los números (Counters)
document.querySelectorAll('.counter').forEach(counter => {
    const target = +counter.getAttribute('data-target');
    
    gsap.to(counter, {
        scrollTrigger: {
            trigger: "#impacto-section",
            start: "top 80%",
        },
        innerText: target,
        duration: 2,
        snap: { innerText: 1 },
        ease: "power1.inOut"
    });
});

// Animación de los círculos de progreso
document.querySelectorAll('.progress-circle').forEach(circle => {
    const percent = circle.getAttribute('data-percent');
    const radius = circle.r.baseVal.value;
    const circumference = 2 * Math.PI * radius; // Aprox 364.4
    
    const offset = circumference - (percent / 100) * circumference;

    gsap.to(circle, {
        scrollTrigger: {
            trigger: "#impacto-section",
            start: "top 80%",
        },
        strokeDashoffset: offset,
        duration: 2,
        ease: "power2.out"
    });
});

// Animación de entrada para la sección Dashboard
gsap.from(".mockup-container", {
    scrollTrigger: {
        trigger: "#dashboard-preview",
        start: "top 70%",
    },
    x: -50,
    opacity: 0,
    duration: 1.2,
    ease: "power4.out"
});

gsap.from("#dashboard-preview .dashboard-right", {
    scrollTrigger: {
        trigger: "#dashboard-preview",
        start: "top 70%",
    },
    x: 50,
    opacity: 0,
    duration: 1.2,
    delay: 0.2,
    ease: "power4.out"
});

// Efecto de flotación suave para la maqueta
gsap.to(".mockup-container", {
    y: 10,
    duration: 3,
    repeat: -1,
    yoyo: true,
    ease: "sine.inOut"
});

// Animación escalonada para los testimonios
gsap.from(".testimonio-card", {
    scrollTrigger: {
        trigger: "#prueba-social",
        start: "top 80%",
    },
    y: 40,
    opacity: 0,
    duration: 1,
    stagger: 0.15, // Delay entre cada tarjeta
    ease: "power2.out"
});

// Verificar si #precios ya está visible (navegación con hash)
const _preciosEl = document.getElementById('precios');
if (_preciosEl && _preciosEl.getBoundingClientRect().top < window.innerHeight * 0.9) {
    gsap.set('.price-card', { opacity: 1, y: 0 });
} else {
    gsap.from('.price-card', {
        scrollTrigger: {
            trigger: '#precios',
            start: 'top 90%',
            once: true,
        },
        y: 30,
        opacity: 0,
        duration: 0.7,
        stagger: 0.1,
        ease: 'power3.out'
    });
}

gsap.from("#cta-final .group", {
    scrollTrigger: {
        trigger: "#cta-final",
        start: "top 85%",
    },
    scale: 0.95,
    opacity: 0,
    duration: 1.5,
    ease: "power4.out"
});

// Animación de los botones con un ligero retraso
gsap.from("#cta-final a, #cta-final button", {
    scrollTrigger: {
        trigger: "#cta-final",
        start: "top 80%",
    },
    x: 30,
    opacity: 0,
    duration: 1,
    stagger: 0.2,
    ease: "power2.out"
});
</script>

    <style>
        @keyframes marquee {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .animate-marquee {
            display: flex;
            width: max-content;
            animation: marquee 40s linear infinite;
            will-change: transform;
            transform: translateZ(0);
            backface-visibility: hidden;
        }

        .animate-marquee:hover { animation-play-state: paused; }

        .word {
            display: inline-block;
            opacity: 0;
            transform: translateY(40px);
            filter: blur(8px);
        }

        #scramble-text span {
            display: inline-block;
            transition: color 0.1s;
        }
    </style>
     
<style>
    /* Animación de dibujado de la curva */
    .curve-path {
        stroke-dasharray: 700;
        stroke-dashoffset: 700;
        transition: stroke-dashoffset 1.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .curve-path.drawn {
        stroke-dashoffset: 0;
    }
 
    /* Puntos que aparecen con fade */
    .curve-dot, .curve-dot-ring, .curve-label {
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    .curve-dot.visible, .curve-dot-ring.visible, .curve-label.visible {
        opacity: 1;
    }
 
    /* Pulso en los dots */
    @keyframes dotPulse {
        0%, 100% { r: 14; opacity: 0.15; }
        50%       { r: 18; opacity: 0.08; }
    }
    .curve-dot-ring.visible {
        animation: dotPulse 2s ease-in-out infinite;
    }
 
    /* Steps entrada */
    .proceso-step {
        opacity: 0;
        transform: translateY(16px);
        transition: opacity 0.5s ease, transform 0.5s ease,
                    border-color 0.3s, background-color 0.3s;
    }
    .proceso-step.visible {
        opacity: 1;
        transform: translateY(0);
    }

</style>

<!-- FOOTER -->
<footer class="bg-[#0a0a0a] border-t border-white/5 mt-20 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-12">
            <!-- Brand -->
            <div>
                <h3 class="text-white font-black text-lg tracking-tight mb-4">
                    <span class="text-[#d15330]">R</span>ubra
                </h3>
                <p class="text-white/40 text-sm leading-relaxed">
                    Herramienta de apoyo a la presupuestación y gestión de obra. La validación final de la información y decisiones corresponde al usuario.
                </p>
            </div>

            <!-- Producto -->
            <div>
                <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Producto</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#vision" class="text-white/50 hover:text-[#d15330] transition">Visión</a></li>
                    <li><a href="#producto" class="text-white/50 hover:text-[#d15330] transition">Características</a></li>
                    <li><a href="#dashboard-preview" class="text-white/50 hover:text-[#d15330] transition">Dashboard</a></li>
                    <li><a href="#precios" class="text-white/50 hover:text-[#d15330] transition">Planes</a></li>
                </ul>
            </div>

            <!-- Empresa + Legal en columna -->
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Empresa</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-white/50 hover:text-[#d15330] transition">Sobre nosotros</a></li>
                        <li><a href="#" class="text-white/50 hover:text-[#d15330] transition">Blog</a></li>
                        <li><a href="#" class="text-white/50 hover:text-[#d15330] transition">Documentación</a></li>
                        <li><a href="#" class="text-white/50 hover:text-[#d15330] transition">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('legal.terminos') }}" class="text-white/50 hover:text-[#d15330] transition">Términos</a></li>
                        <li><a href="{{ route('legal.privacidad') }}" class="text-white/50 hover:text-[#d15330] transition">Privacidad</a></li>
                        <li><a href="{{ route('legal.cookies') }}" class="text-white/50 hover:text-[#d15330] transition">Cookies</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Copyright + Texto legal -->
        <div class="border-t border-white/5 pt-8 space-y-3">
            <p class="text-white/40 text-sm">&copy; {{ date('Y') }} Rubra. Todos los derechos reservados.</p>
            <p class="text-white/25 text-xs leading-relaxed">
                El uso de Rubra está sujeto a nuestros Términos y Condiciones, Política de Privacidad, Política de Cookies y Política de Soporte. Los datos cargados por cada usuario siguen siendo de su titularidad. Rubra es una herramienta de apoyo a la presupuestación y gestión de obra, por lo que la validación final de la información y de las decisiones tomadas con base en la plataforma corresponde al usuario. En caso de soporte solicitado, ApexObra podrá acceder a la cuenta únicamente con fines de asistencia técnica.
            </p>
        </div>
    </div>
</footer>
<button onclick="window.scrollTo({top:0,behavior:'smooth'})"
    class="fixed bottom-6 left-6 bg-white text-black p-3 rounded-sm shadow-2xl hover:bg-[#d15330] hover:text-white transition-all duration-500 z-50 transform hover:-translate-y-2 uppercase text-[10px] font-black tracking-widest">
    Top
</button>

</x-app-layout>