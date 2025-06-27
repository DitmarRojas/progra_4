<?php

namespace App\Livewire;

use App\Models\Cuenta;
use App\Models\CuentasOrgs;
use App\Models\Organizacion;
use Livewire\Component;
use Livewire\WithPagination;

class Cuentas extends Component
{

    use WithPagination;
    
    public $cuenta_id;
    public string $codigo = '';
    public string $nombre = '';
    public string $tipo = '';
    public string $descripcion = '';
    public $nivel = 0;
    public bool $estado = true;
    public $buscar = '';
    public $contenido = 'nombre';
    public $orden = 'asc';

    protected $listeners = ['eliminarCuentaSeleccionada'];
    public $organizacion_id = '';
    public $buscarOrg = '';
    public array $seleccionados = [];

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
                'codigo' => 'required|string|max:10|unique:cuentas,codigo,' . $this->cuenta_id,
                'nombre' => 'required|string|max:100',
                'tipo' => 'required|string|max:50',
                'descripcion' => 'nullable|string|max:255',
                'nivel' => 'required|numeric|min:1|max:5',
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
        $this->modal('crearCuenta')->close();
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
                'codigo' => 'required|string|max:10|unique:cuentas,codigo,' . $this->cuenta_id,
                'nombre' => 'required|string|max:100',
                'tipo' => 'required|string|max:50',
                'descripcion' => 'nullable|string|max:255',
                'nivel' => 'required|numeric|min:1|max:5',
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
        $this->modal('editarCuenta')->close();
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
        $this->modal('eliminarCuenta')->close();
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
            'seleccionados'
        ]);
    }

    public function updated($propertyName):void
    {
        $this->validateOnly($propertyName, [
            'codigo' => 'string|max:10|unique:cuentas,codigo,' . $this->cuenta_id,
            'tipo' => 'required|string|max:50',
            'nivel' => 'integer|numeric|min:1|max:5',
        ]);
    }

    public function updatedSeleccionados($value)
    {
        $nuevoId = end($this->seleccionados);

        if (!$nuevoId) return;

        $existe = CuentasOrgs::where('organizacion_id', $this->organizacion_id)
            ->where('cuenta_id', $nuevoId)
            ->exists();

        if ($existe) {
            $this->seleccionados = array_filter($this->seleccionados, fn($id) => $id != $nuevoId);
            session()->flash('error', 'La cuenta ya está asociada a la organización.');
            $this->dispatch('error');
        }
    }

    public function asociarCuentas():void
    {
        $this->validate([
            'organizacion_id' => 'required|exists:organizaciones,id',
        ]);

        foreach ($this->seleccionados as $cuentaId) {
            CuentasOrgs::firstOrCreate([
                'cuenta_id' => $cuentaId,
                'organizacion_id' => $this->organizacion_id,
            ]);
        }
        $this->dispatch('alertaCuenta');
        session()->flash('message', 'Cuentas asociadas exitosamente.');
        $this->modal('crearCuentasOrg')->close();
    }

    public function eliminarCuentaSeleccionada($cuentaId)
    {
        $this->seleccionados = array_values(array_filter($this->seleccionados, fn($id) => $id != $cuentaId));
    }

    public function updatedBuscarOrg($value)
    {
        $org = Organizacion::where('nit', $value)->first();
        $this->organizacion_id = $org ? $org->id : '';
    }

    public function updatedOrganizacionId($value)
    {
        $this->seleccionados = [];

        $org = Organizacion::find($value);
        $this->buscarOrg = $org ? $org->nit : '';
    }

    public function render()
    {
        $cuentasSeleccionadas = [];
        if (!empty($this->seleccionados)) {
            $cuentasSeleccionadas = Cuenta::select('id', 'codigo', 'nombre')
            ->whereIn('id', $this->seleccionados)
            ->get();
        }

        $organizaciones = Organizacion::select('id', 'nombre')->get();

        $organizacion = null;
        if ($this->organizacion_id) {
            $organizacion = Organizacion::find($this->organizacion_id);
        }

        $cuentas = Cuenta::where(function($query) {
            $query->where('codigo', 'like', '%' . $this->buscar . '%')
                ->orWhere('nombre', 'like', '%' . $this->buscar . '%');
        })
        ->orderBy($this->contenido, $this->orden)
        ->paginate(5)
        ->withQueryString();

        return view('livewire.cuentas', [
            'cuentas' => $cuentas,
            'organizaciones' => $organizaciones,
            'organizacion' => $organizacion,
            'cuentasSeleccionadas' => $cuentasSeleccionadas,
        ]);
    }
}
