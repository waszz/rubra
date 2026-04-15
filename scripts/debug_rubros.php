<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// List all projects and their top-level nodes
$projects = App\Models\Proyecto::all(['id','nombre']);
foreach ($projects as $p) {
    $roots = App\Models\ProyectoRecurso::where('proyecto_id', $p->id)->whereNull('parent_id')->pluck('nombre')->toArray();
    echo "ID={$p->id} [{$p->nombre}]: " . implode(', ', $roots) . "\n";
}
echo "\n";

$proyectoId = App\Models\ProyectoRecurso::select('proyecto_id')->whereNull('parent_id')->where('nombre','like','%cielorraso%')->first()?->proyecto_id
    ?? App\Models\ProyectoRecurso::select('proyecto_id')->whereNull('parent_id')->first()->proyecto_id;
echo "Debugging Proyecto ID: $proyectoId\n\n";

// Simulate sumarSubtotalNodos
function sumarSubtotalNodos($nodos, float $multiplier, int $depth = 0): float {
    $total = 0;
    foreach ($nodos as $nodo) {
        $tieneHijos = $nodo->hijos && $nodo->hijos->count() > 0;
        $cantNodo   = ($nodo->cantidad ?? 1) * $multiplier;
        $pad = str_repeat('  ', $depth);

        if (is_null($nodo->recurso_id)) {
            $precioPropio = (float)($nodo->precio_usd ?? 0);
            if ($precioPropio > 0 && !$tieneHijos) {
                echo $pad . "+ (no-rc leaf) {$nodo->nombre} precio={$precioPropio} cant={$cantNodo}\n";
                $total += $precioPropio * $cantNodo;
            }
            if ($tieneHijos) {
                echo $pad . "> (subrubro) {$nodo->nombre} hijos={$nodo->hijos->count()} cant={$cantNodo}\n";
                $total += sumarSubtotalNodos($nodo->hijos, $cantNodo, $depth+1);
            }
        } else {
            $sub = ($nodo->precio_usd ?? 0) * ($nodo->cantidad ?? 0) * $multiplier;
            echo $pad . "+ (rc={$nodo->recurso_id}) {$nodo->nombre} precio={$nodo->precio_usd} cant={$nodo->cantidad} mult={$multiplier} = {$sub}\n";
            $total += $sub;
        }
    }
    return $total;
}

$nodes = App\Models\ProyectoRecurso::where('proyecto_id', $proyectoId)
    ->whereNull('parent_id')
    ->with(['hijos', 'hijos.recurso', 'hijos.hijos', 'hijos.hijos.recurso', 'hijos.hijos.hijos', 'hijos.hijos.hijos.recurso'])
    ->limit(2)
    ->get();

foreach ($nodes as $n) {
    echo "\n=== {$n->nombre} ===\n";
    $total = sumarSubtotalNodos($n->hijos ?? collect(), 1);
    echo "TOTAL: {$total}\n";
}
