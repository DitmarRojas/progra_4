<?php

namespace App\Livewire\Auth;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $apellidos = '';
    public string $telefono = '';
    public int $rol_id = 0;
    public string $email = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'nombres' => ['required', 'string', 'alpha', 'max:100'],
            'apellidos' => ['required', 'string', 'alpha', 'max:100'],
            'telefono' => ['required', 'string', 'min:8', 'max:8'],
            'username' => ['required', 'string', 'alpha_num', 'max:100', 'unique:'. Usuario::class],
            'rol_id' => ['required','exists:roles,id'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:'. Usuario::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = Usuario::create([
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'telefono' => $validated['telefono'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => $validated['password'],
            'rol_id' => $validated['rol_id'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Render the component.
     * Necesitamos este método para pasar los roles a la vista.
     */
    public function render()
    {
        return view('livewire.auth.register', [
            'roles' => Rol::all()
        ]);
    }
}