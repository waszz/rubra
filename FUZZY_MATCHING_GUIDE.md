# 🚀 Mejoras de Fuzzy Matching para Chatbot RUBÍ

## 📊 Comparación: Antes vs Después

### ANTES (Búsqueda Exacta)
```
Usuario: "Necesito cables 2.5 que sean gruesos"
↓
Sistema: Busca "cables" en lista
↓
Resultado: ❌ No encuentra nada o sugerencia genérica
```

### DESPUÉS (Fuzzy Matching)
```
Usuario: "Necesito cables 2.5 que sean gruesos"
↓
FuzzyMatcher detecta: "cables" + "2.5"
↓
Busca en recursos: 
  - Cable Unipolar 2.5mm (90% match) ✅
  - Cable Unipolar 1.5mm (45% match) 
↓
Pasa al LLM en system prompt: 
  "BÚSQUEDA FUZZY 'cables' → Cable Unipolar 2.5mm (90%)"
↓
LLM agrega recurso correcto con ID 1
```

## 🔍 Cómo Funciona la Búsqueda Fuzzy

### 1. Detección de Palabras Clave
```php
$palabrasClaveComunes = [
    'cable', 'llave', 'tomacorriente',
    'tubo', 'ladrillo', 'cemento', ...
];

// Si el usuario escribe: "cables 2.5mm", 
// se detecta: ['cable', '2.5']
```

### 2. Búsqueda Levenshtein
```
Levenshtein Distance = número mínimo de ediciones
para transformar una string en otra

Ejemplo:
- "cables" → "Cable" = 1 edición (case)
- "thermica" → "Térmica" = 2 ediciones (accent, case)
- Distancia baja = mejor match
```

### 3. Ranking de Resultados
```
Score = (Similaridad) × (Cobertura de palabras)

"cables 2.5" vs recursos:
1. Cable Unipolar 2.5mm    → "cable" ✓ + "2.5" ✓   = 90%
2. Cable Unipolar 1.5mm    → "cable" ✓ + "2.5" ✗   = 45%
3. Llave Térmica 16A       → "cable" ✗ + "2.5" ✗   = 0%
```

## 📦 Estructura de Archivos

```
app/
├── Utils/
│   └── FuzzyMatcher.php          (NEW - 150 líneas)
├── Livewire/Proyecto/
│   └── ChatbotRubi.php           (MODIFIED - +60 líneas)
└── Models/
    └── Recurso.php               (sin cambios)

test_fuzzy_matcher.php             (NEW - Demo/Ejemplos)
```

## 🎯 Métodos Principales

### `FuzzyMatcher::search()`
```php
$matches = FuzzyMatcher::search(
    query: 'cables 2.5',
    items: $recursos,
    maxResults: 5,
    minSimilarity: 0.3
);

// Retorna:
// [
//   ['item' => [...], 'similarity' => 0.90, 'distance' => 11],
//   ['item' => [...], 'similarity' => 0.45, 'distance' => 12]
// ]
```

### `FuzzyMatcher::findBest()`
```php
$best = FuzzyMatcher::findBest('thermica', $recursos);
// Retorna el match con highest similarity
// Ideal para encontrar un solo recurso
```

### `FuzzyMatcher::searchMultiWord()`
```php
$matches = FuzzyMatcher::searchMultiWord(
    'tomacorriente doble',
    $recursos
);
// Busca resources que contengan TODAS las palabras
// Ordena por posición temprana (match mejor en el nombre)
```

## 📈 Casos de Uso

| Búsqueda | Tipo | Resultado |
|----------|------|-----------|
| "cables 2.5" | Exacto + número | Cable Unipolar 2.5mm |
| "cabels" | Typo | Cable Unipolar (después de fallback) |
| "llave termica" | Sin acento | Llave Térmica 16A |
| "thermica 20" | Similar + número | Llave Térmica 16A |
| "tomacorriente" | Exact word | Tomacorriente Doble |
| "ladrillos" | Plural | Ladrillo Visto 18x9cm |

## 🔧 Configuración

```php
// En ChatbotRubi.php, método generarSugerenciasFuzzy():

$minSimilarity = 0.3;  // Umbral mínimo (30%)
$maxResults = 3;       // Máximo matches por palabra clave

// Aumenta minSimilarity para ser más estricto (menos falsos positivos)
// Disminuye para ser más permisivo (más tolerante a typos)
```

## 📋 Palabras Clave Monitoreadas

Por defecto, el sistema busca estas palabras:
```
Eléctricos: cable, cables, llave, llaves, termica, térmicas, tomacorriente, toma, corriente, luz
Conducción: tuberia, caño, tubo
Construcción: hierro, acero, hormigon, ladrillo, arena, cemento, pintura
Plomería: valvula, ducha, baño, pileta
```

Puedes agregar más en `generarSugerenciasFuzzy()`:
```php
$palabrasClaveComunes = [
    // Agregar aquí nuevas palabras
    'fusible', 'breaker', 'canaleta', ...
];
```

## 🧪 Testing

Ejecutar pruebas:
```bash
cd /path/to/rubra
php test_fuzzy_matcher.php
```

Salida esperada:
```
✅ EJEMPLO 1: "cables 2.5" → Cable Unipolar 2.5mm (90%)
✅ EJEMPLO 2: "llave térmica 16" → Llave Térmica 16A (90%)
✅ EJEMPLO 6: "ladrillos" → Ladrillo Visto 18x9cm (90%)
```

## 📋 Integración en el System Prompt

El `$systemPrompt` ahora incluye una sección:
```
════════════════════════════════════════════════════════════════
🔍 BÚSQUEDA FUZZY (análisis inteligente del input del usuario)
════════════════════════════════════════════════════════════════
BUSQUEDA 'cables' → ID 1: Cable Unipolar 2.5mm (90% match)
BUSQUEDA 'cables' → ID 2: Cable Unipolar 1.5mm (45% match)
```

El LLM recibe estas sugerencias y las usa para:
1. Confirmar qué recurso agregar
2. Entender mejor la intención del usuario
3. Sugerir cantidad/unidad correcta

## 🚀 Ventajas

✅ **Tolerancia a typos**: "thermica" → "Térmica"  
✅ **Búsqueda parcial**: "2.5" en "2.5mm"  
✅ **Sin dependencias externas**: Usa PHP built-in `levenshtein()`  
✅ **Rápido**: O(n*m) complexity, manejable para 50-100 recursos  
✅ **Multiidioma**: Funciona con acentos español  
✅ **Flexible**: Ajusta `minSimilarity` y `maxResults` según necesidad  

## ⚠️ Consideraciones

- Para búsquedas muy grandes (1000+ recursos), considera caching
- El algoritmo Levenshtein es sensible a mayúsculas (se normaliza a minúsculas)
- Los acentos se consideran diferencias (OK para español)
- Mejor para búsquedas de 1-3 palabras que consultas complejas

## 📚 Referencias

- [PHP levenshtein() documentation](https://www.php.net/manual/en/function.levenshtein.php)
- [Levenshtein distance](https://en.wikipedia.org/wiki/Levenshtein_distance)
- [Fuzzy string matching](https://en.wikipedia.org/wiki/Approximate_string_matching)
