<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;
use App\Livewire\Concerns\AutorizaProyecto;
use App\Models\Recurso;
use App\Models\ProyectoRecurso;
use Illuminate\Support\Facades\Http;
use App\Utils\FuzzyMatcher;

class ChatbotRubi extends Component
{
    use AutorizaProyecto;

    public Proyecto $proyecto;

    public array  $mensajes   = [];
    public string $input      = '';
    public bool   $cargando   = false;
    public bool   $abierto    = false;
    public string $error      = '';
    public bool   $modoLectura = false;

    public function mount(Proyecto $proyecto): void
    {
        $this->autorizarAcceso($proyecto);
        $this->proyecto = $proyecto;

        // Presupuesto bloqueado en ejecucion, pausado y finalizado
        // en_revision permite editar presupuesto pero no acceder a ejecución
        $this->modoLectura = in_array($proyecto->estado_obra, ['ejecucion', 'pausado', 'finalizado']);

        $bienvenida = match ($proyecto->estado_obra) {
            'activo'      => "¡Hola! Soy Rubí, tu asistente de presupuestación de RUBRA.\nEste proyecto está **activo**. Puedo ayudarte a:\n• Presupuestar obras nuevas, reformas o ampliaciones\n• Calcular costos con análisis de precios unitarios\n• Comparar variantes constructivas\n• Crear rubros, subrubros y agregar recursos al presupuesto\n\n¿Querés empezar?",
            'ejecucion'   => "¡Hola! Soy Rubí. El proyecto está en **ejecución**.\nEl presupuesto está bloqueado para modificaciones. Podés consultar costos, ver el desglose por rubro y registrar el avance de obra, pero no se puede editar el presupuesto.\n\n¿En qué te ayudo?",
            'en_revision' => "¡Hola! Soy Rubí. Este proyecto está **en revisión**.\nPodés editar el presupuesto libremente, pero la vista de Ejecución no está disponible hasta que el proyecto avance de estado.\nPuedo ayudarte a crear rubros, agregar recursos y estimar costos.",
            'pausado'     => "¡Hola! Soy Rubí. Este proyecto está **pausado**.\nEl presupuesto está bloqueado temporalmente. Solo puedo responder consultas informativas.\nCuando el proyecto se reactive, podrás volver a editar el presupuesto.",
            'finalizado'  => "¡Hola! Soy Rubí. Este proyecto está **finalizado**.\nEl presupuesto es de solo lectura. Puedo mostrarte resúmenes, desglosar costos y responder consultas, pero no es posible realizar modificaciones.",
            default       => "¡Hola! Soy Rubí, tu asistente de RUBRA. ¿En qué puedo ayudarte con este proyecto?",
        };

        $this->mensajes = [
            [
                'role'    => 'assistant',
                'content' => $bienvenida,
            ],
        ];
    }

    public function toggle(): void
    {
        $this->abierto = !$this->abierto;
    }

