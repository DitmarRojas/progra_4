<?php

namespace App\Livewire;

use App\Models\AsientosDiario;
use App\Models\Transaccion;
use App\Models\Cuenta;
use App\Models\Organizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AsientosDiarios extends Component
{
    use WithPagination;

    // Propiedades para filtros
    public $buscar = '';
    public $filtroTransaccion = '';
    public $filtroOrganizacion = '';
    public $filtroCuenta = '';

    // Propiedades para el formulario
    public $asiento_id;
    public $nro_asiento = '';
    public $monto_debe = 0;
    public $monto_haber = 0;
    public $descripcion = '';
    public $transaccion_id = '';
    public $cuenta_id = '';
    public $organizacion_id = '';

    // Propiedades para ordenamiento
    public $contenido = 'created_at';
    public $orden = 'desc';

    // Listeners
    protected $listeners = ['eliminarAsiento'];

    public function mount()
    {
        // Si viene un filtro de transacción por URL, aplicarlo
        if (request()->has('filtroTransaccion')) {
            $this->filtroTransaccion = request('filtroTransaccion');
            
            // Si hay una transacción, cargar automáticamente su organización
            if ($this->filtroTransaccion) {
                $transaccion = Transaccion::find($this->filtroTransaccion);
                if ($transaccion) {
                    $this->filtroOrganizacion = $transaccion->organizacion_id;
                    $this->organizacion_id = $transaccion->organizacion_id;
                }
            }
        }
    }

    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function updatingFiltroTransaccion()
    {
        $this->resetPage();
    }

    public function updatingFiltroOrganizacion()
    {
        $this->resetPage();
        // Al cambiar la organización, resetear la cuenta seleccionada
        $this->cuenta_id = '';
        $this->filtroCuenta = '';
    }

    public function updatingFiltroCuenta()
    {
        $this->resetPage();
    }

    public function updatedOrganizacionId($value)
    {
        // Cuando cambie la organización, resetear la cuenta y transacción
        $this->cuenta_id = '';
        $this->transaccion_id = '';
        
        // Forzar re-renderizado para actualizar las cuentas y transacciones disponibles
        $this->dispatch('$refresh');
    }

    public function updatedTransaccionId($value)
    {
        // Cuando se seleccione una transacción, cargar su organización automáticamente
        if ($value) {
            $transaccion = Transaccion::find($value);
            if ($transaccion) {
                $this->organizacion_id = $transaccion->organizacion_id;
            }
        }
    }

    public function ordenar($campo)
    {
        if ($this->contenido === $campo) {
            $this->orden = $this->orden === 'asc' ? 'desc' : 'asc';
        } else {
            $this->contenido = $campo;
            $this->orden = 'asc';
        }
        $this->resetPage();
    }

    public function crear()
    {
        $this->validate([
            'nro_asiento' => 'nullable|string|max:50',
            'monto_debe' => 'required|numeric|min:0',
            'monto_haber' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
            'transaccion_id' => 'required|exists:transacciones,id',
            'cuenta_id' => 'required|exists:cuentas,id',
            'organizacion_id' => 'required|exists:organizaciones,id',
        ], [
            'monto_debe.required' => 'El monto debe es requerido.',
            'monto_haber.required' => 'El monto haber es requerido.',
            'transaccion_id.required' => 'Debe seleccionar una transacción.',
            'cuenta_id.required' => 'Debe seleccionar una cuenta.',
            'organizacion_id.required' => 'Debe seleccionar una organización.',
        ]);

        // Validar que al menos uno de los montos sea mayor a 0
        if ($this->monto_debe == 0 && $this->monto_haber == 0) {
            session()->flash('error', 'Al menos uno de los montos (debe o haber) debe ser mayor a 0.');
            $this->dispatch('error');
            return;
        }

        // Validar que solo uno de los montos sea mayor a 0
        if ($this->monto_debe > 0 && $this->monto_haber > 0) {
            session()->flash('error', 'Solo puede tener valor en debe O en haber, no en ambos.');
            $this->dispatch('error');
            return;
        }

        // Verificar que la cuenta pertenezca a la organización
        if (!$this->cuentaPerteneceAOrganizacion($this->cuenta_id, $this->organizacion_id)) {
            session()->flash('error', 'La cuenta seleccionada no pertenece a la organización.');
            $this->dispatch('error');
            return;
        }

        // Verificar que la transacción no esté contabilizada
        $transaccion = Transaccion::find($this->transaccion_id);
        if ($transaccion && $transaccion->estado) {
            session()->flash('error', 'No se pueden agregar asientos a una transacción ya contabilizada.');
            $this->dispatch('error');
            return;
        }

        DB::transaction(function () {
            AsientosDiario::create([
                'nro_asiento' => $this->nro_asiento ?: $this->generarNumeroAsiento(),
                'monto_debe' => $this->monto_debe,
                'monto_haber' => $this->monto_haber,
                'descripcion' => $this->descripcion,
                'transaccion_id' => $this->transaccion_id,
                'cuenta_id' => $this->cuenta_id,
            ]);
        });

        $this->dispatch('alertaAsiento');
        session()->flash('message', 'Asiento diario creado exitosamente.');
        $this->vaciarFormulario();
    }

    public function editarModal($id)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->asiento_id = $id;
        $asiento = AsientosDiario::with(['transaccion', 'cuenta'])->find($id);
        
        if ($asiento) {
            $this->nro_asiento = $asiento->nro_asiento;
            $this->monto_debe = $asiento->monto_debe;
            $this->monto_haber = $asiento->monto_haber;
            $this->descripcion = $asiento->descripcion;
            $this->transaccion_id = $asiento->transaccion_id;
            $this->cuenta_id = $asiento->cuenta_id;
            $this->organizacion_id = $asiento->transaccion->organizacion_id ?? '';
        }
    }

    public function editar()
    {
        $asiento = AsientosDiario::with('transaccion')->find($this->asiento_id);
        
        // Verificar que la transacción no esté contabilizada
        if ($asiento && $asiento->transaccion && $asiento->transaccion->estado) {
            session()->flash('error', 'No se puede editar un asiento de una transacción ya contabilizada.');
            $this->dispatch('error');
            return;
        }

        $this->validate([
            'nro_asiento' => 'nullable|string|max:50',
            'monto_debe' => 'required|numeric|min:0',
            'monto_haber' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
            'transaccion_id' => 'required|exists:transacciones,id',
            'cuenta_id' => 'required|exists:cuentas,id',
            'organizacion_id' => 'required|exists:organizaciones,id',
        ]);

        // Validar montos
        if ($this->monto_debe == 0 && $this->monto_haber == 0) {
            session()->flash('error', 'Al menos uno de los montos (debe o haber) debe ser mayor a 0.');
            $this->dispatch('error');
            return;
        }

        if ($this->monto_debe > 0 && $this->monto_haber > 0) {
            session()->flash('error', 'Solo puede tener valor en debe O en haber, no en ambos.');
            $this->dispatch('error');
            return;
        }

        // Verificar que la cuenta pertenezca a la organización
        if (!$this->cuentaPerteneceAOrganizacion($this->cuenta_id, $this->organizacion_id)) {
            session()->flash('error', 'La cuenta seleccionada no pertenece a la organización.');
            $this->dispatch('error');
            return;
        }

        DB::transaction(function () use ($asiento) {
            if ($asiento) {
                $asiento->update([
                    'nro_asiento' => $this->nro_asiento,
                    'monto_debe' => $this->monto_debe,
                    'monto_haber' => $this->monto_haber,
                    'descripcion' => $this->descripcion,
                    'transaccion_id' => $this->transaccion_id,
                    'cuenta_id' => $this->cuenta_id,
                ]);
            }
        });

        $this->dispatch('alertaAsiento');
        session()->flash('message', 'Asiento diario actualizado exitosamente.');
        $this->vaciarFormulario();
    }

    public function eliminarModal($id)
    {
        $this->asiento_id = $id;
        $asiento = AsientosDiario::with('transaccion')->find($id);
        
        if ($asiento && $asiento->transaccion && $asiento->transaccion->estado) {
            session()->flash('error', 'No se puede eliminar un asiento de una transacción ya contabilizada.');
            $this->dispatch('error');
            return;
        }
    }

    public function eliminarAsiento()
    {
        $asiento = AsientosDiario::with('transaccion')->find($this->asiento_id);
        
        if ($asiento && (!$asiento->transaccion || !$asiento->transaccion->estado)) {
            $asiento->delete();
            
            $this->dispatch('alertaAsiento');
            session()->flash('message', 'Asiento diario eliminado exitosamente.');
        } else {
            session()->flash('error', 'No se puede eliminar el asiento.');
            $this->dispatch('error');
        }
        
        $this->resetPage();
    }

    public function vaciarFormulario()
    {
        $this->reset([
            'asiento_id', 'nro_asiento', 'monto_debe', 'monto_haber', 
            'descripcion', 'transaccion_id', 'cuenta_id'
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Obtiene las cuentas asociadas a una organización específica
     */
    private function getCuentasPorOrganizacion($organizacionId)
    {
        if (!$organizacionId) {
            return collect();
        }
        
        return Cuenta::select('cuentas.id', 'cuentas.codigo', 'cuentas.nombre')
            ->join('cuentas_orgs', 'cuentas.id', '=', 'cuentas_orgs.cuenta_id')
            ->where('cuentas_orgs.organizacion_id', $organizacionId)
            ->orderBy('cuentas.codigo')
            ->get();
    }

    /**
     * Obtiene las transacciones de una organización específica
     */
    private function getTransaccionesPorOrganizacion($organizacionId)
    {
        if (!$organizacionId) {
            return collect();
        }
        
        return Transaccion::select('id', 'descripcion', 'fecha_transaccion', 'tipo_transaccion', 'estado')
            ->where('organizacion_id', $organizacionId)
            ->orderBy('fecha_transaccion', 'desc')
            ->get();
    }

    /**
     * Verifica si una cuenta pertenece a una organización
     */
    private function cuentaPerteneceAOrganizacion($cuentaId, $organizacionId)
    {
        if (!$cuentaId || !$organizacionId) {
            return false;
        }
        
        return Cuenta::join('cuentas_orgs', 'cuentas.id', '=', 'cuentas_orgs.cuenta_id')
            ->where('cuentas.id', $cuentaId)
            ->where('cuentas_orgs.organizacion_id', $organizacionId)
            ->exists();
    }

    /**
     * Genera un número de asiento automático
     */
    private function generarNumeroAsiento()
    {
        $ultimo = AsientosDiario::whereNotNull('nro_asiento')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$ultimo || !$ultimo->nro_asiento) {
            return 'AST-001';
        }
        
        // Extraer el número del formato AST-XXX
        preg_match('/AST-(\d+)/', $ultimo->nro_asiento, $matches);
        $numero = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        
        return 'AST-' . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        $query = AsientosDiario::with(['transaccion', 'cuenta']);

        // Aplicar filtros
        if ($this->buscar) {
            $query->where(function($q) {
                $q->where('descripcion', 'like', '%' . $this->buscar . '%')
                  ->orWhere('nro_asiento', 'like', '%' . $this->buscar . '%')
                  ->orWhereHas('cuenta', function($sq) {
                      $sq->where('nombre', 'like', '%' . $this->buscar . '%')
                        ->orWhere('codigo', 'like', '%' . $this->buscar . '%');
                  });
            });
        }

        if ($this->filtroTransaccion) {
            $query->where('transaccion_id', $this->filtroTransaccion);
        }

        if ($this->filtroOrganizacion) {
            $query->whereHas('transaccion', function($q) {
                $q->where('organizacion_id', $this->filtroOrganizacion);
            });
        }

        if ($this->filtroCuenta) {
            $query->where('cuenta_id', $this->filtroCuenta);
        }

        $asientos = $query->orderBy($this->contenido, $this->orden)->paginate(15)->withQueryString();

        $organizaciones = Organizacion::select('id', 'nombre')->get();
        $cuentasDisponibles = $this->getCuentasPorOrganizacion($this->organizacion_id);
        $transaccionesDisponibles = $this->getTransaccionesPorOrganizacion($this->organizacion_id);
        
        // Para los filtros
        $todasTransacciones = Transaccion::select('id', 'descripcion', 'fecha_transaccion')
            ->when($this->filtroOrganizacion, function($q) {
                $q->where('organizacion_id', $this->filtroOrganizacion);
            })
            ->orderBy('fecha_transaccion', 'desc')
            ->get();
            
        $todasCuentas = $this->getCuentasPorOrganizacion($this->filtroOrganizacion);

        return view('livewire.asientos-diarios', [
            'asientos' => $asientos,
            'organizaciones' => $organizaciones,
            'cuentasDisponibles' => $cuentasDisponibles,
            'transaccionesDisponibles' => $transaccionesDisponibles,
            'todasTransacciones' => $todasTransacciones,
            'todasCuentas' => $todasCuentas,
        ]);
    }
}
