<?php

namespace App\Livewire;

use Livewire\Component;

class Usuario extends Component
{
    public string $nombre;
    public string $apellido;
    public string $telefono;
    public string $email;
    public string $password;
    public function render()
    {
        return view('livewire.usuario');
    }
}
