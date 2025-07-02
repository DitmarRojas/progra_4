<?php

namespace App\Livewire;

use App\Models\AsientosDiario;
use App\Models\Transaccion;
use App\Models\Cuenta;
use App\Models\Organizacion;
use App\Models\Periodo;
use App\Exports\SumasYSaldosExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SumasYSaldos extends Component
{
    // Propiedades para ordenamiento
    public $contenido = 'codigo';
    public $orden = 'asc';

    // Información del período cargado
    public $periodo = null;

    public function mount($periodoId = null)
    {
        // Cargar período desde parámetro de URL o request
        $id = $periodoId ?? request('periodo');
        
        if ($id) {
            $this->periodo = Periodo::with('organizaciones')->find($id);
        }

        // Si no hay período, redirigir a períodos
        if (!$this->periodo) {
            return redirect()->route('periodos');
        }
    }

    /**
     * Regresar a la vista de períodos
     */
    public function volverAPeriodos()
    {
        return redirect()->route('periodos');
    }

    public function ordenar($campo)
    {
        if ($this->contenido === $campo) {
            $this->orden = $this->orden === 'asc' ? 'desc' : 'asc';
        } else {
            $this->contenido = $campo;
            $this->orden = 'asc';
        }
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
     * Exportar sumas y saldos a Excel
     */
    public function exportarExcel()
    {
        if (!$this->periodo) {
            session()->flash('error', 'No hay un período seleccionado para exportar.');
            return;
        }

        $nombreArchivo = 'sumas_y_saldos_' . $this->periodo->organizaciones->nombre . '_' . $this->periodo->nombre . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new SumasYSaldosExport($this->periodo), $nombreArchivo);
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
