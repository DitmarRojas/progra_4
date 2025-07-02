<?php

namespace App\Exports;

use App\Models\Periodo;
use App\Models\Cuenta;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SumasYSaldosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnFormatting
{
    private $periodo;
    private $totales;

    public function __construct(Periodo $periodo)
    {
        $this->periodo = $periodo;
    }

    /**
     * Obtener la colección de datos
     */
    public function collection()
    {
        return $this->generarSumasYSaldos();
    }

    /**
     * Definir los encabezados
     */
    public function headings(): array
    {
        return [
            'Código',
            'Nombre de la Cuenta',
            'Tipo',
            'Debe',
            'Haber',
            'Saldo Deudor',
            'Saldo Acreedor',
            'Activo',
            'Pasivo',
            'Capital',
            'Egresos',
            'Ingresos'
        ];
    }

    /**
     * Mapear los datos para cada fila
     */
    public function map($cuenta): array
    {
        return [
            $cuenta->codigo,
            $cuenta->nombre,
            $cuenta->tipo,
            $cuenta->debe,
            $cuenta->haber,
            $cuenta->saldo_deudor,
            $cuenta->saldo_acreedor,
            $cuenta->activo,
            $cuenta->pasivo,
            $cuenta->capital,
            $cuenta->egresos,
            $cuenta->ingresos
        ];
    }

    /**
     * Aplicar estilos a la hoja
     */
    public function styles(Worksheet $sheet)
    {
        // Calcular totales para agregarlos al final
        $sumasYSaldos = $this->generarSumasYSaldos();
        $this->totales = $this->calcularTotales($sumasYSaldos);
        $lastRow = $sumasYSaldos->count() + 2;

        // Información del período
        $sheet->setCellValue('A1', 'SUMAS Y SALDOS');
        $sheet->setCellValue('A2', 'Organización: ' . $this->periodo->organizaciones->nombre);
        $sheet->setCellValue('A3', 'Período: ' . $this->periodo->nombre);
        $sheet->setCellValue('A4', 'Desde: ' . $this->periodo->fecha_inicio . ' Hasta: ' . $this->periodo->fecha_fin);
        $sheet->setCellValue('A5', 'Estado: ' . $this->periodo->estado);
        $sheet->setCellValue('A6', 'Generado: ' . now()->format('d/m/Y H:i:s'));

        // Mover los encabezados a la fila 8
        $sheet->insertNewRowBefore(8, 7);

        // Agregar fila de totales
        $totalRow = $lastRow + 7;
        $sheet->setCellValue('A' . $totalRow, 'TOTALES');
        $sheet->setCellValue('B' . $totalRow, '');
        $sheet->setCellValue('C' . $totalRow, '');
        $sheet->setCellValue('D' . $totalRow, $this->totales->total_debe);
        $sheet->setCellValue('E' . $totalRow, $this->totales->total_haber);
        $sheet->setCellValue('F' . $totalRow, $this->totales->total_saldo_deudor);
        $sheet->setCellValue('G' . $totalRow, $this->totales->total_saldo_acreedor);
        $sheet->setCellValue('H' . $totalRow, $this->totales->total_activo);
        $sheet->setCellValue('I' . $totalRow, $this->totales->total_pasivo);
        $sheet->setCellValue('J' . $totalRow, $this->totales->total_capital);
        $sheet->setCellValue('K' . $totalRow, $this->totales->total_egresos);
        $sheet->setCellValue('L' . $totalRow, $this->totales->total_ingresos);

        return [
            // Estilo del título
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
            6 => ['font' => ['italic' => true]],
            
            // Estilo de los encabezados
            8 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'E3F2FD']]],
            
            // Estilo de la fila de totales
            $totalRow => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'FFF3E0']]],
        ];
    }

    /**
     * Título de la hoja
     */
    public function title(): string
    {
        return 'Sumas y Saldos';
    }

    /**
     * Formato de columnas
     */
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    /**
     * Genera las sumas y saldos del período (copiado del componente)
     */
    private function generarSumasYSaldos()
    {
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
        ->leftJoin('asientos_diarios', 'cuentas.id', '=', 'asientos_diarios.cuenta_id')
        ->leftJoin('transacciones', function($join) {
            $join->on('asientos_diarios.transaccion_id', '=', 'transacciones.id')
                 ->where('transacciones.estado', true)
                 ->whereBetween('transacciones.fecha_transaccion', [$this->periodo->fecha_inicio, $this->periodo->fecha_fin]);
        })
        ->join('cuentas_orgs', 'cuentas.id', '=', 'cuentas_orgs.cuenta_id')
        ->where('cuentas_orgs.organizacion_id', $this->periodo->organizacion_id);

        $cuentas = $query->groupBy('cuentas.id', 'cuentas.codigo', 'cuentas.nombre', 'cuentas.tipo')
                        ->orderBy('cuentas.codigo', 'asc')
                        ->get();

        return $cuentas->map(function($cuenta) {
            $saldoDeudor = max(0, $cuenta->saldo_deudor);
            $saldoAcreedor = max(0, $cuenta->saldo_acreedor);

            $activo = 0;
            $pasivo = 0;
            $capital = 0;
            $egresos = 0;
            $ingresos = 0;

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
     * Calcula los totales
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
}
