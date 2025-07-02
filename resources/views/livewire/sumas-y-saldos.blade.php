<div>
    <flux:heading size="xl" class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <flux:icon.chart-bar class="w-8 h-8 text-blue-600" />
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sumas y Saldos</h1>
                    @if($periodo)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $periodo->organizaciones->nombre }} - {{ $periodo->nombre }}
                            ({{ \Carbon\Carbon::parse($periodo->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($periodo->fecha_fin)->format('d/m/Y') }})
                        </p>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <flux:button wire:click="volverAPeriodos" variant="outline" size="sm">
                    <flux:icon.arrow-left class="w-4 h-4" />
                    Volver a Períodos
                </flux:button>
                @if($periodo)
                    <flux:button wire:click="exportarExcel" variant="primary" size="sm">
                        <flux:icon.document-arrow-down class="w-4 h-4" />
                        Exportar Excel
                    </flux:button>
                @endif
            </div>
        </div>
    </flux:heading>

    @if($periodo)
        {{-- Tabla de Sumas y Saldos --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                                wire:click="ordenar('codigo')">
                                Código
                                @if($contenido === 'codigo')
                                    <flux:icon.chevron-up class="inline w-4 h-4 {{ $orden === 'asc' ? '' : 'rotate-180' }}" />
                                @endif
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                                wire:click="ordenar('nombre')">
                                Cuenta
                                @if($contenido === 'nombre')
                                    <flux:icon.chevron-up class="inline w-4 h-4 {{ $orden === 'asc' ? '' : 'rotate-180' }}" />
                                @endif
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Debe
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Haber
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Saldo Deudor
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Saldo Acreedor
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Activo
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Pasivo
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Capital
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Egresos
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ingresos
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($sumasYSaldos as $cuenta)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $cuenta->codigo }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    {{ $cuenta->nombre }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ number_format($cuenta->debe, 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ number_format($cuenta->haber, 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ $cuenta->saldo_deudor > 0 ? number_format($cuenta->saldo_deudor, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ $cuenta->saldo_acreedor > 0 ? number_format($cuenta->saldo_acreedor, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ $cuenta->activo > 0 ? number_format($cuenta->activo, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ $cuenta->pasivo > 0 ? number_format($cuenta->pasivo, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ $cuenta->capital > 0 ? number_format($cuenta->capital, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ $cuenta->egresos > 0 ? number_format($cuenta->egresos, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ $cuenta->ingresos > 0 ? number_format($cuenta->ingresos, 2) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <flux:icon.chart-bar class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                    <p>No hay movimientos contables en este período</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($sumasYSaldos->isNotEmpty())
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr class="font-bold text-gray-900 dark:text-white">
                                <td class="px-4 py-3 text-sm font-bold" colspan="2">TOTALES</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format($totales->total_debe, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format($totales->total_haber, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format($totales->total_saldo_deudor, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format($totales->total_saldo_acreedor, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format($totales->total_activo, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format($totales->total_pasivo, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format($totales->total_capital, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format($totales->total_egresos, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format($totales->total_ingresos, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Indicadores de Balance --}}
        @if($sumasYSaldos->isNotEmpty())
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        @if($balance->debe_haber_cuadrado)
                            <flux:icon.check-circle class="w-5 h-5 text-green-500 mr-2" />
                            <span class="text-sm text-green-600 dark:text-green-400">Debe = Haber</span>
                        @else
                            <flux:icon.x-circle class="w-5 h-5 text-red-500 mr-2" />
                            <span class="text-sm text-red-600 dark:text-red-400">Debe ≠ Haber</span>
                        @endif
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        @if($balance->balance_cuadrado)
                            <flux:icon.check-circle class="w-5 h-5 text-green-500 mr-2" />
                            <span class="text-sm text-green-600 dark:text-green-400">Balance Cuadrado</span>
                        @else
                            <flux:icon.x-circle class="w-5 h-5 text-red-500 mr-2" />
                            <span class="text-sm text-red-600 dark:text-red-400">Balance Descuadrado</span>
                        @endif
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        @if($balance->todo_cuadrado)
                            <flux:icon.check-circle class="w-5 h-5 text-green-500 mr-2" />
                            <span class="text-sm text-green-600 dark:text-green-400">Todo Correcto</span>
                        @else
                            <flux:icon.exclamation-triangle class="w-5 h-5 text-yellow-500 mr-2" />
                            <span class="text-sm text-yellow-600 dark:text-yellow-400">Revisar Balance</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-8">
            <div class="text-center">
                <flux:icon.exclamation-triangle class="w-16 h-16 mx-auto mb-4 text-yellow-500" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay período seleccionado</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Selecciona un período para ver las sumas y saldos</p>
                <flux:button wire:click="volverAPeriodos" variant="primary">
                    <flux:icon.arrow-left class="w-4 h-4" />
                    Ir a Períodos
                </flux:button>
            </div>
        </div>
    @endif
</div>
