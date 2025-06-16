<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Rol; // Asegúrate de importar tu modelo Rol
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
    public $rol_id = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'alpha', 'max:255'],
            'apellidos' => ['required', 'string', 'alpha', 'max:255'],
            'telefono' => ['required', 'string', 'min:8', 'max:8'],
            'rol_id' => ['required','exists:roles,id'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Asegúrate de que los nuevos campos se incluyan en la creación del usuario
        // y que tu modelo User tenga estos campos en $fillable
        $user = User::create([
            'name' => $validated['name'],
            'apellidos' => $validated['apellidos'],
            'telefono' => $validated['telefono'],
            'email' => $validated['email'],
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