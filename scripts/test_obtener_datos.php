<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Proyecto;
use App\Livewire\Proyecto\PresupuestoDetallado;

$proyecto = Proyecto::find(3);
if (!$proyecto) {
    echo "Proyecto 3 no encontrado\n";
    exit(0);
}

$comp = new PresupuestoDetallado();
$comp->proyecto = $proyecto;

$ref = new ReflectionClass($comp);
$method = $ref->getMethod('obtenerDatosPresupuesto');
$method->setAccessible(true);

$datos = $method->invokeArgs($comp, ['completo']);

echo "Total calculado por obtenerDatosPresupuesto: " . ($datos['total'] ?? 0) . "\n";

foreach ($datos['cat_subtotales'] as $cat => $val) {
    echo "Cat: $cat => " . number_format($val, 2, ',', '.') . "\n";
}

exit(0);
