<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Recurso;
use App\Models\ProyectoRecurso;

$nombre = 'Hormigón Pobre H8 (m3)';
$r = Recurso::where('nombre', $nombre)->with('items.recursoBase')->first();

if (!$r) {
    echo "NOT_FOUND\n";
    exit(0);
}

echo "RECURSO_ID: " . $r->id . "\n";
echo "RECURSO_TIPO: " . ($r->tipo ?? '') . "\n";
echo "ITEMS_COUNT: " . ($r->items->count()) . "\n";
foreach ($r->items as $it) {
    $base = $it->recursoBase;
    echo "ITEM_ID: {$it->id} | nombre: {$it->nombre} | cantidad: {$it->cantidad} | recurso_id: {$it->recurso_id} | composicion_id: {$it->composicion_id}\n";
    if ($base) {
        echo " -> BASE: {$base->id} | {$base->nombre} | tipo: {$base->tipo} | precio_usd: {$base->precio_usd}\n";
    } else {
        echo " -> BASE: null\n";
    }
}

$prs = ProyectoRecurso::where('recurso_id', $r->id)->get();
echo "PROYECTO_RECURSOS_COUNT: " . $prs->count() . "\n";
foreach ($prs as $p) {
    echo "PR_ID: {$p->id} | proyecto_id: {$p->proyecto_id} | parent_id: {$p->parent_id} | nombre: {$p->nombre} | cantidad: {$p->cantidad}\n";
}
