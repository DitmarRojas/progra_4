<?php

namespace App\Livewire;

use App\Models\AsientosDiario;
use App\Models\Transaccion;
use App\Models\Cuenta;
use App\Models\Organizacion;
use App\Models\Periodo;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SumasYSaldos extends Component
{
    use WithPagination;

    // Filtros
    public $periodoId = '';
    public $buscar = '';
    
    // Propiedades para ordenamiento
    public $contenido = 'codigo';
    public $orden = 'asc';

    // Información del período cargado
    public $periodo = null;
    
    // Modal de confirmación para cerrar período
    public $mostrarModalCerrar = false;

    public function mount()
    {
        // Si viene un período por URL, cargarlo
        if (request()->has('periodo')) {
            $this->periodoId = request('periodo');
            $this->cargarPeriodo();
        }
    }

    /**
     * Cargar información del período seleccionado
     */
    private function cargarPeriodo()
    {
        if ($this->periodoId) {
            $this->periodo = Periodo::with('organizaciones')->find($this->periodoId);
        }
    }

    public function updatingBuscar()
    {
        $this->resetPage();
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

    /**
     * Genera las sumas y saldos del período seleccionado
     */
    private function generarSumasYSaldos()
    {
        if (!$this->periodo) {
            return collect();
        }

        // Query base para obtener las cuentas con movimientos en el período
        $query = Cuenta::select([
            'cuentas.id',
            'cuentas.codigo',
            'cuentas.nombre',
            'cuentas.tipo',
            DB::raw('COALESCE(SUM(asientos_diarios.monto_debe), 0) as total_debe'),
            DB::raw('COALESCE(SUM(asientos_diarios.monto_haber), 0) as total_haber'),
            DB::raw('(COALESCE(SUM(asientos_diarios.monto_debe), 0) - COALESCE(SUM(asientos_diarios.monto_haber), 0)) as saldo_deudor'),
            DB::raw('(COALESCE(SUM(asientos_diarios.monto_haber), 0) - COALESCE(SUM(asientos_diarios.monto_debe), 0)) as saldo_acreedor')
        ])
        ->join('asientos_diarios', 'cuentas.id', '=', 'asientos_diarios.cuenta_id')
        ->join('transacciones', function($join) {
            $join->on('asientos_diarios.transaccion_id', '=', 'transacciones.id')
                 ->where('transacciones.estado', true) // Solo transacciones contabilizadas
                 ->whereBetween('transacciones.fecha_transaccion', [$this->periodo->fecha_inicio, $this->periodo->fecha_fin]);
        })
        ->join('cuentas_orgs', 'cuentas.id', '=', 'cuentas_orgs.cuenta_id')
        ->where('cuentas_orgs.organizacion_id', $this->periodo->organizacion_id);

        // Filtro de búsqueda
        if ($this->buscar) {
            $query->where(function($q) {
                $q->where('cuentas.codigo', 'like', '%' . $this->buscar . '%')
                  ->orWhere('cuentas.nombre', 'like', '%' . $this->buscar . '%');
            });
        }

        $cuentas = $query->groupBy('cuentas.id', 'cuentas.codigo', 'cuentas.nombre', 'cuentas.tipo')
                        ->having(DB::raw('COALESCE(SUM(asientos_diarios.monto_debe), 0) + COALESCE(SUM(asientos_diarios.monto_haber), 0)'), '>', 0) // Solo cuentas con movimientos
                        ->orderBy('cuentas.' . $this->contenido, $this->orden)
                        ->get();

        // Procesar cada cuenta para clasificar en las columnas correspondientes
        return $cuentas->map(function($cuenta) {
            $saldoDeudor = max(0, $cuenta->saldo_deudor);
            $saldoAcreedor = max(0, $cuenta->saldo_acreedor);

            // Inicializar columnas de clasificación
            $activo = 0;
            $pasivo = 0;
            $capital = 0;
            $egresos = 0;
            $ingresos = 0;

            // Clasificar según el tipo de cuenta y su saldo
            switch (strtoupper($cuenta->tipo)) {
                case 'ACTIVO':
                    $activo = $saldoDeudor;
                    break;
                case 'PASIVO':
                    $pasivo = $saldoAcreedor;
                    break;
                case 'PATRIMONIO':
                    $capital = $saldoAcreedor;
                    break;
                case 'EGRESOS':
                    $egresos = $saldoDeudor;
                    break;
                case 'INGRESOS':
                    $ingresos = $saldoAcreedor;
                    break;
            }

            return (object) [
                'id' => $cuenta->id,
                'codigo' => $cuenta->codigo,
                'nombre' => $cuenta->nombre,
                'tipo' => $cuenta->tipo,
                'debe' => $cuenta->total_debe,
                'haber' => $cuenta->total_haber,
                'saldo_deudor' => $saldoDeudor,
                'saldo_acreedor' => $saldoAcreedor,
                'activo' => $activo,
                'pasivo' => $pasivo,
                'capital' => $capital,
                'egresos' => $egresos,
                'ingresos' => $ingresos,
            ];
        });
    }

    /**
     * Calcula los totales de cada columna
     */
    private function calcularTotales($sumasYSaldos)
    {
        return (object) [
            'total_debe' => $sumasYSaldos->sum('debe'),
            'total_haber' => $sumasYSaldos->sum('haber'),
            'total_saldo_deudor' => $sumasYSaldos->sum('saldo_deudor'),
            'total_saldo_acreedor' => $sumasYSaldos->sum('saldo_acreedor'),
            'total_activo' => $sumasYSaldos->sum('activo'),
            'total_pasivo' => $sumasYSaldos->sum('pasivo'),
            'total_capital' => $sumasYSaldos->sum('capital'),
            'total_egresos' => $sumasYSaldos->sum('egresos'),
            'total_ingresos' => $sumasYSaldos->sum('ingresos'),
        ];
    }

    /**
     * Verificar si el balance está cuadrado
     */
    private function verificarBalance($totales)
    {
        $cuadreDebe = abs($totales->total_debe - $totales->total_haber) < 0.01;
        $cuadreBalance = abs(($totales->total_activo) - ($totales->total_pasivo + $totales->total_capital)) < 0.01;
        $cuadreResultados = abs($totales->total_ingresos - $totales->total_egresos) < 0.01;

        return (object) [
            'debe_haber_cuadrado' => $cuadreDebe,
            'balance_cuadrado' => $cuadreBalance,
            'resultados_cuadrado' => $cuadreResultados,
            'todo_cuadrado' => $cuadreDebe && $cuadreBalance && $cuadreResultados
        ];
    }

    /**
     * Mostrar modal de confirmación para cerrar período
     */
    public function mostrarModalCerrarPeriodo()
    {
        if (!$this->periodo) {
            session()->flash('error', 'No hay un período seleccionado.');
            return;
        }

        if ($this->periodo->estado === 'Cerrado') {
            session()->flash('error', 'Este período ya está cerrado.');
            return;
        }

        $this->mostrarModalCerrar = true;
    }

    /**
     * Cerrar el período actual
     */
    public function cerrarPeriodo()
    {
        if (!$this->periodo) {
            session()->flash('error', 'No hay un período seleccionado.');
            return;
        }

        if ($this->periodo->estado === 'Cerrado') {
            session()->flash('error', 'Este período ya está cerrado.');
            return;
        }

        // Verificar que el balance esté cuadrado
        $sumasYSaldos = $this->generarSumasYSaldos();
        $totales = $this->calcularTotales($sumasYSaldos);
        $balance = $this->verificarBalance($totales);

        if (!$balance->debe_haber_cuadrado) {
            session()->flash('error', 'No se puede cerrar el período. El balance debe/haber no está cuadrado.');
            $this->mostrarModalCerrar = false;
            return;
        }

        if (!$balance->balance_cuadrado) {
            session()->flash('error', 'No se puede cerrar el período. El balance general no está cuadrado.');
            $this->mostrarModalCerrar = false;
            return;
        }

        // Cerrar el período
        DB::transaction(function () {
            $this->periodo->update([
                'estado' => 'Cerrado',
                'fecha_cierre' => now()
            ]);
        });

        $this->mostrarModalCerrar = false;
        session()->flash('message', 'Período cerrado exitosamente.');
        
        // Recargar el período
        $this->cargarPeriodo();
    }

    /**
     * Cancelar el cierre del período
     */
    public function cancelarCierre()
    {
        $this->mostrarModalCerrar = false;
    }

    public function render()
    {
        $sumasYSaldos = $this->generarSumasYSaldos();
        $totales = $this->calcularTotales($sumasYSaldos);
        $balance = $this->verificarBalance($totales);

        return view('livewire.sumas-y-saldos', [
            'sumasYSaldos' => $sumasYSaldos,
            'totales' => $totales,
            'balance' => $balance,
        ]);
    }
}
