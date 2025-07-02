<?php

namespace App\Livewire;

use App\Models\Organizacion;
use App\Models\Transaccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Transacciones extends Component
{
    use WithPagination;

    // Propiedades de la Transacción
    public $fecha_transaccion;
    public $tipo_transaccion = 'Otro';
    public $descripcion = '';
    public $num_referencia = '';
    public $estado = false;
    
    // Gestión de Asientos Diarios
    public $asientos = [];

    // Gestión de Organización
    public $selected_org_id;
    public $selected_org_name;
    public $cuentas_organizacion = [];

    // Búsqueda y Paginación
    public $buscar = '';
    public $contenido = 'num_referencia';
    public $orden = 'asc';

    // Edición de transacciones
    public $editando_transaccion_id = null;
    public $editando = false;
    
    // Modal de confirmación para cambio de estado
    public $transaccion_cambio_estado_id = null;
    
    // Modal de confirmación para eliminar transacción
    public $transaccion_eliminar_id = null;

    protected function rules()
    {
        $rules = [
            'fecha_transaccion' => 'required|date',
            'tipo_transaccion' => 'required|string|in:Ingreso,Gasto,Transferencia,Ajuste,Otro',
            'descripcion' => 'nullable|string|max:255',
            'num_referencia' => 'nullable|string|max:255',
            'asientos' => 'required|array|min:2',
            'asientos.*.cuenta_id' => 'required|exists:cuentas,id',
            'asientos.*.debe' => 'nullable|numeric|min:0',
            'asientos.*.haber' => 'nullable|numeric|min:0',
            'asientos.*.descripcion' => 'nullable|string|max:255',
        ];

        // Validación personalizada: no puede haber cuentas duplicadas
        $rules['asientos'] = [
            'required',
            'array',
            'min:2',
            function ($attribute, $value, $fail) {
                $cuentas = collect($value)->pluck('cuenta_id')->filter();
                if ($cuentas->count() !== $cuentas->unique()->count()) {
                    $fail('No puede seleccionar la misma cuenta en múltiples asientos.');
                }
            },
        ];

        return $rules;
    }

    protected $messages = [
        'fecha_transaccion.required' => 'La fecha de transacción es obligatoria.',
        'tipo_transaccion.required' => 'El tipo de transacción es obligatorio.',
        'asientos.required' => 'Debe agregar al menos dos asientos.',
        'asientos.min' => 'Debe tener al menos 2 asientos para cumplir con la partida doble.',
        'asientos.*.cuenta_id.required' => 'Debe seleccionar una cuenta.',
        'asientos.*.cuenta_id.exists' => 'La cuenta seleccionada no es válida.',
        'asientos.*.debe.min' => 'El monto del debe no puede ser negativo.',
        'asientos.*.haber.min' => 'El monto del haber no puede ser negativo.',
    ];

    public function mount()
    {
        $this->fecha_transaccion = now()->format('Y-m-d');
        $this->selected_org_id = session('selected_org_id');
        $this->tipo_transaccion = 'Otro';
        $this->estado = false; // false = pendiente

        if ($this->selected_org_id) {
            $this->cargarOrganizacion($this->selected_org_id);
        }
        
        // Solo inicializar con 1 asiento
        $this->addAsiento();
    }

    public function cargarOrganizacion($orgId)
    {
        $organizacion = Organizacion::with('cuentas')->find($orgId);
        if ($organizacion) {
            $this->selected_org_id = $organizacion->id;
            $this->selected_org_name = $organizacion->nombre;
            $this->cuentas_organizacion = $organizacion->cuentas;
            session(['selected_org_id' => $organizacion->id]);
        }
    }

    public function cambiarOrganizacion()
    {
        $this->reset(['selected_org_id', 'selected_org_name', 'cuentas_organizacion']);
        session()->forget('selected_org_id');
        $this->cancelarEdicion();
    }

    public function addAsiento()
    {
        $this->asientos[] = ['cuenta_id' => '', 'debe' => '', 'haber' => '', 'descripcion' => ''];
    }

    public function removeAsiento($index)
    {
        if (count($this->asientos) > 1) { // Mantener mínimo 1 asiento
            unset($this->asientos[$index]);
            $this->asientos = array_values($this->asientos);
        }
    }

    public function editarTransaccion($transaccionId)
    {
        $transaccion = Transaccion::with('asientosDiarios')->find($transaccionId);
        
        if (!$transaccion) {
            session()->flash('error', 'Transacción no encontrada.');
            return;
        }

        // Solo permitir editar si está pendiente
        if ($transaccion->estado !== false) {
            session()->flash('error', 'Solo se pueden editar transacciones en estado pendiente.');
            return;
        }

        // Verificar si la transacción pertenece a un período cerrado
        if ($this->transaccionEnPeriodoCerrado($transaccionId)) {
            session()->flash('error', 'No se puede editar una transacción que pertenece a un período cerrado.');
            return;
        }

        $this->editando = true;
        $this->editando_transaccion_id = $transaccionId;
        $this->fecha_transaccion = $transaccion->fecha_transaccion;
        $this->tipo_transaccion = $transaccion->tipo_transaccion;
        $this->descripcion = $transaccion->descripcion;
        $this->num_referencia = $transaccion->num_referencia;
        $this->estado = $transaccion->estado;

        // Cargar asientos
        $this->asientos = [];
        foreach ($transaccion->asientosDiarios as $asiento) {
            $this->asientos[] = [
                'cuenta_id' => $asiento->cuenta_id,
                'debe' => $asiento->monto_debe > 0 ? $asiento->monto_debe : '',
                'haber' => $asiento->monto_haber > 0 ? $asiento->monto_haber : '',
                'descripcion' => $asiento->descripcion
            ];
        }
    }

    public function cancelarEdicion()
    {
        $this->editando = false;
        $this->editando_transaccion_id = null;
        $this->resetForm();
    }

    public function mostrarModalCambioEstado($transaccionId)
    {
        $this->transaccion_cambio_estado_id = $transaccionId;
        // FluxUI maneja automáticamente la apertura del modal
    }

    public function cerrarModalCambioEstado()
    {
        $this->transaccion_cambio_estado_id = null;
        // Con FluxUI, cerramos el modal usando el dispatch
        $this->dispatch('close-modal', 'cambio-estado');
    }

    public function mostrarModalEliminar($transaccionId)
    {
        $this->transaccion_eliminar_id = $transaccionId;
        // FluxUI maneja automáticamente la apertura del modal
    }

    public function cerrarModalEliminar()
    {
        $this->transaccion_eliminar_id = null;
        $this->dispatch('close-modal', 'eliminar-transaccion');
    }

    public function confirmarEliminarTransaccion()
    {
        if (!$this->transaccion_eliminar_id) {
            return;
        }

        $transaccion = Transaccion::find($this->transaccion_eliminar_id);
        
        if (!$transaccion) {
            session()->flash('error', 'Transacción no encontrada.');
            $this->transaccion_eliminar_id = null;
            return;
        }

        // Solo permitir eliminar si está pendiente
        if ($transaccion->estado !== false) {
            session()->flash('error', 'Solo se pueden eliminar transacciones en estado pendiente.');
            $this->transaccion_eliminar_id = null;
            return;
        }

        try {
            DB::transaction(function () use ($transaccion) {
                $transaccion->asientosDiarios()->delete();
                $transaccion->delete();
            });

            session()->flash('message', 'Transacción eliminada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la transacción: ' . $e->getMessage());
        }

        // Limpiar el ID de transacción
        $this->transaccion_eliminar_id = null;
    }

    public function confirmarCambioEstado()
    {
        if (!$this->transaccion_cambio_estado_id) {
            return;
        }

        $transaccion = Transaccion::find($this->transaccion_cambio_estado_id);
        
        if (!$transaccion) {
            session()->flash('error', 'Transacción no encontrada.');
            $this->transaccion_cambio_estado_id = null;
            return;
        }

        // Cambiar estado
        $nuevoEstado = $transaccion->estado === false ? true : false;
        $transaccion->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado ? 'Transacción marcada como procesada.' : 'Transacción marcada como pendiente.';
        session()->flash('message', $mensaje);
        
        // Limpiar el ID de transacción y cerrar modal
        $this->transaccion_cambio_estado_id = null;
        $this->dispatch('close-modal', 'cambio-estado');
    }

    public function crearTransaccion()
    {
        // Validar si hay una organización seleccionada
        if (!$this->selected_org_id) {
            $this->addError('organizacion', 'Debe seleccionar una organización primero.');
            return;
        }

        // Limpiar asientos vacíos antes de validar
        $this->limpiarAsientosVacios();

        $this->validate();

        // Validar que cada asiento tenga al menos un monto (debe o haber)
        foreach ($this->asientos as $index => $asiento) {
            $debe = floatval($asiento['debe'] ?? 0);
            $haber = floatval($asiento['haber'] ?? 0);
            
            if ($debe > 0 && $haber > 0) {
                $this->addError("asientos.{$index}.debe", 'Un asiento no puede tener tanto Debe como Haber.');
                return;
            }
            
            if ($debe == 0 && $haber == 0) {
                $this->addError("asientos.{$index}.debe", 'El asiento debe tener un monto en Debe o Haber.');
                return;
            }
        }

        // Validación de partida doble
        $totalDebe = collect($this->asientos)->sum(function($asiento) {
            return floatval($asiento['debe'] ?? 0);
        });
        $totalHaber = collect($this->asientos)->sum(function($asiento) {
            return floatval($asiento['haber'] ?? 0);
        });

        if (abs($totalDebe - $totalHaber) > 0.01) { // Tolerancia para decimales
            $this->addError('partida_doble', 'El total del Debe ('.number_format($totalDebe, 2).') no coincide con el total del Haber ('.number_format($totalHaber, 2).').');
            return;
        }
        
        if ($totalDebe == 0) {
            $this->addError('partida_doble', 'El movimiento no puede ser cero.');
            return;
        }

        // Validar que al menos un asiento tenga debe y otro haber
        $tieneDebes = collect($this->asientos)->where('debe', '>', 0)->count();
        $tieneHaberes = collect($this->asientos)->where('haber', '>', 0)->count();
        
        if ($tieneDebes == 0 || $tieneHaberes == 0) {
            $this->addError('partida_doble', 'Debe tener al menos un asiento con Debe y otro con Haber.');
            return;
        }

            DB::transaction(function () {
                if ($this->editando && $this->editando_transaccion_id) {
                    // Actualizar transacción existente
                    $transaccion = Transaccion::find($this->editando_transaccion_id);
                    $transaccion->update([
                        'fecha_transaccion' => $this->fecha_transaccion,
                        'tipo_transaccion' => $this->tipo_transaccion,
                        'descripcion' => $this->descripcion,
                        'num_referencia' => $this->num_referencia,
                        'estado' => $this->estado,
                    ]);

                    // Eliminar asientos anteriores y crear nuevos
                    $transaccion->asientosDiarios()->delete();
                } else {
                    // Crear nueva transacción
                    $transaccion = Transaccion::create([
                        'fecha_transaccion' => $this->fecha_transaccion,
                        'tipo_transaccion' => $this->tipo_transaccion,
                        'descripcion' => $this->descripcion,
                        'num_referencia' => $this->num_referencia,
                        'usuario_id' => Auth::user()->id,
                        'estado' => false, // Por defecto pendiente (false)
                    ]);
                }

                foreach ($this->asientos as $asiento) {
                    $transaccion->asientosDiarios()->create([
                        'cuenta_id' => $asiento['cuenta_id'],
                        'monto_debe' => floatval($asiento['debe'] ?? 0),
                        'monto_haber' => floatval($asiento['haber'] ?? 0),
                        'descripcion' => $asiento['descripcion'] ?? $this->descripcion,
                    ]);
                }
            });

            $mensaje = $this->editando ? 'Transacción actualizada exitosamente.' : 'Transacción creada exitosamente.';
            session()->flash('message', $mensaje);
            $this->dispatch('transaccion-guardada');
            $this->resetForm();
    }

    private function limpiarAsientosVacios()
    {
        $this->asientos = array_filter($this->asientos, function($asiento) {
            return !empty($asiento['cuenta_id']) && 
                   (floatval($asiento['debe'] ?? 0) > 0 || floatval($asiento['haber'] ?? 0) > 0);
        });
        $this->asientos = array_values($this->asientos);
    }
    
    public function resetForm()
    {
        $this->reset(['fecha_transaccion', 'descripcion', 'num_referencia', 'asientos', 'editando', 'editando_transaccion_id', 'estado']);
        $this->fecha_transaccion = now()->format('Y-m-d');
        $this->tipo_transaccion = 'Otro';
        $this->estado = false;
        $this->addAsiento();
    }

    public function getTotalDebeProperty()
    {
        return collect($this->asientos)->sum(function($asiento) {
            return floatval($asiento['debe'] ?? 0);
        });
    }

    public function getTotalHaberProperty()
    {
        return collect($this->asientos)->sum(function($asiento) {
            return floatval($asiento['haber'] ?? 0);
        });
    }

    public function getDiferenciaProperty()
    {
        return $this->total_debe - $this->total_haber;
    }

    public function updatedAsientos($value, $key)
    {
        // Solo manejar actualizaciones de debe y haber para cálculos en tiempo real
        if (strpos($key, '.debe') !== false || strpos($key, '.haber') !== false) {
            $parts = explode('.', $key);
            $index = $parts[0];
            $field = $parts[1];
            
            // Si se ingresa un valor en 'debe', limpiar 'haber' y viceversa
            if ($field === 'debe' && floatval($value) > 0) {
                $this->asientos[$index]['haber'] = '';
            } elseif ($field === 'haber' && floatval($value) > 0) {
                $this->asientos[$index]['debe'] = '';
            }
        }
    }

    /**
     * Verificar si la transacción pertenece a un período cerrado
     */
    private function transaccionEnPeriodoCerrado($transaccionId)
    {
        $transaccion = Transaccion::find($transaccionId);
        
        if (!$transaccion) {
            return false;
        }

        // Buscar si alguna cuenta de los asientos de la transacción está en un período cerrado
        $periodosCerrados = DB::table('transacciones')
            ->join('asientos_diarios', 'transacciones.id', '=', 'asientos_diarios.transaccion_id')
            ->join('cuentas', 'asientos_diarios.cuenta_id', '=', 'cuentas.id')
            ->join('cuentas_orgs', 'cuentas.id', '=', 'cuentas_orgs.cuenta_id')
            ->join('periodos', 'cuentas_orgs.organizacion_id', '=', 'periodos.organizacion_id')
            ->where('transacciones.id', $transaccionId)
            ->where('periodos.estado', 'Cerrado')
            ->whereDate('transacciones.fecha_transaccion', '>=', DB::raw('periodos.fecha_inicio'))
            ->whereDate('transacciones.fecha_transaccion', '<=', DB::raw('periodos.fecha_fin'))
            ->exists();

        return $periodosCerrados;
    }

    public function render()
    {
        $organizaciones = $this->selected_org_id ? [] : Organizacion::all();
        
        $transacciones = Transaccion::with('asientosDiarios.cuenta', 'usuarios')
            ->whereHas('asientosDiarios.cuenta', function($query) {
                if ($this->selected_org_id) {
                    $query->whereHas('organizaciones', function($subQuery) {
                        $subQuery->where('organizaciones.id', $this->selected_org_id);
                    });
                }
            })
            ->where(function($query) {
                if($this->buscar && $this->contenido) {
                    $query->where($this->contenido, 'like', '%' . $this->buscar . '%');
                }
            })
            ->orderBy('fecha_transaccion', $this->orden)
            ->paginate(5);

        return view('livewire.transacciones',[
            'transacciones' => $transacciones,
            'organizaciones' => $organizaciones
        ]);
    }
}