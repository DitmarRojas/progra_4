<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::create([
            'nombre' => 'Contador General',
            'descripcion' => 'Rol con todos los permisos',
        ]);

        Rol::create([
            'nombre' => 'Contador',
            'descripcion' => 'Rol con permisos limitados',
        ]);
    }
}
