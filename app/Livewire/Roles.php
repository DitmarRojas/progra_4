<?php

namespace App\Livewire;

use App\Models\Rol;
use Livewire\Component;
use Livewire\WithPagination;

class Roles extends Component
{
    use WithPagination;

    public $rol_id;
    public $nombre = '';
    public $descripcion = '';
    public $buscar = '';
    public $contenido = 'nombre';
    public $orden = 'asc';

    public function modalCrear(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function crear():void
    {
        $this->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre,' . $this->rol_id,
            'descripcion' => 'nullable|string|max:255',
        ]);
        
        $rol = Rol::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ]);

        $this->dispatch('guardado');
        session()->flash('mensaje', 'Rol ' . $rol->nombre . ' creado exitosamente.');
        $this->vaciarFormulario();
        $this->modal('registrarModal')->close();
    }

    public function modalEditar($id):void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->rol_id = $id;
        $rol = Rol::find($id);
        if ($rol) {
            $this->nombre = $rol->nombre;
            $this->descripcion = $rol->descripcion;
        }
    }
    
    public function editar():void
    {
        $this->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre,' . $this->rol_id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $rol = Rol::find($this->rol_id);
        if ($rol) {
            $rol->update([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
            ]);
            $this->dispatch('guardado');
            session()->flash('mensaje', 'Rol ' . $rol->nombre . ' actualizado correctamente.');
            $this->vaciarFormulario();
            $this->modal('editarModal')->close();
        }
    }

    public function modalEliminar($id): void
    {
        $rol = Rol::find($id);
        if($rol){
            $this->rol_id = $id;
            $this->rol->nombre = $rol->nombre;
        }
    }
    public function Eliminar():void
    {
        $rol = Rol::find($this->rol_id);
        if ($rol) {
            $rol->delete();
            $this->dispatch('guardado');
            session()->flash('mensaje', 'Rol ' . $rol->nombre . ' eliminado correctamente.');
            $this->vaciarFormulario();
            $this->modal('eliminarModal')->close();
        }
    }

    public function vaciarFormulario():void
    {
        $this->rol_id = null;
        $this->nombre = '';
        $this->descripcion = '';
    }

    public function render()
    {
        return view('livewire.roles');
    }
}
