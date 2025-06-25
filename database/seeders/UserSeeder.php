<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Usuario::create([
            'nombres' => 'Arleth',
            'apellidos' => 'Ricaldez',
            'telefono' => '78945612',
            'rol_id' => 1,
            'email' => 'arleth.ricaldez@prime.com',
            'username' => 'ale.atitag',
            'password' => bcrypt('123456789'),
        ]);
        Usuario::create([
            'nombres' => 'fulanito',
            'apellidos' => 'prueba',
            'telefono' => '78541254',
            'rol_id' => 2,
            'email' => 'fulanito@prime.com',
            'username' => 'fulanito123',
            'password' => bcrypt('123456789'),
        ]);
    }
}
