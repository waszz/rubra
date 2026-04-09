<x-app-layout>
<div class="min-h-screen bg-[#0a0a0a] text-gray-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20">

        {{-- Header --}}
        <div class="mb-14">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('home') }}" class="text-[10px] text-white/40 hover:text-[#d15330] uppercase tracking-widest font-black transition">← Inicio</a>
            </div>
            <div class="flex items-center gap-3 mb-3">
                <div class="h-[2px] w-8 bg-[#d15330]"></div>
                <span class="text-[10px] text-[#d15330] font-black uppercase tracking-widest">Legal</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-black uppercase tracking-tighter text-white mb-3">
                Política de Cookies
            </h1>
            <p class="text-white/30 text-xs uppercase tracking-widest font-bold">Última actualización: {{ date('d/m/Y') }}</p>
        </div>

        {{-- Contenido --}}
        <div class="space-y-10 text-sm leading-relaxed">

            @php
            $secciones = [
                [
                    'num' => '1',
                    'titulo' => 'Qué son las cookies',
                    'cuerpo' => 'Las cookies son pequeños archivos de texto que un sitio o aplicación web puede almacenar en el dispositivo del usuario para recordar información sobre su visita, preferencias, sesión o comportamiento de navegación.',
                ],
                [
                    'num' => '2',
                    'titulo' => 'Para qué utiliza cookies Rubra',
                    'cuerpo' => 'Rubra puede utilizar cookies y tecnologías similares para fines como:

• permitir el inicio y mantenimiento de sesión;
• recordar preferencias del usuario;
• mejorar el rendimiento y la estabilidad del servicio;
• medir tráfico y uso de páginas o funcionalidades;
• reforzar la seguridad y detectar actividades anómalas;
• personalizar ciertos aspectos de la experiencia dentro del producto o del sitio.',
                ],
                [
                    'num' => '3',
                    'titulo' => 'Tipos de cookies que pueden usarse',
                    'cuerpo' => 'Según su finalidad, Rubra puede emplear:

• Cookies estrictamente necesarias: indispensables para el funcionamiento básico del sitio o la plataforma.
• Cookies de rendimiento o analítica: permiten medir uso, visitas, interacciones y comportamiento general.
• Cookies funcionales: recuerdan preferencias, idioma, configuración o experiencia previa.
• Cookies de terceros: provistas por servicios externos integrados, tales como analítica, mapas, soporte o contenido embebido.',
                ],
                [
                    'num' => '4',
                    'titulo' => 'Base para su uso',
                    'cuerpo' => 'Las cookies estrictamente necesarias pueden utilizarse por resultar indispensables para el funcionamiento del servicio. Las demás cookies podrán utilizarse en la medida permitida por la normativa aplicable y, cuando corresponda, sobre la base del consentimiento del usuario.',
                ],
                [
                    'num' => '5',
                    'titulo' => 'Gestión de cookies por el usuario',
                    'cuerpo' => 'El usuario puede configurar su navegador o dispositivo para aceptar, rechazar, limitar o eliminar cookies. Debe tener presente que bloquear determinadas cookies puede afectar la disponibilidad o el funcionamiento correcto de algunas funcionalidades del sitio o de la plataforma.',
                ],
                [
                    'num' => '6',
                    'titulo' => 'Cookies de terceros',
                    'cuerpo' => 'Cuando Rubra utilice herramientas de terceros, dichas herramientas podrán instalar sus propias cookies o mecanismos equivalentes. El tratamiento asociado a esas cookies puede quedar sujeto también a las políticas del tercero correspondiente.',
                ],
                [
                    'num' => '7',
                    'titulo' => 'Cambios a esta política',
                    'cuerpo' => 'ApexObra podrá modificar esta Política de Cookies para reflejar cambios normativos, técnicos o funcionales. La versión vigente será la publicada en el sitio o en la plataforma.',
                ],
            ];
            @endphp

            @foreach($secciones as $s)
            <div class="border-t border-white/5 pt-8">
                <div class="flex items-start gap-4">
                    <span class="shrink-0 text-[#d15330] font-black text-xs uppercase tracking-widest mt-1 w-6">{{ $s['num'] }}.</span>
                    <div class="flex-1">
                        <h2 class="text-white font-black uppercase tracking-wider text-base mb-3">{{ $s['titulo'] }}</h2>
                        @foreach(explode("\n\n", $s['cuerpo']) as $parrafo)
                            <p class="text-white/50 leading-relaxed mb-3">{{ $parrafo }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        {{-- Pie --}}
        <div class="mt-20 pt-10 border-t border-white/5 text-center">
            <p class="text-white/20 text-xs uppercase tracking-widest">© {{ date('Y') }} Rubra — ApexObra. Todos los derechos reservados.</p>
        </div>

    </div>
</div>
</x-app-layout>
