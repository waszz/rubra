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
                Política de Privacidad
            </h1>
            <p class="text-white/30 text-xs uppercase tracking-widest font-bold">Última actualización: {{ date('d/m/Y') }}</p>
        </div>

        {{-- Contenido --}}
        <div class="space-y-10 text-sm leading-relaxed">

            @php
            $secciones = [
                [
                    'num' => '1',
                    'titulo' => 'Finalidad de esta política',
                    'cuerpo' => 'La presente Política de Privacidad explica qué datos puede recopilar Rubra, con qué finalidades los trata, con quién puede compartirlos, cuánto tiempo puede conservarlos y qué derechos corresponden a los usuarios y titulares de datos.',
                ],
                [
                    'num' => '2',
                    'titulo' => 'Responsable del tratamiento',
                    'cuerpo' => 'El responsable del tratamiento será ApexObra, en su calidad de titular y operador de Rubra, con los datos identificatorios que se completen al momento de publicación de esta política.',
                ],
                [
                    'num' => '3',
                    'titulo' => 'Datos que podemos recopilar',
                    'cuerpo' => 'Rubra podrá tratar, según el uso de la plataforma y los servicios contratados, las siguientes categorías de datos:

• Datos de identificación y contacto: nombre, apellido, correo electrónico, teléfono, empresa, cargo.
• Datos de cuenta y autenticación: usuario, contraseñas cifradas, tokens, historial de acceso, verificaciones de seguridad.
• Datos de uso y soporte: registros de actividad, eventos del sistema, errores, métricas de uso, consultas de soporte.
• Datos de proyecto y operación: rubros, materiales, composiciones, presupuestos, reportes, imágenes, comprobantes, archivos y documentación cargada.
• Datos de facturación y comerciales: plan contratado, pagos, vencimientos, comprobantes, información tributaria o comercial suministrada por el usuario.
• Datos de terceros cargados por el usuario: información de colaboradores, clientes, proveedores, contratistas u otras personas relacionadas con el proyecto, cuando el usuario decida incorporarlos a la plataforma.',
                ],
                [
                    'num' => '4',
                    'titulo' => 'Finalidades del tratamiento',
                    'cuerpo' => 'Los datos podrán ser tratados para:

• prestar, mantener y mejorar el servicio;
• crear y administrar cuentas;
• autenticar usuarios y proteger accesos;
• procesar información cargada dentro de la plataforma;
• brindar soporte técnico;
• responder consultas;
• gestionar planes, pagos y renovaciones;
• prevenir fraude, abuso o incidentes de seguridad;
• realizar métricas internas y análisis de uso;
• cumplir obligaciones legales, regulatorias, fiscales o requerimientos de autoridad competente.',
                ],
                [
                    'num' => '5',
                    'titulo' => 'Base o fundamento del tratamiento',
                    'cuerpo' => 'El tratamiento podrá fundarse, según el caso, en una o más de las siguientes bases: ejecución de la relación contractual o precontractual con el usuario; cumplimiento de obligaciones legales; interés legítimo de ApexObra en operar, proteger y mejorar la plataforma; y consentimiento del titular cuando corresponda.',
                ],
                [
                    'num' => '6',
                    'titulo' => 'Datos aportados por el usuario sobre terceros',
                    'cuerpo' => 'Cuando el usuario cargue datos personales de terceros dentro de Rubra, declara bajo su exclusiva responsabilidad que cuenta con legitimación suficiente para hacerlo y que ha cumplido con los deberes de información, autorización o base legal exigibles en su jurisdicción.',
                ],
                [
                    'num' => '7',
                    'titulo' => 'Conservación de la información',
                    'cuerpo' => 'Los datos se conservarán durante el tiempo necesario para cumplir las finalidades que motivaron su recolección, prestar el servicio, cumplir obligaciones legales, resolver disputas, prevenir fraude, ejercer defensas o atender requerimientos regulatorios.

Aun después de la baja de la cuenta, cierta información podrá mantenerse durante un plazo limitado por razones legales, contables, de seguridad o respaldo, tras lo cual podrá eliminarse o anonimizarse.',
                ],
                [
                    'num' => '8',
                    'titulo' => 'Compartición de datos',
                    'cuerpo' => 'ApexObra podrá compartir datos con proveedores tecnológicos, de alojamiento, procesamiento, analítica, seguridad, correo, soporte o facturación, siempre en la medida razonablemente necesaria para operar Rubra.

También podrá comunicar información cuando exista obligación legal, requerimiento válido de autoridad competente, necesidad de prevenir fraude o riesgo, o en el marco de una reorganización societaria, adquisición o transferencia de activos, respetando la normativa aplicable.',
                ],
                [
                    'num' => '9',
                    'titulo' => 'Transferencias internacionales',
                    'cuerpo' => 'Si para operar la plataforma se utilizan servicios o proveedores ubicados fuera del país del usuario, los datos podrán ser transferidos internacionalmente. En ese caso, ApexObra procurará adoptar salvaguardas razonables y seleccionar proveedores con estándares adecuados de seguridad y cumplimiento.',
                ],
                [
                    'num' => '10',
                    'titulo' => 'Seguridad de la información',
                    'cuerpo' => 'ApexObra adopta medidas técnicas y organizativas razonables para proteger la información contra pérdida, acceso no autorizado, divulgación indebida, alteración o destrucción. Sin embargo, ningún sistema es absolutamente invulnerable, por lo que no puede garantizarse una seguridad total en todo momento.',
                ],
                [
                    'num' => '11',
                    'titulo' => 'Derechos de los titulares',
                    'cuerpo' => 'Los titulares de datos personales podrán ejercer, conforme a la normativa aplicable, sus derechos de acceso, rectificación, actualización, oposición, supresión, portabilidad o revocación del consentimiento, cuando correspondan.

Las solicitudes deberán canalizarse por los medios de contacto oficiales que ApexObra publique junto con esta política. Antes de responder, podrán requerirse verificaciones razonables de identidad.',
                ],
                [
                    'num' => '12',
                    'titulo' => 'Menores de edad',
                    'cuerpo' => 'Rubra no está diseñada para ser utilizada por menores de edad sin la intervención o autorización válida de sus representantes, salvo que una funcionalidad o servicio específico disponga otra cosa conforme a derecho.',
                ],
                [
                    'num' => '13',
                    'titulo' => 'Cambios a esta política',
                    'cuerpo' => 'ApexObra podrá actualizar esta Política de Privacidad por motivos legales, regulatorios, técnicos o comerciales. La versión vigente será la publicada en el sitio o en la plataforma.',
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
