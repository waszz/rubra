<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Proyecto;
use App\Livewire\Proyecto\PresupuestoDetallado;
use App\Models\ConfiguracionGeneral;

$proyectoId = 3; // ajustá si necesitás otro proyecto
$proyecto = Proyecto::find($proyectoId);
if (!$proyecto) {
    echo "Proyecto {$proyectoId} no encontrado\n";
    exit(1);
}

$comp = new PresupuestoDetallado();
$comp->proyecto = $proyecto;

$ref = new ReflectionClass($comp);
$method = $ref->getMethod('obtenerDatosPresupuesto');
$method->setAccessible(true);
$datos = $method->invokeArgs($comp, ['completo']);

// resumen similar a exportarPDF
$pctBeneficio = (float) ($proyecto->beneficio ?? 0);
$factor = 1 + ($pctBeneficio / 100);
$subtotalBase = $datos['total'] ?? 0;
$subtotalConBeneficio = $subtotalBase * $factor;

// calcular totalGeneral replicando la lógica del template
$totalGeneralByTemplate = 0;
$foundComposition = false;
$compositionNames = [];
foreach ($datos['items'] as $item) {
    if ($item['tipo'] === 'subrubro') {
        $ownPrice = $item['precio_own'] ?? ($item['precio_usd'] ?? 0);
        $ownCon = ($ownPrice) * $factor * ($item['cantidad'] ?? 0);
        $totalGeneralByTemplate += $ownCon;
    } else {
        $totalGeneralByTemplate += ($item['subtotal'] ?? 0) * $factor;
    }
    // detectar si hay alguna "Hormigón" en items
    if (str_contains($item['nombre'] ?? '', 'Hormigón')) {
        $compositionNames[] = $item;
    }
}

echo "datos[total] (sin beneficio): " . number_format($subtotalBase, 2, ',', '.') . "\n";
echo "subtotalConBeneficio (resumen): " . number_format($subtotalConBeneficio, 2, ',', '.') . "\n";
echo "totalGeneral calculado por template: " . number_format($totalGeneralByTemplate, 2, ',', '.') . "\n";

if (count($compositionNames)) {
    echo "Items con 'Hormigón' encontrados en datos['items']: " . count($compositionNames) . "\n";
    foreach ($compositionNames as $c) {
        echo " - {$c['nombre']} | cantidad: " . ($c['cantidad'] ?? 0) . " | precio_usd: " . number_format($c['precio_usd'] ?? 0, 2, ',', '.') . " | subtotal: " . number_format($c['subtotal'] ?? 0, 2, ',', '.') . "\n";
    }
} else {
    echo "No se encontraron items con 'Hormigón' en datos['items']\n";
}

// render HTML to file for manual inspection
$config = ConfiguracionGeneral::instancia();
$viewData = [
    'titulo'      => 'DEBUG PRESUPUESTO',
    'proyecto'    => $proyecto,
    'datos'       => $datos,
    'userPlan'    => 'pro',
    'opciones'    => [
        'incluirEmailCliente' => false,
        'incluirAlcance'      => false,
        'incluirCondiciones'  => false,
        'incluirValidez'      => false,
        'incluirUnidad'       => true,
        'incluirCantidad'     => true,
        'incluirPrecio'       => true,
        'exportScope'         => 'completo',
    ],
    'alcance'      => '',
    'condiciones'  => '',
    'validez'      => '',
    'emailCliente' => '',
    'fecha'        => now()->locale('es')->translatedFormat('d \d\e F \d\e Y'),
    'fechaEmision' => now()->format('d/m/Y'),
    'config'       => $config,
    'logoBase64'   => null,
    'resumen'      => [
        'subtotal'               => $subtotalBase,
        'subtotal_con_beneficio' => $subtotalConBeneficio,
        'pct_beneficio'          => $pctBeneficio,
        'beneficio'              => $subtotalBase * ($pctBeneficio / 100),
        // usar 0 para carga_social en este debug para evitar llamar métodos privados
        'carga_social'           => 0,
        'pct_impuestos'          => (float) ($proyecto->impuestos ?? 22),
        'impuestos'              => $subtotalConBeneficio * ((float) ($proyecto->impuestos ?? 22) / 100),
        'total_obra'             => $subtotalConBeneficio + ($subtotalConBeneficio * ((float) ($proyecto->impuestos ?? 22) / 100)),
        'precio_final'           => $subtotalConBeneficio + ($subtotalConBeneficio * ((float) ($proyecto->impuestos ?? 22) / 100)),
    ],
    'monedaBase'   => $proyecto->moneda_base ?? 'USD',
    'pctBeneficio' => $pctBeneficio,
];

$html = view('exports.presupuesto-pdf', $viewData)->render();
$file = storage_path('app/temp/debug_presupuesto.html');
if (!is_dir(dirname($file))) mkdir(dirname($file), 0755, true);
file_put_contents($file, $html);

echo "HTML renderizado en: {$file}\n";

exit(0);
