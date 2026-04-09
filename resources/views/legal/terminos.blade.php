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
                Términos y Condiciones
            </h1>
            <p class="text-white/30 text-xs uppercase tracking-widest font-bold">Última actualización: {{ date('d/m/Y') }}</p>
        </div>

        {{-- Contenido --}}
        <div class="space-y-10 text-sm leading-relaxed">

            @php
            $secciones = [
                [
                    'num' => '1',
                    'titulo' => 'Identificación del titular',
                    'cuerpo' => 'Rubra es una plataforma desarrollada, administrada y explotada por ApexObra, empresa dedicada al desarrollo de soluciones digitales para la industria de la construcción.

A los efectos de este documento, las referencias a "Rubra", "la plataforma", "el sistema", "nosotros" o "ApexObra" aluden al titular del servicio, salvo que del contexto surja lo contrario.

Datos a completar antes de publicación: razón social completa de ApexObra — domicilio legal — correo de contacto legal y de soporte — sitio web oficial de comercialización — país y jurisdicción aplicable.',
                ],
                [
                    'num' => '2',
                    'titulo' => 'Objeto del servicio',
                    'cuerpo' => 'Rubra es un software en modalidad SaaS (software as a service) orientado a facilitar la presupuestación, organización, análisis, seguimiento y control de obras, proyectos de construcción, reformas e instalaciones.

La plataforma puede incluir funcionalidades de creación de rubros, subrubros y partidas; gestión de materiales y composiciones; importación y exportación de datos; presupuestos; reportes; paneles; control de costos; documentación; bitácora; carga de comprobantes; imágenes; colaboración entre usuarios; integraciones y demás módulos que ApexObra decida ofrecer dentro de sus planes vigentes.',
                ],
                [
                    'num' => '3',
                    'titulo' => 'Aceptación y capacidad',
                    'cuerpo' => 'Al acceder, registrarse o utilizar Rubra, el usuario declara haber leído, comprendido y aceptado íntegramente estos Términos y Condiciones. Si utiliza la plataforma en representación de una empresa, estudio, contratista u otra organización, declara contar con facultades suficientes para obligar válidamente a dicha entidad.',
                ],
                [
                    'num' => '4',
                    'titulo' => 'Naturaleza de la plataforma',
                    'cuerpo' => 'Rubra constituye una herramienta de apoyo técnico, administrativo y organizativo. No reemplaza el criterio profesional, técnico, contable, fiscal, contractual ni legal del usuario.

Toda validación final de cantidades, cómputos, precios, impuestos, cargas sociales, rendimientos, márgenes, documentación y resultados corresponde exclusivamente al usuario o al profesional que este designe. Los resultados generados por la plataforma dependen de la información cargada y de los criterios definidos por el propio usuario.',
                ],
                [
                    'num' => '5',
                    'titulo' => 'Registro y cuentas',
                    'cuerpo' => 'Para utilizar determinadas funcionalidades, el usuario deberá crear una cuenta y proporcionar información veraz, completa y actualizada. El usuario se compromete a no utilizar identidades falsas, no suplantar a terceros y no crear cuentas con información engañosa.

ApexObra podrá rechazar registros, requerir verificaciones adicionales o suspender cuentas cuando detecte inconsistencias, fraude, abuso, uso indebido o incumplimiento de estos Términos.',
                ],
                [
                    'num' => '6',
                    'titulo' => 'Credenciales y seguridad',
                    'cuerpo' => 'El usuario es responsable de custodiar su correo, contraseña y demás mecanismos de autenticación asociados a su cuenta. También es responsable por la actividad realizada desde ella, salvo que acredite un acceso no autorizado no imputable a su conducta o negligencia.

El usuario deberá notificar de inmediato cualquier sospecha de acceso indebido, uso no autorizado o incidente de seguridad. ApexObra podrá aplicar medidas preventivas razonables, tales como bloqueo temporal, validación reforzada, límites de acceso o cierre de sesiones.',
                ],
                [
                    'num' => '7',
                    'titulo' => 'Planes, precios y facturación',
                    'cuerpo' => 'Rubra podrá ofrecer planes gratuitos, demos, pruebas, suscripciones pagas, módulos opcionales y soluciones empresariales. Cada plan podrá establecer límites sobre cantidad de proyectos, usuarios, almacenamiento, exportaciones, integraciones, soporte y demás funcionalidades.

Los precios, moneda, impuestos, forma de cobro, frecuencia de facturación, renovaciones, promociones, cambios de plan y consecuencias ante mora deberán informarse en la oferta comercial, en el sitio o dentro de la plataforma. Salvo disposición legal en contrario, los importes ya devengados no serán reembolsables.',
                ],
                [
                    'num' => '8',
                    'titulo' => 'Licencia de uso',
                    'cuerpo' => 'Sujeto al cumplimiento de estos Términos y, cuando corresponda, al pago del plan contratado, ApexObra concede al usuario una licencia limitada, revocable, no exclusiva, intransferible y no sublicenciable para acceder y usar Rubra conforme a su finalidad prevista.

Esta licencia no implica cesión de propiedad intelectual ni habilita al usuario a copiar, revender, sublicenciar, descompilar, desensamblar, hacer ingeniería inversa o crear productos derivados de la plataforma.',
                ],
                [
                    'num' => '9',
                    'titulo' => 'Uso permitido y responsabilidad del usuario',
                    'cuerpo' => 'El usuario utilizará Rubra de manera lícita, diligente y conforme a la normativa aplicable. Será responsable por la calidad y legalidad de los datos que cargue, por la revisión final de la información emitida y por toda decisión profesional, comercial o contractual que adopte con apoyo en la plataforma.',
                ],
                [
                    'num' => '10',
                    'titulo' => 'Usos prohibidos',
                    'cuerpo' => 'Queda prohibido utilizar Rubra para fines ilícitos, fraudulentos o engañosos; vulnerar su seguridad; acceder sin autorización a cuentas de terceros; introducir malware; automatizar accesos en forma abusiva; hacer scraping no autorizado; compartir credenciales en forma contraria al plan contratado; o usar la plataforma para desarrollar servicios competidores.',
                ],
                [
                    'num' => '11',
                    'titulo' => 'Datos y contenidos del usuario',
                    'cuerpo' => 'El usuario conserva la titularidad sobre los datos, archivos, imágenes, presupuestos, reportes, comprobantes y demás contenidos que cargue o genere en Rubra. No obstante, autoriza a ApexObra a alojarlos, almacenarlos, estructurarlos, respaldarlos, procesarlos, mostrarlos al propio usuario y a sus colaboradores autorizados, y utilizarlos en la medida estrictamente necesaria para operar la plataforma y prestar el servicio.

El usuario garantiza que posee derechos suficientes sobre todo contenido que cargue en el sistema.',
                ],
                [
                    'num' => '12',
                    'titulo' => 'Soporte técnico y acceso autorizado',
                    'cuerpo' => 'Cuando el usuario solicite asistencia, revisión, corrección, configuración, mantenimiento, migración o modificación vinculada a su cuenta, autoriza expresamente a ApexObra a acceder a su perfil y a la información estrictamente necesaria para atender el requerimiento.

Ese acceso deberá limitarse a fines legítimos de soporte, continuidad operativa, seguridad o resolución de incidencias. No implica cesión de titularidad de los datos ni habilita un uso ajeno a la prestación del servicio.',
                ],
                [
                    'num' => '13',
                    'titulo' => 'Disponibilidad, mantenimiento y cambios',
                    'cuerpo' => 'ApexObra realizará esfuerzos razonables para mantener la plataforma operativa. Sin embargo, Rubra se presta según disponibilidad y puede experimentar interrupciones, degradaciones, mantenimientos programados, actualizaciones, fallas de terceros o eventos de fuerza mayor.

ApexObra podrá agregar, modificar, sustituir o discontinuar módulos, integraciones o funcionalidades por razones técnicas, legales, operativas o comerciales, procurando no vulnerar derechos adquiridos por normas imperativas aplicables.',
                ],
                [
                    'num' => '14',
                    'titulo' => 'Integraciones de terceros',
                    'cuerpo' => 'Rubra podrá integrarse con servicios de terceros, tales como almacenamiento, mapas, pasarelas de pago, herramientas analíticas, mensajería o autenticación. ApexObra no controla necesariamente dichos servicios y no responde por sus cambios, caídas, incompatibilidades, interrupciones o cargos externos.',
                ],
                [
                    'num' => '15',
                    'titulo' => 'Propiedad intelectual',
                    'cuerpo' => 'El software, su arquitectura, diseño, interfaces, documentación, marca, nombre comercial, logotipos, bases de datos estructurales y demás elementos distintivos o funcionales de Rubra son propiedad de ApexObra o se utilizan legítimamente bajo licencia o autorización. Nada de lo previsto en estos Términos supone cesión de tales derechos al usuario.',
                ],
                [
                    'num' => '16',
                    'titulo' => 'Protección de datos personales',
                    'cuerpo' => 'El tratamiento de datos personales se complementa con la Política de Privacidad de Rubra, la cual forma parte de este paquete legal. El usuario declara que los datos personales que cargue en la plataforma han sido obtenidos y serán tratados conforme a derecho y bajo su propia responsabilidad cuando correspondan a terceros.',
                ],
                [
                    'num' => '17',
                    'titulo' => 'Respaldo, exportación y conservación',
                    'cuerpo' => 'Rubra podrá implementar mecanismos de respaldo y exportación, pero no garantiza recuperación total frente a cualquier incidente. El usuario debe conservar copias propias de la información crítica.

En caso de baja, cancelación o mora prolongada, ApexObra podrá conservar los datos por un plazo razonable con fines técnicos, legales, de seguridad, facturación o prevención de fraude, tras lo cual podrá eliminarlos conforme a su política vigente.',
                ],
                [
                    'num' => '18',
                    'titulo' => 'Suspensión, cancelación y baja',
                    'cuerpo' => 'ApexObra podrá suspender o cancelar cuentas ante falta de pago, uso abusivo, fraude, riesgo de seguridad, incumplimiento de estos Términos o requerimiento legal. Cuando resulte razonable, podrá notificar previamente; no obstante, podrá actuar de inmediato si existe urgencia técnica o de seguridad.

El usuario podrá solicitar la baja conforme al procedimiento previsto en la plataforma o por soporte. La baja no elimina obligaciones ya devengadas ni implica reintegro automático salvo norma imperativa o política comercial expresa en contrario.',
                ],
                [
                    'num' => '19',
                    'titulo' => 'Exclusión de garantías',
                    'cuerpo' => 'En la máxima medida permitida por la ley aplicable, Rubra se proporciona "tal cual" y "según disponibilidad". ApexObra no garantiza que el servicio esté libre de errores, que cumpla necesidades específicas no pactadas o que los resultados sean exactos sin validación humana y profesional.',
                ],
                [
                    'num' => '20',
                    'titulo' => 'Limitación de responsabilidad',
                    'cuerpo' => 'En la máxima medida permitida por la normativa aplicable, ApexObra no será responsable por daños indirectos, lucro cesante, pérdida de oportunidad, pérdida de contratos, interrupción de actividad, pérdida de datos ni perjuicios derivados del uso o imposibilidad de uso de Rubra.

En particular, no responderá por errores en datos cargados por el usuario, decisiones técnicas o comerciales tomadas por este, diferencias de precios, cantidades o rendimientos, fallas de terceros o accesos indebidos derivados de una custodia deficiente de credenciales. En caso de corresponder responsabilidad legalmente exigible, esta se limitará al monto efectivamente abonado por el usuario durante los últimos meses previos al hecho reclamado, salvo norma imperativa en contrario.',
                ],
                [
                    'num' => '21',
                    'titulo' => 'Indemnidad',
                    'cuerpo' => 'El usuario mantendrá indemne a ApexObra, sus socios, directores, dependientes, contratistas y afiliados frente a reclamos, sanciones, daños, costos o gastos originados en el incumplimiento de estos Términos, en su uso indebido de la plataforma o en contenidos cargados por él que infrinjan derechos de terceros.',
                ],
                [
                    'num' => '22',
                    'titulo' => 'Modificaciones de los términos',
                    'cuerpo' => 'ApexObra podrá actualizar estos Términos por razones legales, técnicas, operativas o comerciales. La versión vigente será la publicada en el sitio o en la plataforma, y la continuidad en el uso del servicio implicará aceptación de la versión actualizada, sin perjuicio de los avisos razonables que deban realizarse.',
                ],
                [
                    'num' => '23',
                    'titulo' => 'Ley aplicable y jurisdicción',
                    'cuerpo' => 'Estos Términos se regirán por las leyes de la jurisdicción que ApexObra determine en su versión final. Toda controversia será sometida a los tribunales competentes según se determine en la versión definitiva de este documento, salvo que una norma imperativa disponga otro criterio.',
                ],
                [
                    'num' => '24',
                    'titulo' => 'Contacto',
                    'cuerpo' => 'Para consultas legales, administrativas o técnicas, el usuario podrá comunicarse a través del correo y canales oficiales que ApexObra publique junto con la versión definitiva del presente documento.',
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

        {{-- ─────────────────────────────────────────── --}}
        {{-- POLÍTICA DE SOPORTE                       --}}
        {{-- ─────────────────────────────────────────── --}}
        <div class="mt-24 mb-14">
            <div class="flex items-center gap-3 mb-3">
                <div class="h-[2px] w-8 bg-[#d15330]"></div>
                <span class="text-[10px] text-[#d15330] font-black uppercase tracking-widest">Legal</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-black uppercase tracking-tighter text-white mb-3">
                Política de Soporte
            </h1>
            <p class="text-white/30 text-xs uppercase tracking-widest font-bold">Última actualización: {{ date('d/m/Y') }}</p>
        </div>

        <div class="space-y-10 text-sm leading-relaxed">

            @php
            $soporte = [
                [
                    'num' => '1',
                    'titulo' => 'Objeto',
                    'cuerpo' => 'La presente Política de Soporte describe el alcance general de la asistencia que ApexObra puede brindar a los usuarios de Rubra, sin perjuicio de las condiciones específicas que correspondan al plan contratado o a acuerdos empresariales particulares.',
                ],
                [
                    'num' => '2',
                    'titulo' => 'Canales de soporte',
                    'cuerpo' => 'El soporte podrá prestarse por correo electrónico, formulario interno, mensajería, panel de ayuda u otros canales que ApexObra habilite oficialmente. ApexObra podrá modificar, centralizar o sustituir dichos canales en cualquier momento.',
                ],
                [
                    'num' => '3',
                    'titulo' => 'Alcance general del soporte',
                    'cuerpo' => 'Salvo que se acuerde expresamente algo distinto, el soporte está orientado a:

• resolver incidencias técnicas de la plataforma;
• asistir en configuración básica o uso general;
• orientar sobre funcionalidades disponibles;
• verificar errores reproducibles;
• acompañar procesos razonables de migración o importación cuando estén contemplados;
• atender consultas sobre acceso, cuenta, facturación o funcionamiento.',
                ],
                [
                    'num' => '4',
                    'titulo' => 'Qué no incluye el soporte estándar',
                    'cuerpo' => 'Salvo pacto expreso, el soporte estándar no incluye:

• consultoría profesional sobre presupuestación, costos, normativa, impuestos o gestión de obra;
• carga manual masiva de información por parte de ApexObra;
• personalizaciones a medida no incluidas en el plan;
• capacitación avanzada individual ilimitada;
• asistencia sobre software o hardware de terceros no controlados por ApexObra;
• corrección de errores originados en datos incorrectos cargados por el usuario.',
                ],
                [
                    'num' => '5',
                    'titulo' => 'Niveles de prioridad',
                    'cuerpo' => 'ApexObra podrá clasificar tickets o consultas según su criticidad, por ejemplo:

• Alta: caída total del sistema, imposibilidad general de acceso o falla crítica.
• Media: error funcional relevante con solución alternativa parcial.
• Baja: consultas de uso, mejoras, dudas operativas o incidencias menores.

Los tiempos de respuesta y resolución pueden variar según la prioridad, el plan contratado, el horario de recepción, la complejidad técnica y la necesidad de intervención de terceros.',
                ],
                [
                    'num' => '6',
                    'titulo' => 'Horarios y tiempos de atención',
                    'cuerpo' => 'Salvo acuerdo comercial específico, ApexObra no garantiza atención 24/7 ni tiempos fijos de resolución. Los tiempos publicados o comunicados tendrán carácter estimativo y razonable, no constituyendo una obligación de resultado salvo pacto expreso en un SLA particular.',
                ],
                [
                    'num' => '7',
                    'titulo' => 'Acceso a la cuenta para asistencia',
                    'cuerpo' => 'Cuando sea necesario para diagnosticar o resolver un problema y el usuario haya solicitado soporte, ApexObra podrá acceder a la cuenta, proyectos y datos estrictamente necesarios para la asistencia técnica, conforme a lo previsto en los Términos y Condiciones y en la Política de Privacidad.',
                ],
                [
                    'num' => '8',
                    'titulo' => 'Requisitos de colaboración del usuario',
                    'cuerpo' => 'Para recibir soporte eficaz, el usuario deberá colaborar proporcionando información suficiente, capturas, pasos para reproducir el problema, archivos de ejemplo, permisos necesarios y demás elementos razonables que ApexObra solicite para el diagnóstico.',
                ],
                [
                    'num' => '9',
                    'titulo' => 'Limitaciones',
                    'cuerpo' => 'ApexObra hará esfuerzos razonables para asistir al usuario, pero no garantiza que toda consulta tenga solución inmediata o que todo requerimiento de mejora sea implementado. La admisión de una solicitud no implica obligación de desarrollar nuevas funciones ni de alterar la hoja de ruta del producto.',
                ],
                [
                    'num' => '10',
                    'titulo' => 'Cambios a esta política',
                    'cuerpo' => 'ApexObra podrá actualizar esta Política de Soporte para adecuarla a cambios de producto, procesos internos o condiciones comerciales. La versión vigente será la publicada en el sitio o en la plataforma.',
                ],
            ];
            @endphp

            @foreach($soporte as $s)
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
