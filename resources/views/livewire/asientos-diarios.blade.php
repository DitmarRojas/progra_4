<div>
    {{-- Mensajes de alerta --}}
    <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-[9999] w-full max-w-md pointer-events-none">
        <x-action-message 
            on="alertaAsiento" 
            class="flex items-center p-4 mb-4 text-sm font-medium rounded-lg bg-green-50 text-green-800 border border-green-200 shadow-lg pointer-events-auto">
            <div>
                <span class="font-semibold">¡Éxito!</span> {{ session('message') }}
            </div>
        </x-action-message>

        <x-action-message 
            on="error" 
            class="flex items-center p-4 mb-4 text-sm font-medium rounded-lg bg-red-50 text-red-800 border border-red-200 shadow-lg pointer-events-auto">
            <div>
                <span class="font-semibold">¡Error!</span> {{ session('error') }}
            </div>
        </x-action-message>
    </div>

    <flux:header class="mb-6">
        <flux:heading size="xl" class="text-gray-900 dark:text-white">Gestión de Asientos Diarios</flux:heading>
        <flux:subheading class="text-gray-600 dark:text-gray-400">
            Administra los asientos contables de cada transacción por organización
        </flux:subheading>
    </flux:header>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <flux:heading size="lg" class="mb-4">Filtros</flux:heading>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <flux:input 
                wire:model.live="buscar" 
                placeholder="Buscar asientos..."
                icon="magnifying-glass"
            />
            
            <flux:select wire:model.live="filtroTransaccion" placeholder="Transacción">
                <flux:select.option value="">Todas las transacciones</flux:select.option>
                @foreach($todasTransacciones as $trans)
                    <flux:select.option value="{{ $trans->id }}">
                        {{ $trans->descripcion }} ({{ $trans->fecha_transaccion->format('d/m/Y') }})
                    </flux:select.option>
                @endforeach
            </flux:select>
            
            <flux:select wire:model.live="filtroCuenta" placeholder="Cuenta">
                <flux:select.option value="">Todas las cuentas</flux:select.option>
                @foreach($todasCuentas as $cuenta)
                    <flux:select.option value="{{ $cuenta->id }}">
                        {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Información de organización seleccionada --}}
    @if($organizacion_id)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <flux:icon name="building-office" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    <span class="text-sm font-medium text-blue-800 dark:text-blue-300">
                        Trabajando con: {{ $organizaciones->firstWhere('id', $organizacion_id)->nombre ?? 'Organización' }}
                    </span>
                </div>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    {{ $cuentasDisponibles->count() }} cuentas | {{ $transaccionesDisponibles->count() }} transacciones
                </div>
            </div>
        </div>
    @endif

    {{-- Tabla de asientos diarios --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Transacción
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Cuenta
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="ordenar('monto_debe')">
                            <div class="flex items-center justify-end space-x-1">
                                <span>Debe</span>
                                @if($contenido === 'monto_debe')
                                    <flux:icon name="{{ $orden === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="ordenar('monto_haber')">
                            <div class="flex items-center justify-end space-x-1">
                                <span>Haber</span>
                                @if($contenido === 'monto_haber')
                                    <flux:icon name="{{ $orden === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($asientos as $asiento)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                @if($asiento->transaccion)
                                    <div>
                                        <div class="font-medium">{{ Str::limit($asiento->transaccion->descripcion, 30) }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">
                                            {{ $asiento->transaccion->fecha_transaccion->format('d/m/Y') }} | {{ $asiento->transaccion->tipo_transaccion }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">Sin transacción</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                @if($asiento->cuenta)
                                    <div>
                                        <div class="font-mono text-sm font-medium">{{ $asiento->cuenta->codigo }}</div>
                                        <div class="text-gray-600 dark:text-gray-400">{{ Str::limit($asiento->cuenta->nombre, 25) }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400">Sin cuenta</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ Str::limit($asiento->descripcion, 40) ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                @if($asiento->monto_debe > 0)
                                    <span class="font-medium text-green-600 dark:text-green-400">
                                        ${{ number_format($asiento->monto_debe, 2) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                @if($asiento->monto_haber > 0)
                                    <span class="font-medium text-red-600 dark:text-red-400">
                                        ${{ number_format($asiento->monto_haber, 2) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asiento->transaccion && $asiento->transaccion->estado)
                                    <flux:badge color="green" size="sm">Contabilizada</flux:badge>
                                @else
                                    <flux:badge color="yellow" size="sm">Pendiente</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @if(!$asiento->transaccion || !$asiento->transaccion->estado)
                                        <div>
                                            <flux:tooltip content="Editar asiento" placement="top">
                                                <flux:button 
                                                    wire:click="editarAsiento({{ $asiento->id }})"
                                                    variant="primary"
                                                    icon="pencil"
                                                    class="bg-emerald-400 hover:bg-emerald-500">
                                                </flux:button>
                                            </flux:tooltip>
                                        </div>
                                    @else
                                        <div>
                                            <flux:tooltip content="Transacción contabilizada - No editable" placement="top">
                                                <flux:button 
                                                    variant="primary"
                                                    icon="lock-closed"
                                                    disabled
                                                    class="bg-gray-400 cursor-not-allowed">
                                                </flux:button>
                                            </flux:tooltip>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron asientos diarios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700">
                    @php
                        $totalDebe = $asientos->sum('monto_debe');
                        $totalHaber = $asientos->sum('monto_haber');
                        $diferencia = $totalDebe - $totalHaber;
                    @endphp
                    <tr class="font-medium">
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-900 dark:text-white">
                            <strong>Totales:</strong>
                        </td>
                        <td class="px-6 py-3 text-right text-sm">
                            <span class="font-bold text-green-600 dark:text-green-400">
                                ${{ number_format($totalDebe, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right text-sm">
                            <span class="font-bold text-red-600 dark:text-red-400">
                                ${{ number_format($totalHaber, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center text-sm">
                            @if(abs($diferencia) < 0.01)
                                <flux:badge color="green" size="sm">Cuadrado</flux:badge>
                            @else
                                <flux:badge color="red" size="sm">Descuadrado</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        @if($asientos->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $asientos->links() }}
            </div>
        @endif
    </div>
</div>