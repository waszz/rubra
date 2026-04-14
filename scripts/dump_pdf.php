<?php
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel minimally
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Use the same parser logic as _parsePDF by calling it via reflection
$proyecto = \App\Models\Proyecto::first();
if (!$proyecto) {
    echo "No projects found\n";
    exit(1);
}

$component = new \App\Livewire\Proyecto\PresupuestoDetallado();
$ref = new ReflectionClass($component);
$prop = $ref->getProperty('proyecto');
$prop->setAccessible(true);
$prop->setValue($component, $proyecto);

$method = $ref->getMethod('_parsePDF');
$method->setAccessible(true);

$result = $method->invoke($component, 'C:/Users/TERA/Desktop/presupuesto-VIVIENDA-01-14-04-2026.pdf');

echo "Beneficio detectado: " . $result['beneficio'] . "%" . PHP_EOL;
echo "Precio final: " . $result['preciofinal'] . PHP_EOL;
echo "Items parseados: " . count($result['items']) . PHP_EOL . PHP_EOL;

$lastCat = '';
$lastSub = '';
foreach ($result['items'] as $item) {
    switch ($item['tipo']) {
        case 'categoria':
            echo "=== CATEGORIA: {$item['nombre']} ===" . PHP_EOL;
            $lastCat = $item['nombre'];
            $lastSub = '';
            break;
        case 'subrubro':
            echo "  -- SUBRUBRO: {$item['nombre']} ({$item['unidad']} x{$item['cantidad']}) @ \${$item['precio']}" . PHP_EOL;
            $lastSub = $item['nombre'];
            break;
        case 'recurso':
            echo "       * {$item['nombre']} ({$item['unidad']} x{$item['cantidad']}) @ \${$item['precio']}" . PHP_EOL;
            break;
    }
}
