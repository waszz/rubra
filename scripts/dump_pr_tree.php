<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProyectoRecurso;

$proyectoId = 3;
$all = ProyectoRecurso::where('proyecto_id', $proyectoId)->orderBy('parent_id')->orderBy('orden')->get();
$map = [];
foreach ($all as $p) {
    $map[$p->id] = $p;
}
function printNode($node, $map, $level = 0) {
    $indent = str_repeat('  ', $level);
    $recursoTipo = $node->recurso?->tipo ?? 'n/a';
    echo $indent . "ID: {$node->id} | parent: {$node->parent_id} | nombre: {$node->nombre} | recurso_id: {$node->recurso_id} | recurso_tipo: {$recursoTipo} | cantidad: {$node->cantidad} | precio_usd: {$node->precio_usd}\n";
    foreach ($map as $m) {
        if ($m->parent_id == $node->id) {
            printNode($m, $map, $level+1);
        }
    }
}

// print roots
foreach ($all as $a) {
    if (is_null($a->parent_id)) {
        printNode($a, $map, 0);
    }
}
