<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        $usuario = Auth::user();

        Auth::guard('web')->logout();

        if($usuario) {
            $usuario->estado = 'Inactivo';
            $usuario->save();
        }

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}
