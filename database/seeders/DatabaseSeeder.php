<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ComposicionesSeeder;
use Database\Seeders\PermisosRolesSeeder;
use Database\Seeders\RecursoSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario de prueba (opcional)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seeders
        $this->call([
            RecursoSeeder::class,
            ComposicionesSeeder::class,
            PermisosRolesSeeder::class,
        ]);
    }
}