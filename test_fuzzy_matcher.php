<?php
/**
 * Prueba de Fuzzy Matching para el chatbot RUBÍ
 * 
 * Este archivo demuestra cómo funciona el FuzzyMatcher
 * con ejemplos de búsqueda de recursos eléctricos y construcción
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Utils\FuzzyMatcher;

// Simulamos una lista de recursos como si vinieran de la BD
$recursosEjemplo = [
    ['id' => 1, 'nombre' => 'Cable Unipolar 2.5mm', 'tipo' => 'Eléctrico', 'precio_usd' => 0.50],
    ['id' => 2, 'nombre' => 'Cable Unipolar 1.5mm', 'tipo' => 'Eléctrico', 'precio_usd' => 0.35],
    ['id' => 3, 'nombre' => 'Llave Térmica 16A', 'tipo' => 'Eléctrico', 'precio_usd' => 5.00],
    ['id' => 4, 'nombre' => 'Llave de Luz (Punto)', 'tipo' => 'Eléctrico', 'precio_usd' => 2.50],
    ['id' => 5, 'nombre' => 'Tomacorriente Doble', 'tipo' => 'Eléctrico', 'precio_usd' => 3.00],
    ['id' => 6, 'nombre' => 'Tubo Conduit 20mm', 'tipo' => 'Conducción', 'precio_usd' => 1.20],
    ['id' => 7, 'nombre' => 'Caja de Paso Eléctrica', 'tipo' => 'Accesorios', 'precio_usd' => 2.00],
    ['id' => 8, 'nombre' => 'Ladrillo Visto 18x9cm', 'tipo' => 'Construcción', 'precio_usd' => 0.45],
    ['id' => 9, 'nombre' => 'Cemento Portland 50kg', 'tipo' => 'Construcción', 'precio_usd' => 8.50],
    ['id' => 10, 'nombre' => 'Arena Fina 25kg', 'tipo' => 'Construcción', 'precio_usd' => 5.00],
];

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "🔍 DEMOSTRACIÓN DE FUZZY MATCHING PARA RUBÍ\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Ejemplo 1: Búsqueda de cables
echo "📌 EJEMPLO 1: Buscar 'cables 2.5'\n";
echo "───────────────────────────────────────────────────────────────\n";
$query = 'cables 2.5';
$resultados = FuzzyMatcher::search($query, $recursosEjemplo, 3, 0.3);

echo "Query: '$query'\n";
echo "Resultados encontrados: " . count($resultados) . "\n\n";

foreach ($resultados as $idx => $result) {
    $item = $result['item'];
    echo ($idx + 1) . ". {$item['nombre']}\n";
    echo "   ID: {$item['id']} | Similaridad: " . round($result['similarity'] * 100) . "% | Distancia: {$result['distance']}\n";
}

// Ejemplo 2: Búsqueda de llaves térmicas
echo "\n\n📌 EJEMPLO 2: Buscar 'llave térmica 16'\n";
echo "───────────────────────────────────────────────────────────────\n";
$query2 = 'llave térmica 16';
$resultados2 = FuzzyMatcher::search($query2, $recursosEjemplo, 3, 0.3);

echo "Query: '$query2'\n";
echo "Resultados encontrados: " . count($resultados2) . "\n\n";

foreach ($resultados2 as $idx => $result) {
    $item = $result['item'];
    echo ($idx + 1) . ". {$item['nombre']}\n";
    echo "   ID: {$item['id']} | Similaridad: " . round($result['similarity'] * 100) . "% | Distancia: {$result['distance']}\n";
}

// Ejemplo 3:búsqueda multipalabra
echo "\n\n📌 EJEMPLO 3: Buscar múltiples palabras 'tomacorriente doble'\n";
echo "───────────────────────────────────────────────────────────────\n";
$query3 = 'tomacorriente doble';
$resultados3 = FuzzyMatcher::searchMultiWord($query3, $recursosEjemplo);

echo "Query: '$query3'\n";
echo "Resultados encontrados: " . count($resultados3) . "\n\n";

foreach ($resultados3 as $idx => $result) {
    $item = $result['item'];
    echo ($idx + 1) . ". {$item['nombre']}\n";
    echo "   ID: {$item['id']} | Score: " . round($result['score'] * 100) . "%\n";
}

// Ejemplo 4: búsqueda con palabra no exacta (typo)
echo "\n\n📌 EJEMPLO 4: Buscar 'cabels' (typo de 'cables')\n";
echo "───────────────────────────────────────────────────────────────\n";
$query4 = 'cabels';
$resultados4 = FuzzyMatcher::search($query4, $recursosEjemplo, 3, 0.25);

echo "Query: '$query4'\n";
echo "Resultados encontrados: " . count($resultados4) . "\n\n";

foreach ($resultados4 as $idx => $result) {
    $item = $result['item'];
    echo ($idx + 1) . ". {$item['nombre']}\n";
    echo "   ID: {$item['id']} | Similaridad: " . round($result['similarity'] * 100) . "% | Distancia: {$result['distance']}\n";
}

// Ejemplo 5: findBest - encontrar el mejor match
echo "\n\n📌 EJEMPLO 5: Mejor match para 'thermica'\n";
echo "───────────────────────────────────────────────────────────────\n";
$query5 = 'thermica';
$best = FuzzyMatcher::findBest($query5, $recursosEjemplo, 0.25);

echo "Query: '$query5'\n";
if ($best) {
    $item = $best['item'];
    echo "✓ Mejor match: {$item['nombre']}\n";
    echo "  ID: {$item['id']} | Similaridad: " . round($best['similarity'] * 100) . "%\n";
} else {
    echo "✗ No se encontró coincidencia\n";
}

// Ejemplo 6: Construcción
echo "\n\n📌 EJEMPLO 6: Buscar 'ladrillos'\n";
echo "───────────────────────────────────────────────────────────────\n";
$query6 = 'ladrillos';
$resultados6 = FuzzyMatcher::search($query6, $recursosEjemplo, 3, 0.3);

echo "Query: '$query6'\n";
echo "Resultados encontrados: " . count($resultados6) . "\n\n";

foreach ($resultados6 as $idx => $result) {
    $item = $result['item'];
    echo ($idx + 1) . ". {$item['nombre']}\n";
    echo "   ID: {$item['id']} | Similaridad: " . round($result['similarity'] * 100) . "% | Distancia: {$result['distance']}\n";
}

echo "\n\n═══════════════════════════════════════════════════════════════\n";
echo "✅ Demostración completada\n";
echo "═══════════════════════════════════════════════════════════════\n";
