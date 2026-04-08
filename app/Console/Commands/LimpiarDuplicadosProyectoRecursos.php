<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProyectoRecurso;
use Illuminate\Support\Facades\DB;

class LimpiarDuplicadosProyectoRecursos extends Command
{
    protected $signature = 'proyecto:limpiar-duplicados';
    protected $description = 'Elimina registros duplicados en proyecto_recursos (rubros con la misma categoría y proyecto_id)';

    public function handle()
    {
        $this->info('🧹 Iniciando limpieza de registros duplicados...');

        // Obtener todos los proyectos
        $proyectos = DB::table('proyectos')->pluck('id');
        $deletedCount = 0;

        foreach ($proyectos as $proyectoId) {
            // Para cada proyecto, obtener rubros PRINCIPALES (parent_id = null) agrupados por categoría
            $rubros = ProyectoRecurso::where('proyecto_id', $proyectoId)
                ->whereNull('parent_id')
                ->orderBy('created_at')
                ->get()
                ->groupBy('categoria');

            // Si hay duplicados en alguna categoría, mantener el primero y borrar los demás
            foreach ($rubros as $categoria => $items) {
                if (count($items) > 1) {
                    $this->warn("  Proyecto ID {$proyectoId}: '{$categoria}' tiene " . count($items) . " registros");
                    
                    // Mantener el primero (más antiguo), borrar los demás
                    $primera = $items->first();
                    foreach ($items->slice(1) as $duplicate) {
                        $this->line("    ❌ Eliminando ID {$duplicate->id} (creado: {$duplicate->created_at})");
                        $duplicate->delete();
                        $deletedCount++;
                    }
                }
            }
        }

        if ($deletedCount === 0) {
            $this->info('✅ No se encontraron registros duplicados.');
        } else {
            $this->info("✅ Se eliminaron {$deletedCount} registros duplicados.");
        }
    }
}