    public function enviar(?string $override = null): void
    {
        $texto = trim($override ?? $this->input);
        if (!$texto || $this->cargando) return;

        // Bloquear acciones de modificación si el proyecto está en modo lectura
        if ($this->modoLectura) {
            $this->mensajes[] = ['role' => 'user', 'content' => $texto];
            $this->input = '';
            // Aún así, permite responder consultas informativas via IA
        }

        $this->input    = '';
        $this->cargando = true;
        $this->error    = '';

        // Contexto del proyecto
        $rubros = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->whereNull('parent_id')
            ->with('hijos')
            ->get()
            ->map(fn($r) => [
                'id'       => $r->id,
                'nombre'   => $r->nombre,
                'unidad'   => $r->unidad,
                'cantidad' => $r->cantidad,
                'precio'   => $r->precio_usd,
                'categoria' => $r->categoria,
                'hijos'    => $r->hijos->map(fn($h) => [
                    'id'       => $h->id,
                    'nombre'   => $h->nombre,
                    'unidad'   => $h->unidad,
                    'cantidad' => $h->cantidad,
                    'precio'   => $h->precio_usd,
                    'categoria' => $h->categoria,
                ]),
            ]);

        $recursos = Recurso::take(50)->get(['id', 'nombre', 'unidad', 'precio_usd', 'tipo']);

        // Mapeo de palabras clave a recursos eléctricos
        $cable25 = $recursos->firstWhere('nombre', 'Cable Unipolar 2.5mm');
        $cable15 = $recursos->firstWhere('nombre', 'Cable Unipolar 1.5mm');
        $llaveTermica = $recursos->firstWhere('nombre', 'Llave Térmica 16A');
        $llaveLuz = $recursos->firstWhere('nombre', 'Llave de Luz (Punto)');
        $tomacorriente = $recursos->firstWhere('nombre', 'Tomacorriente Doble');

        $electricosMap = "PALABRAS CLAVE → ID: NOMBRE
CABLE 2.5mm = " . ($cable25?->id ?? '?') . ": Cable Unipolar 2.5mm
CABLE 1.5mm = " . ($cable15?->id ?? '?') . ": Cable Unipolar 1.5mm  
LLAVE TERMICA = " . ($llaveTermica?->id ?? '?') . ": Llave Térmica 16A
LLAVE LUZ = " . ($llaveLuz?->id ?? '?') . ": Llave de Luz (Punto)
TOMACORRIENTE = " . ($tomacorriente?->id ?? '?') . ": Tomacorriente Doble";

        // Generar sugerencias fuzzy basadas en el input del usuario
        $recursosArray = $recursos->toArray();
        $sugerenciasFuzzy = $this->generarSugerenciasFuzzy($texto, $recursosArray);

        // Generar tabla de rubros con mejor formato jerárquico
        $tablaBien = $rubros->map(function($r) {
            $linea = "ID {$r['id']}: {$r['nombre']}";
            if (!empty($r['hijos'])) {
                $linea .= "\n";
                foreach ($r['hijos'] as $h) {
                    $linea .= "    ├─ ID {$h['id']}: {$h['nombre']} (subrubro)\n";
                }
            }
            return $linea;
        })->implode("");

        $bloqueadoTexto = match ($this->proyecto->estado_obra) {
            'activo' =>
                "ESTADO: ACTIVO — EDICION COMPLETA PERMITIDA\n\nPodés realizar todas las acciones disponibles: crear_rubro, crear_subrubro, agregar_recurso, actualizar_cantidad, eliminar_rubro, eliminar_subrubro, eliminar_recurso.",
            'ejecucion' =>
                "ESTADO: EN EJECUCION — PRESUPUESTO BLOQUEADO\n\nEl proyecto está en ejecución. ESTA TERMINANTEMENTE PROHIBIDO modificar el presupuesto.\nACCIONES BLOQUEADAS: crear_rubro, crear_subrubro, agregar_recurso, actualizar_cantidad, eliminar_rubro, eliminar_subrubro, eliminar_recurso.\n\nSi el usuario pide cambios en el presupuesto, respondé CON ESTE JSON EXACTO:\n{\"mensaje\": \"No puedo modificar el presupuesto. El proyecto está en ejecución y el presupuesto está bloqueado. Solo puedo responder consultas sobre costos y avance de obra.\"}\n\nSolo consultás información. NUNCA ejecutes acciones de modificación.",
            'en_revision' =>
                "ESTADO: EN REVISION — EDICION DE PRESUPUESTO PERMITIDA\n\nPodés realizar todas las acciones sobre el presupuesto: crear_rubro, crear_subrubro, agregar_recurso, actualizar_cantidad, eliminar_rubro, eliminar_subrubro, eliminar_recurso.\nLO UNICO BLOQUEADO es el acceso a la vista de Ejecución (eso lo maneja la interfaz, no vos).\nSi el usuario pregunta por ejecución o avance de obra, respondé:\n{\"mensaje\": \"La vista de Ejecución no está disponible mientras el proyecto está en revisión. Podés seguir editando el presupuesto normalmente.\"}",
            'pausado' =>
                "ESTADO: PAUSADO — PRESUPUESTO BLOQUEADO\n\nEl proyecto está pausado. ESTA TERMINANTEMENTE PROHIBIDO modificar el presupuesto.\nACCIONES BLOQUEADAS: crear_rubro, crear_subrubro, agregar_recurso, actualizar_cantidad, eliminar_rubro, eliminar_subrubro, eliminar_recurso.\n\nSi el usuario pide cambios, respondé CON ESTE JSON EXACTO:\n{\"mensaje\": \"No puedo realizar cambios. El proyecto está pausado y el presupuesto está bloqueado temporalmente. Cuando el proyecto se reactive, podrás editar el presupuesto.\"}\n\nSolo consultás información. NUNCA ejecutes acciones de modificación.",
            'finalizado' =>
                "ESTADO: FINALIZADO — PRESUPUESTO SOLO LECTURA\n\nEl proyecto está finalizado. ESTA TERMINANTEMENTE PROHIBIDO realizar cualquier modificación.\nACCIONES BLOQUEADAS: crear_rubro, crear_subrubro, agregar_recurso, actualizar_cantidad, eliminar_rubro, eliminar_subrubro, eliminar_recurso.\n\nSi el usuario pide cambios, respondé CON ESTE JSON EXACTO:\n{\"mensaje\": \"No puedo realizar cambios. El proyecto está finalizado y el presupuesto es de solo lectura.\"}\n\nSolo consultás información. NUNCA ejecutes acciones de modificación.",
            default =>
                "ESTADO: {$this->proyecto->estado_obra} — podés realizar todas las acciones disponibles.",
        };

        $modoLabel = match ($this->proyecto->estado_obra) {
            'activo'      => 'ACTIVO (EDICION COMPLETA)',
            'ejecucion'   => 'EN EJECUCION (SOLO LECTURA DE PRESUPUESTO)',
            'en_revision' => 'EN REVISION (SOLO LECTURA)',
            'pausado'     => 'PAUSADO (SOLO LECTURA)',
            'finalizado'  => 'FINALIZADO (SOLO LECTURA)',
            default       => strtoupper($this->proyecto->estado_obra),
        };

        $systemPrompt = <<<PROMPT
RUBI - ASISTENTE DE PRESUPUESTACION DE OBRAS

================================================================
IDENTIDAD Y ROL
================================================================

Sos Rubí, asistente especializada en presupuestación de obras de construcción del sistema RUBRA.
Ayudás a calcular costos de materiales, mano de obra, equipos y gastos generales.
Trabajás con los precios y recursos registrados en el proyecto actual.
Cuando el usuario describe una obra o pide estimaciones, hacés preguntas específicas para obtener
dimensiones, calidad de terminaciones y plazos antes de calcular.
Siempre presentás presupuestos desglosados por rubro.
Si hay incertidumbre en algún ítem, indicás un rango mínimo–máximo.
No inventás precios: si no tenés el dato, informás qué información necesitás.

PROYECTO: {$this->proyecto->nombre_proyecto}
ESTADO: {$this->proyecto->estado_obra}

================================================================
INSTRUCCION CRITICA: SIEMPRE RESPONDE EN JSON
================================================================

NUNCA respondas en texto libre. SIEMPRE respondé EN JSON con la estructura exacta.
NO INCLUYAS explicación fuera del JSON.
NO ESCRIBAS "Listo" o texto adicional.
SOLO JSON, NADA MAS.

Para respuestas conversacionales, consultas, preguntas de relevamiento o presentación de presupuestos estimados,
usá SIEMPRE: {"mensaje": "tu texto aquí usando \\n para saltos de línea"}

================================================================
MODO DE PROYECTO: {$modoLabel}
================================================================

{$bloqueadoTexto}

================================================================
RELEVAMIENTO INICIAL (ANTES DE CALCULAR)
================================================================

Cuando el usuario pida presupuestar una obra nueva o estimación de costos, SIEMPRE preguntá primero:
1. Tipo de obra: ¿es obra nueva, reforma o ampliación?
2. Superficie total en m²
3. Ubicación / zona geográfica
4. Calidad de terminaciones: económica, media o alta
5. Plazo de ejecución deseado

Si la obra tiene etapas, identificá cada una (demolición, estructura, instalaciones, terminaciones)
y preguntá cuál se quiere presupuestar primero. Presentá el costo por etapa y el total acumulado.

Guiá al usuario con preguntas de a UNA por vez si no sabe por dónde empezar.
Usá lenguaje simple y confirmá cada dato antes de avanzar al siguiente.

================================================================
FORMATO ESTÁNDAR DE PRESUPUESTO (usar en {"mensaje": "..."})
================================================================

Presentá todos los presupuestos con esta estructura exacta (usando \n para saltos de línea):

════════════════════════════════
RESUMEN EJECUTIVO
Total estimado: \$X.XXX USD
Costo por m²: \$XXX USD/m²
Fecha de referencia: [mes/año]
⚠️ Los precios pueden variar. Cotizá con proveedores locales antes de firmar contrato.
════════════════════════════════
DESGLOSE POR RUBRO
1. Materiales:       \$X.XXX  (XX%)
2. Mano de obra:     \$X.XXX  (XX%)
3. Equipos:          \$X.XXX  (XX%)
4. Gastos generales: \$X.XXX  (XX%)
════════════════════════════════
ÍTEMS PRINCIPALES
Ítem          | Cant | Unid | P.Unit | Subtotal
--------------+------+------+--------+---------
[descripción] | XXX  |  m²  | \$XXX   | \$X.XXX
════════════════════════════════
IMPREVISTOS (10-15% recomendado): \$X.XXX
════════════════════════════════
NOTAS Y SUPUESTOS
- [supuesto 1]
- [qué puede variar el precio]
════════════════════════════════

================================================================
ANÁLISIS DE PRECIOS UNITARIOS
================================================================

Cuando calculés un ítem, mostrá:
- Insumos con rendimiento
- Mano de obra con categoría y horas
- Costo total por unidad de medida

Para comparativas, mostrá tabla con 2–3 variantes (ej: ladrillo común vs steel frame vs hormigón)
indicando diferencia de costo, plazo y mantenimiento futuro.

================================================================
ADVERTENCIA DE PRECIOS E INFLACIÓN
================================================================

SIEMPRE indicá la fecha de referencia de los precios usados.
NUNCA presentés un presupuesto como precio fijo sin advertir que los materiales pueden variar.
Recomendá cotizar con proveedores locales antes de firmar contratos.

================================================================
OPCIONES AL FINALIZAR UN PRESUPUESTO
================================================================

Al finalizar un presupuesto completo, SIEMPRE ofrecé estas tres opciones en el {"mensaje": "..."}:
1. Ver el desglose completo de todos los ítems
2. Obtener un resumen de una página para compartir con el cliente
3. Listar solo los materiales para cotizar con proveedores

================================================================
RUBROS ACTUALES (RUBRO = sin padre, SUBRUBRO = con parent_id)
════════════════════════════════════════════════════════════════

ESTRUCTURA JERÁRQUICA DE RUBROS Y SUBRUBROS:
{$tablaBien}

RUBROS PRINCIPALES (SIN PADRE):
{$rubros->map(fn($r) => "ID {$r['id']}: {$r['nombre']}")->implode(" | ")}

RECURSOS DISPONIBLES (ID|Nombre|Tipo|Precio):
{$recursos->take(100)->map(fn($r) => "{$r['id']}|{$r['nombre']}|{$r['tipo']}|{$r['precio_usd']}")->implode(" || ")}

MAPEO RÁPIDO MATERIALES ELÉCTRICOS:
{$electricosMap}

{$sugerenciasFuzzy}

════════════════════════════════════════════════════════════════
🎯 RUBRO vs SUBRUBRO
════════════════════════════════════════════════════════════════

RUBRO (parent_id=null): Categoría principal (Estructuras, Mampostería, Instalaciones)
SUBRUBRO (parent_id=ID): Item dentro de un rubro (Electricidad bajo Instalaciones)

🔍 CÓMO BUSCAR EL parent_id (⭐ CRÍTICO: LEE ESTO SIEMPRE):
1. MIRA LA LISTA "RUBROS PRINCIPALES (SIN PADRE)" arriba en este mensaje
2. Busca el nombre del rubro padre que el usuario mencionó
3. Copia EXACTAMENTE el ID numérico de esa fila
4. Ese es el parent_id que necesitas usar
5. ⚠️ JAMÁS uses IDs de conversaciones anteriores, SOLO los de la lista de arriba

EJEMPLO PASO A PASO:
Entrada del usuario: "crear subrubro Electricidad en 05. Instalaciones"
→ Busco "05. Instalaciones" en RUBROS PRINCIPALES
→ Encuentro: "ID 999: 05. Instalaciones" (ejemplo real será diferente)
→ parent_id = 999
→ Respuesta: {"acciones": [{"accion": "crear_subrubro", "nombre": "Electricidad", "parent_id": 999}]}

REGLAS ESTRICTAS:
- NUNCA crear "Electricidad" como RUBRO + "Instalaciones" como RUBRO (son categorías iguales)
- SIEMPRE crear "Electricidad" como SUBRUBRO con parent_id del rubro Instalaciones
- El parent_id DEBE estar en la lista de "RUBROS PRINCIPALES" arriba
- Si no encuentras el rubro padre en la lista, PREGUNTA al usuario cuál es

════════════════════════════════════════════════════════════════
📝 FORMATOS JSON (COPIA EXACTAMENTE - RESPONDE SOLO JSON)
════════════════════════════════════════════════════════════════

✅ CREAR SUBRUBRO:
{"acciones": [{"accion": "crear_subrubro", "nombre": "Electricidad", "parent_id": 368}]}

✅ AGREGAR UN RECURSO:
{"acciones": [{"accion": "agregar_recurso", "parent_id": 368, "recurso_id": 42, "cantidad": 500}]}

✅ AGREGAR MÚLTIPLES RECURSOS (3 cables diferentes):
{"acciones": [{"accion": "agregar_recurso", "parent_id": 368, "recurso_id": 21, "cantidad": 500}, {"accion": "agregar_recurso", "parent_id": 368, "recurso_id": 22, "cantidad": 100}, {"accion": "agregar_recurso", "parent_id": 368, "recurso_id": 23, "cantidad": 20}]}

✅ SI NO ENCUENTRAS RECURSO:
{"mensaje": "No encontré ese material en la base de datos"}

════════════════════════════════════════════════════════════════
🚀 REGLAS DE BÚSQUEDA DE RECURSOS
════════════════════════════════════════════════════════════════

MATCHING PARCIAL (Búsqueda Inteligente):
- Usuario pide "cables 2mm" → BUSCA "Cable" Y "2" en la lista → encontrará "Cable Unipolar 2.5mm"
- Usuario pide "cables 1mm" → BUSCA "Cable" Y "1.5" en la lista → encontrará "Cable Unipolar 1.5mm"
- Usuario pide "termicas 20" → BUSCA "Térmica" o "Llave Térmica" → encontrará "Llave Térmica 16A"
- Usuario pide "llave de luz" → BUSCA "Llave" Y "Luz" → encontrará "Llave de Luz (Punto)"
- NO busques coincidencia exacta, busca PALABRAS CLAVE

CANTIDADES:
- Cables: metros (m)
- Termicas/Llaves: cantidad en unidades
- Inferir si faltan

JAMÁS AGREGUES SINO ENCUENTRAS:
- Si usuario pide "items especiales" sin coincidencia → RESPONDER EN JSON: {"mensaje": "No encontré ese material"}
- NO AGREGUES el recurso genérico por defecto

PRIORIDAD DE BÚSQUEDA (en este orden):
1️⃣ BÚSQUEDA FUZZY (arriba): Análisis inteligente del input → recursos sugeridos ordenados por similaridad
2️⃣ MAPEO RÁPIDO: Para materiales eléctricos comunes (Cable, Llave, Tomacorriente, etc)
3️⃣ RECURSOS DISPONIBLES: Búsqueda en la lista completa si no hay coincidencia en los anteriores

════════════════════════════════════════════════════════════════
🔥 REGLA DE ORO: RESPUESTA EN JSON PURO
════════════════════════════════════════════════════════════════

❌ INCORRECTO: "Voy a crear el subrubro Electricidad en Instalaciones... {"acciones": [...]}"
✅ CORRECTO: {"acciones": [{"accion": "crear_subrubro", "nombre": "Electricidad", "parent_id": 368}]}

❌ INCORRECTO: "Aquí está el JSON: {\"acciones\": ...}"
✅ CORRECTO: {"acciones": [{"accion": "crear_subrubro", "nombre": "Electricidad", "parent_id": 368}]}

Solo JSON. Nada de texto. Nada de explicación. JSON puro.
PROMPT;

        // Construir historial — Groq usa formato OpenAI (igual que Claude pero con system en el array)
        // El primer mensaje de bienvenida va como system, no como assistant
        $historial = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($this->mensajes as $index => $msg) {
            // Saltear bienvenida inicial
            if ($index === 0 && $msg['role'] === 'assistant') continue;
            $historial[] = [
                'role'    => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        // Fusionar mensajes consecutivos del mismo rol
        $historialLimpio = [array_shift($historial)]; // mantener system
        foreach ($historial as $msg) {
            $ultimo = end($historialLimpio);
            if ($ultimo && $ultimo['role'] === $msg['role'] && $ultimo['role'] !== 'system') {
                $historialLimpio[count($historialLimpio) - 1]['content'] .= "\n" . $msg['content'];
            } else {
                $historialLimpio[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }
        }

        // Agregar mensaje actual
        $historialLimpio[] = ['role' => 'user', 'content' => $texto];

        // Agregar a UI
        $this->mensajes[] = ['role' => 'user', 'content' => $texto];

        try {
            $apiKey = config('services.groq.key');

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'llama-3.1-8b-instant',
                    'messages'    => $historialLimpio,
                    'temperature' => 0.3,
                    'max_tokens'  => 1024,
                ]);

            if ($response->status() === 429) {
                throw new \Exception('Límite de requests alcanzado. Esperá unos segundos y volvé a intentar.');
            }

            if (!$response->successful()) {
                $detalle = $response->json('error.message') ?? $response->status();
                throw new \Exception('Error de la API: ' . $detalle);
            }

            $text = $response->json('choices.0.message.content') ?? '';
            $text = trim(preg_replace('/^```json\s*|^```\s*|```\s*$/m', '', trim($text)));

            $data = json_decode($text, true);

            if (!$data) {
                $this->mensajes[] = ['role' => 'assistant', 'content' => $text ?: 'No pude procesar esa solicitud.'];
                $this->cargando   = false;
                $this->dispatch('scroll-chat');
                return;
            }

            // Ejecutar acciones
            $acciones   = isset($data['acciones']) ? $data['acciones'] : [$data];
            $resultados = [];

            foreach ($acciones as $accion) {
                $resultado = $this->ejecutarAccion($accion);
                if ($resultado) $resultados[] = $resultado;
            }

            $mensajeRespuesta = implode("\n", array_filter($resultados));
            if (!$mensajeRespuesta && isset($data['mensaje'])) {
                $mensajeRespuesta = $data['mensaje'];
            }
            if (!$mensajeRespuesta) {
                $mensajeRespuesta = 'Listo ✓';
            }

            $this->mensajes[] = ['role' => 'assistant', 'content' => $mensajeRespuesta];

        } catch (\Exception $e) {
            $this->error      = $e->getMessage();
            $this->mensajes[] = ['role' => 'assistant', 'content' => 'Ocurrió un error: ' . $e->getMessage()];
        }

        $this->cargando = false;
        $this->dispatch('scroll-chat');
    }

    /**
     * Genera sugerencias fuzzy para palabras clave encontradas en el input
     */
    private function generarSugerenciasFuzzy(string $input, array $recursos): string
    {
        // Palabras clave comunes para buscar
        $palabrasClaveComunes = [
            'cable', 'cables', 'llave', 'llaves', 'termica', 'térmicas', 'tomacorriente',
            'toma', 'corriente', 'luz', 'electricidad', 'electrico', 'caja', 'tubo',
            'hierro', 'acero', 'hormigon', 'ladrillo', 'arena', 'cemento', 'pintura',
            'tuberia', 'caño', 'valvula', 'ducha', 'baño', 'pileta'
        ];

        $inputLower = strtolower($input);
        $palabrasEncontradas = [];

        // Detectar palabras clave en el input
        foreach ($palabrasClaveComunes as $palabra) {
            if (str_contains($inputLower, $palabra)) {
                $palabrasEncontradas[] = $palabra;
            }
        }

        if (empty($palabrasEncontradas)) {
            return '';
        }

        // Buscar recursos fuzzy para cada palabra encontrada
        $sugerencias = [];
        foreach (array_unique($palabrasEncontradas) as $palabra) {
            $matches = FuzzyMatcher::search($palabra, $recursos, 3, 0.25);
            
            if (!empty($matches)) {
                foreach ($matches as $match) {
                    $recurso = $match['item'];
                    $similarity = round($match['similarity'] * 100);
                    $sugerencias[] = [
                        'palabra' => $palabra,
                        'recurso_id' => $recurso['id'],
                        'recurso_nombre' => $recurso['nombre'],
                        'similarity' => $similarity,
                    ];
                }
            }
        }

        if (empty($sugerencias)) {
            return '';
        }

        // Formatear sugerencias para el prompt
        $sugerenciasTexto = [];
        foreach ($sugerencias as $s) {
            $sugerenciasTexto[] = "BUSQUEDA '{$s['palabra']}' → ID {$s['recurso_id']}: {$s['recurso_nombre']} ({$s['similarity']}% match)";
        }

        return "════════════════════════════════════════════════════════════════\n" .
               "🔍 BÚSQUEDA FUZZY (análisis inteligente del input del usuario)\n" .
               "════════════════════════════════════════════════════════════════\n\n" .
               implode("\n", $sugerenciasTexto);
    }

    private function ejecutarAccion(array $accion): string
    {
        $tipo = $accion['accion'] ?? '';

        // Bloquear cualquier acción de modificación si el proyecto está en modo lectura
        $accionesModificacion = ['crear_rubro', 'crear_subrubro', 'agregar_recurso', 'actualizar_cantidad', 'eliminar_rubro', 'eliminar_subrubro', 'eliminar_recurso'];
        if ($this->modoLectura && in_array($tipo, $accionesModificacion)) {
            return '⛔ Acción bloqueada: el proyecto está en ejecución y el presupuesto no puede modificarse.';
        }

        switch ($tipo) {

            case 'crear_rubro':
                // Validar que el rubro no exista ya
                $existe = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
                    ->whereNull('parent_id')
                    ->whereRaw('LOWER(nombre) = ?', [strtolower($accion['nombre'])])
                    ->first();
                
                if ($existe) {
                    return "✗ El rubro '{$existe->nombre}' ya existe. Agregá elementos dentro de él en lugar de crear otro.";
                }
                
                $rubro = $this->proyecto->proyectoRecursos()->create([
                    'parent_id'  => null,
                    'recurso_id' => null,
                    'nombre'     => $accion['nombre'],
                    'unidad'     => $accion['unidad'] ?? 'gl',
                    'cantidad'   => 1,
                    'precio_usd' => 0,
                    'categoria'  => $accion['categoria'] ?? $accion['nombre'],
                ]);
                $this->dispatch('presupuesto-actualizado');
                return "✓ Rubro {$rubro->nombre} creado.";

            case 'crear_subrubro':
                // Castear parent_id a int para evitar problemas de comparación
                $parent_id = (int)($accion['parent_id'] ?? 0);
                $proyecto_id = (int)$this->proyecto->id;
                
                \Log::info("crear_subrubro: parent_id=$parent_id, proyecto_id=$proyecto_id, nombre={$accion['nombre']}");
                
                // Validar que parent_id sea válido
                if ($parent_id <= 0) {
                    return "✗ El parent_id no es válido: {$accion['parent_id']}";
                }

                // Validar que parent_id sea del proyecto actual
                $padre = ProyectoRecurso::where('proyecto_id', $proyecto_id)
                    ->where('id', $parent_id)
                    ->whereNull('parent_id')
                    ->first();
                
                if (!$padre) {
                    // Mostrar IDs disponibles para debugging
                    $idsDisponibles = ProyectoRecurso::where('proyecto_id', $proyecto_id)
                        ->whereNull('parent_id')
                        ->pluck('id')
                        ->implode(', ');
                    $allRubros = ProyectoRecurso::where('proyecto_id', $proyecto_id)
                        ->pluck('id', 'nombre')
                        ->toArray();
                    \Log::error("No encontró padre: IDs={$idsDisponibles}, All={" . json_encode($allRubros) . "}");
                    return "✗ No encontré rubro ID {$parent_id} en este proyecto. IDs disponibles: {$idsDisponibles}";
                }

                // Validar que no exista ya un subrubro con ese nombre en el padre
                $existe = ProyectoRecurso::where('proyecto_id', $proyecto_id)
                    ->where('parent_id', $parent_id)
                    ->whereRaw('LOWER(nombre) = ?', [strtolower($accion['nombre'])])
                    ->first();
                
                if ($existe) {
                    return "✗ El subrubro '{$existe->nombre}' ya existe en {$padre->nombre}.";
                }

                try {
                    $sub = ProyectoRecurso::create([
                        'proyecto_id' => $proyecto_id,
                        'parent_id'   => $parent_id,
                        'recurso_id'  => null,
                        'nombre'      => $accion['nombre'],
                        'unidad'      => $accion['unidad'] ?? 'gl',
                        'cantidad'    => 1,
                        'precio_usd'  => 0,
                        'categoria'   => $padre->categoria,
                    ]);
                    \Log::info("Subrubro creado: id={$sub->id}, nombre={$sub->nombre}");
                    $this->dispatch('presupuesto-actualizado');
                    return "✓ Sub-rubro '{$sub->nombre}' (ID {$sub->id}) creado en '{$padre->nombre}'.";
                } catch (\Exception $e) {
                    \Log::error("Error crear subrubro: " . $e->getMessage());
                    return "✗ Error al crear subrubro: " . $e->getMessage();
                }

            case 'agregar_recurso':
                $recurso = Recurso::find($accion['recurso_id']);
                $padre   = ProyectoRecurso::find($accion['parent_id']);
                if (!$recurso || !$padre) return "✗ Recurso o rubro no encontrado.";
                $this->proyecto->proyectoRecursos()->create([
                    'parent_id'  => $accion['parent_id'],
                    'recurso_id' => $recurso->id,
                    'nombre'     => $recurso->nombre,
                    'unidad'     => $recurso->unidad,
                    'cantidad'   => $accion['cantidad'] ?? 1,
                    'precio_usd' => $recurso->precio_usd ?? 0,
                    'categoria'  => $padre->categoria,
                ]);
                $this->dispatch('presupuesto-actualizado');
                return "✓ Recurso {$recurso->nombre} agregado a {$padre->nombre}.";

            case 'modificar_item':
                $item = ProyectoRecurso::find($accion['id']);
                if (!$item) return "✗ Ítem no encontrado.";
                $cambios = [];
                if (isset($accion['cantidad']))   $cambios['cantidad']   = $accion['cantidad'];
                if (isset($accion['precio_usd'])) $cambios['precio_usd'] = $accion['precio_usd'];
                $item->update($cambios);
                $this->dispatch('presupuesto-actualizado');
                return "✓ {$item->nombre} actualizado.";

            case 'responder':
                return $accion['mensaje'] ?? '';

            default:
                return '';
        }
    }

    public function render()
    {
        return view('livewire.proyecto.chatbot-rubi');
    }
}