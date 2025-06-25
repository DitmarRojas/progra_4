<?php

namespace App\Livewire;

use App\Models\Cuenta;
use Livewire\Component;

class Cuentas extends Component
{
    public $cuenta_id;
    public string $codigo = '';
    public string $nombre = '';
    public string $tipo = '';
    public string $descripcion = '';
    public $nivel = 0;
    public bool $estado = true;
    public $buscar = '';
    public $contenido = 'codigo';
    public $orden = 'asc';

    public function crearModal(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->vaciarFormulario();
        $this->cuenta_id = null;
    }

    public function crear():void
    {
            $this->validate([
                'codigo' => 'required|string|max:50|unique:cuentas,codigo,' . $this->cuenta_id,
                'nombre' => 'required|string|max:100',
                'tipo' => 'required|string|max:50',
                'descripcion' => 'nullable|string|max:255',
                'nivel' => 'required|integer|min:0',
            ]);
    
            Cuenta::create([
                'codigo' => $this->codigo,
                'nombre' => $this->nombre,
                'tipo' => $this->tipo,
                'descripcion' => $this->descripcion,
                'nivel' => $this->nivel,
            ]);
        $this->dispatch('alertaCuenta');
        session()->flash('message', 'Cuenta creada exitosamente.');
        $this->vaciarFormulario();
    }

    public function editarModal($id):void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->cuenta_id = $id;
        $cuenta = Cuenta::find($id);
        if ($cuenta) {
            $this->codigo = $cuenta->codigo;
            $this->nombre = $cuenta->nombre;
            $this->tipo = $cuenta->tipo;
            $this->descripcion = $cuenta->descripcion;
            $this->nivel = $cuenta->nivel;
        }
    }

    public function editar():void
    {
            $this->validate([
                'codigo' => 'required|string|max:50|unique:cuentas,codigo,' . $this->cuenta_id,
                'nombre' => 'required|string|max:100',
                'tipo' => 'required|string|max:50',
                'descripcion' => 'nullable|string|max:255',
                'nivel' => 'required|integer|min:0',
            ]);
    
            $cuenta = Cuenta::find($this->cuenta_id);
            if ($cuenta) {
                $cuenta->update([
                    'codigo' => $this->codigo,
                    'nombre' => $this->nombre,
                    'tipo' => $this->tipo,
                    'descripcion' => $this->descripcion,
                    'nivel' => $this->nivel,
                ]);
            }
        $this->dispatch('alertaCuenta');
        session()->flash('message', 'Cuenta modificada exitosamente.');
        $this->vaciarFormulario();
    }
    
    public function eliminarModal($id): void
    {
        $cuenta = Cuenta::find($id);
        if ($cuenta) {
            $this->cuenta_id = $id;
            $this->codigo = $cuenta->codigo;
            $this->nombre = $cuenta->nombre;
        }
    }
    public function eliminar(): void
    {
        $cuenta = Cuenta::find($this->cuenta_id);
        if ($cuenta) {
            $cuenta->delete();
            $this->dispatch('alertaCuenta');
            session()->flash('message', 'Cuenta eliminada exitosamente.');
        }
        $this->vaciarFormulario();
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

    public function vaciarFormulario(): void
    {
        $this->reset([
            'cuenta_id',
            'codigo',
            'nombre',
            'tipo',
            'descripcion',
            'nivel',
        ]);
    }

    public function updated($propertyName):void
    {
        $this->validateOnly($propertyName, [
            'codigo' => 'string|max:10|unique:cuentas,codigo,' . $this->cuenta_id,
            'tipo' => 'required|string|max:50',
            'nivel' => 'integer|min:0|max:10|numeric',
        ]);
    }
    public function render()
    {
        $cuentas = Cuenta::where('codigo', 'like', '%' . $this->buscar . '%')
            ->orWhere('nombre', 'like', '%' . $this->buscar . '%')
            ->orderBy($this->contenido, $this->orden)
            ->paginate(5);
        return view('livewire.cuentas',[
            'cuentas' => $cuentas
        ]);
    }
}
