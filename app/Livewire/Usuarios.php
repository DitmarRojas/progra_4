<?php

namespace App\Livewire;

use App\Models\Rol;
use App\Models\Usuario;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithPagination;

class Usuarios extends Component
{
    use WithPagination;

    public $usuario_id;
    public $estado;
    public string $nombres = '';
    public string $apellidos = '';
    public string $telefono = '';
    public string $email = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int $rol_id = 0;
    public bool $activo = false;
    public $contenido = 'username';
    public $orden = 'asc';
    public $buscar = '';

    public function crearModal(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->vaciarFormulario();
        $this->usuario_id = null;
        $this->activo = false;
    }
public function registrarEditar(): void
{
    $reglas = [
        'nombres' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
        'apellidos' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
        'telefono' => 'nullable|numeric|digits:8',
        'email' => 'required|email|max:200|unique:usuarios,email,' . $this->usuario_id,
        'username' => 'required|string|max:50|unique:usuarios,username,' . $this->usuario_id,
        'rol_id' => 'required|exists:roles,id',
    ];

    if (!$this->usuario_id) {
        $reglas['password'] = ['required', 'string', 'min:8', 'confirmed', Password::defaults()];
    } else {
        $reglas['password'] = ['nullable', 'string', 'min:8', 'confirmed', Password::defaults()];
        $reglas['password_confirmation'] = 'nullable|required_with:password|same:password';
    }

    $this->validate($reglas);

    if($this->usuario_id) {
        $usuario = Usuario::find($this->usuario_id);
        if($usuario) {
            $usuario->update([
                'nombres' => ucfirst($this->nombres),
                'apellidos' => ucfirst($this->apellidos),
                'telefono' => $this->telefono,
                'rol_id' => $this->rol_id,
            ]);
            $this->dispatch('alertas');
            session()->flash('message', 'Usuario ' .  $usuario->nombres . ' modificado correctamente.');
            $this->modal('editarUsuario')->close();
        }
    } else {
        $usuario = Usuario::create([
            'nombres' => ucfirst($this->nombres),
            'apellidos' => ucfirst($this->apellidos),
            'telefono' => $this->telefono,
            'email' => $this->email,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'rol_id' => $this->rol_id,
        ]);
        $this->dispatch('alertas');
        session()->flash('message', 'Usuario guardado correctamente.');
        $this->modal('registrarUsuario')->close();
    }
    $this->vaciarFormulario();
}
    public function editarModal($id):void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $usuario = Usuario::find($id);
        if ($usuario) {
            $this->usuario_id = $usuario->id;
            $this->nombres = $usuario->nombres;
            $this->apellidos = $usuario->apellidos;
            $this->telefono = $usuario->telefono;
            $this->email = $usuario->email;
            $this->username = $usuario->username;
            $this->password = '';
            $this->password_confirmation = '';
            $this->rol_id = $usuario->rol_id;
            $this->activo = true;
        }
    }

public function updated($propertyName)
{
    if (in_array($propertyName, ['password', 'password_confirmation'])) {
        $this->validateOnly('password', [
            'password' => ['string', 'min:8', 'confirmed', Password::defaults()],
        ]);
        $this->validateOnly('password_confirmation', [
            'password_confirmation' => 'required_with:password|same:password',
        ]);
    } else {
    $this->validateOnly($propertyName, [
        'nombres'   => ['string', 'max:100', 'regex:/^[\pL\s]+$/u'],
        'apellidos' => ['string', 'max:100', 'regex:/^[\pL\s]+$/u'],
        'telefono' => 'numeric|digits:8',
        'email' => 'email|max:200|unique:usuarios,email,' . $this->usuario_id,
        'username' => 'string|max:50|unique:usuarios,username,' . $this->usuario_id,
        'rol_id' => 'required|exists:roles,id',
    ]);
    }
}

    public function eliminarModal($id):void
    {
        $usuario = Usuario::find($id);
        if($usuario)
        {
            $this->usuario_id = $usuario->id;
            $this->nombres = $usuario->nombres;
        }
    }

    public function eliminar (): void
    {
            $usuario = Usuario::find($this->usuario_id);
            if($usuario)
            {
                $usuario->delete();
                $this->dispatch('alertas');
                session()->flash('message', 'Se eliminó correctamente el usuario ' . $usuario->nombres . '.');
            }
            $this->modal('eliminarUsuario')->close();
            $this->usuario_id = null;
            $this->nombres = '';
            $this->resetPage();
    }

    public function vaciarFormulario(): void
    {
        $this->reset(['usuario_id', 'nombres', 'apellidos', 'telefono', 'email', 'username', 'password', 'password_confirmation', 'rol_id', 'buscar']);
    }

    public function ordenar($content):void
    {
        if($this->contenido == $content){
            if($this->orden == 'asc'){
                $this->orden = 'desc';
            } else {
                $this->orden = 'asc';
            }
        } else
        {
            $this->contenido = $content;
            $this->orden = 'desc';
        }
    }

    public function bloquearUsuario($id): void
    {
        $usuario = Usuario::find($id);
        if ($usuario && $usuario->estado !== 'Bloqueado') {
            $usuario->estado = 'Bloqueado';
            $usuario->save();
            $this->dispatch('alertas');
            session()->flash('message', 'Usuario ' . $usuario->nombres . ' bloqueado correctamente.');
            $this->resetPage();
        }
    }
    
    public function desbloquearUsuario($id): void
    {
        $usuario = Usuario::find($id);
        if ($usuario && $usuario->estado === 'Bloqueado') {
            $usuario->estado = 'Inactivo';
            $usuario->save();
            $this->dispatch('alertas');
            session()->flash('message', 'Usuario ' . $usuario->nombres . ' desbloqueado correctamente.');
            $this->resetPage();
        }
    }

    public function render()
    {
        $usuarios = Usuario::where('username', 'like', '%' . $this->buscar . '%')
            ->orWhere('nombres', 'like', '%' . $this->buscar . '%')
            ->orderBy($this->contenido, $this->orden)
            ->paginate(5);

    return view('livewire.usuarios', [ 'usuarios' => $usuarios ,
                'roles' => Rol::all()]);
    }
}
