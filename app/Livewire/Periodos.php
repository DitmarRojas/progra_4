<?php

namespace App\Livewire;

use App\Models\Organizacion;
use App\Models\Periodo;
use Livewire\Component;

class Periodos extends Component
{
    public $periodo_id = '';
    public string $nombre = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public string $estado = 'Cerrado';
    public $organizacion_id = '';
    public string $buscar = '';
    public string $contenido = 'nombre';
    public string $orden = 'asc';

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
            'nombre' => 'required|string|max:255|unique:periodos,nombre',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'organizacion_id' => 'required|exists:organizaciones,id',
        ]);

        Periodo::create([
            'nombre' => $this->nombre,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'organizacion_id' => $this->organizacion_id,
        ]);
        $this->dispatch('alertaPeriodo');
        session()->flash('message', 'Periodo creado exitosamente.');
        $this->vaciarFormulario();
        $this->modal('crearPeriodo')->close();
    }

    public function editarModal($id):void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->periodo_id = $id;
        $periodo = Periodo::find($id);
        if ($periodo) {
            $this->nombre = $periodo->nombre;
            $this->fecha_inicio = $periodo->fecha_inicio;
            $this->fecha_fin = $periodo->fecha_fin;
            $this->organizacion_id = $periodo->organizacion_id;
        }
    }
    public function editar():void
    {
        $this->validate([
            'nombre' => 'required|string|max:255|unique:periodos,nombre,' . $this->periodo_id,
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'organizacion_id' => 'required|exists:organizaciones,id',
        ]);

        $periodo = Periodo::find($this->periodo_id);
        if ($periodo) {
            $periodo->update([
                'nombre' => $this->nombre,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'organizacion_id' => $this->organizacion_id,
            ]);
            $this->dispatch('alertaPeriodo');
            session()->flash('message', 'Periodo actualizado exitosamente.');
        }
        $this->vaciarFormulario();
        $this->modal('editarPeriodo')->close();
    }

    public function eliminarModal($id): void
    {
        $periodo = Periodo::find($id);
        if ($periodo) {
            $this->periodo_id = $id;
            $this->nombre = $periodo->nombre;
        }
    }
    
    public function eliminar(): void
    {
        $periodo = Periodo::find($this->periodo_id);
        if ($periodo) {
            $periodo->delete();
            $this->dispatch('alertaPeriodo');
            session()->flash('message', 'Periodo eliminado exitosamente.');
            $this->vaciarFormulario();
            $this->modal('eliminarPeriodo')->close();
        }
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName, [
            'nombre' => 'string|max:255|unique:periodos,nombre,' . $this->periodo_id,
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date|after:fecha_inicio',
            'organizacion_id' => 'exists:organizaciones,id',
        ]);
    }

    public function vaciarFormulario(): void
    {
        $this->periodo_id = null;
        $this->reset([
            'nombre',
            'fecha_inicio',
            'fecha_fin',
            'organizacion_id',
            'buscar'
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

    public function estadoPeriodo($id): void
    {
        $periodo = Periodo::find($id);
        if ($periodo && $periodo->estado !== 'Cerrado') {
            $periodo->estado = 'Cerrado';
            $periodo->save();
            $this->dispatch('alertaPeriodo');
            session()->flash('message', 'Periodo ' . $periodo->nombre . ' cerrado correctamente.');
        }
        else
        {
            if ($periodo) {
                $periodo->estado = 'Abierto';
                $periodo->save();
                $this->dispatch('alertaPeriodo');
                session()->flash('message', 'Periodo ' . $periodo->nombre . ' abierto correctamente.');
            }
        }
    }

    public function render()
    {
        $organizaciones = Organizacion::select('id', 'nombre')->get();
        $periodos = Periodo::query()
            ->when($this->buscar, function ($query) {
                $query->where('nombre', 'like', '%' . $this->buscar . '%');
            })
            ->orderBy($this->contenido, $this->orden)
            ->paginate(5);
        return view('livewire.periodos',[
            'periodos' => $periodos,
            'organizaciones' => $organizaciones,
        ]);
    }
}
