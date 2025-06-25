<?php

namespace App\Livewire;

use App\Models\Organizacion;
use Livewire\Component;

class Organizaciones extends Component
{
    public $organizacion_id;
    public string $nombre ='';
    public string $nit = '';
    public string $direccion = '';
    public string $telefono = '';
    public $buscar = '';
    public $contenido = 'nombre';
    public $orden = 'asc';

    public function crearModal(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->vaciarFormulario();
        $this->organizacion_id = null;
    }
    public function crear():void
    {
        $this->validate([
            'nombre' => 'required|string|max:255|unique:organizaciones,nombre',
            'nit' => 'required|numeric|digits:10|unique:organizaciones,nit',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
        ]);

        Organizacion::create([
            'nombre' => $this->nombre,
            'nit' => $this->nit,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
        ]);
        $this->dispatch('alertaOrg');
        session()->flash('message', 'Organización creada exitosamente.');
        $this->vaciarFormulario();
        $this->modal('crearOrganizacion')->close();
    }

    public function editarModal($id):void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->organizacion_id = $id;
        $organizacion = Organizacion::find($id);
        if ($organizacion) {
            $this->nombre = $organizacion->nombre;
            $this->nit = $organizacion->nit;
            $this->direccion = $organizacion->direccion;
            $this->telefono = $organizacion->telefono;
        }
    }
    public function editar():void
    {
        $this->validate([
            'nombre' => 'required|string|max:255|unique:organizaciones,nombre,' . $this->organizacion_id,
            'nit' => 'required|numeric|digits:10|unique:organizaciones,nit,' . $this->organizacion_id,
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
        ]);

        $organizacion = Organizacion::find($this->organizacion_id);
        if ($organizacion) {
            $organizacion->update([
                'nombre' => $this->nombre,
                'nit' => $this->nit,
                'direccion' => $this->direccion,
                'telefono' => $this->telefono,
            ]);
            $this->dispatch('alertaOrg');
            session()->flash('message', 'Organización actualizada exitosamente.');
        }
        $this->vaciarFormulario();
        $this->modal('editarOrganizacion')->close();
    }

    public function eliminarModal($id): void
    {
        $organizacion = Organizacion::find($id);
        if ($organizacion) {
            $this->organizacion_id = $id;
            $this->nombre = $organizacion->nombre;
        }
    }
    public function eliminar(): void
    {
        $organizacion = Organizacion::find($this->organizacion_id);
        if ($organizacion) {
            $organizacion->delete();
            $this->dispatch('alertaOrg');
            session()->flash('message', 'Organización eliminada exitosamente.');
            $this->vaciarFormulario();
            $this->modal('eliminarOrganizacion')->close();
        }
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName, [
            'nombre' => 'string|max:255|unique:organizaciones,nombre,' . $this->organizacion_id,
            'nit' => 'numeric|digits:10|unique:organizaciones,nit,' . $this->organizacion_id,
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
        ]);
    }

    public function vaciarFormulario(): void
    {
        $this->organizacion_id = null;
        $this->reset([
            'organizacion_id',
            'nombre',
            'nit',
            'direccion',
            'telefono',
            'buscar',
        ]);
    }

    public function ordenar($content): void
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

    public function render()
    {
        $organizaciones = Organizacion::where('nombre', 'like', '%' . $this->buscar . '%')
            ->orWhere('nit', 'like', '%' . $this->buscar . '%')
            ->orderBy($this->contenido, $this->orden)
            ->paginate(5);
        return view('livewire.organizaciones', [
            'organizaciones' => $organizaciones,
        ]);
    }
}
