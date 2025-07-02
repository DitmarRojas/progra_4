<div>
    {{-- Header principal --}}
    <div class="mb-8">
        <flux:heading size="xl" class="text-gray-900 dark:text-gray-100">Sistema Contable</flux:heading>
        <flux:subheading class="text-gray-600 dark:text-gray-400">Gestión profesional de transacciones y asientos contables</flux:subheading>
    </div>    {{-- 🏢 SELECCIÓN DE ORGANIZACIÓN --}}
    @if(!$selected_org_id)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <flux:icon.building-office class="w-16 h-16 mx-auto mb-4 text-blue-600 dark:text-blue-400" />
                <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Seleccionar Organización</flux:heading>
                <flux:subheading class="text-gray-600 dark:text-gray-400">Elige una organización para gestionar sus transacciones contables</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($organizaciones as $organizacion)
                    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-6 cursor-pointer hover:shadow-lg transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-600" 
                        wire:click="cargarOrganizacion({{ $organizacion->id }})">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                                    {{ substr($organizacion->nombre, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <flux:heading size="sm" class="truncate text-gray-900 dark:text-gray-100">{{ $organizacion->nombre }}</flux:heading>
                                <flux:subheading class="text-gray-600 dark:text-gray-400">NIT: {{ $organizacion->nit }}</flux:subheading>
                                @if($organizacion->direccion)
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $organizacion->direccion }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-blue-600 dark:text-blue-400">
                            <span>Seleccionar</span>
                            <flux:icon.arrow-right class="ml-1 w-4 h-4" />
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <flux:icon.building-office class="mx-auto w-12 h-12 text-gray-400 dark:text-gray-500" />
                        <flux:heading size="lg" class="mt-2 text-gray-900 dark:text-gray-100">No hay organizaciones</flux:heading>
                        <flux:subheading class="text-gray-600 dark:text-gray-400">Debes crear una organización primero antes de gestionar transacciones.</flux:subheading>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        {{-- 📊 PANEL DE GESTIÓN DE TRANSACCIONES --}}
        <div class="space-y-6">
            
            {{-- Header con organización seleccionada --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center space-x-4">
                            <flux:icon.building-office class="w-8 h-8" />
                            <div>
                                <flux:heading size="lg" class="text-white">{{ $selected_org_name }}</flux:heading>
                                <p class="text-blue-100">Gestión de transacciones contables</p>
                            </div>
                        </div>
                        <flux:button variant="ghost" size="sm" wire:click="cambiarOrganizacion" class="mt-4 sm:mt-0 text-white border-white/20 hover:bg-white/10">
                            Cambiar Organización
                        </flux:button>
                    </div>
                </div>
            </div>

            {{-- Error de organización --}}
            @error('organizacion') 
                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-center">
                    <flux:icon.exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400 mr-3" />
                    <flux:text class="text-red-700 dark:text-red-300">{{ $message }}</flux:text>
                </div>
            @enderror

            {{-- Banner de modo edición --}}
            @if($editando)
                <div class="p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg flex items-center">
                    <flux:icon.pencil class="w-6 h-6 text-orange-600 dark:text-orange-400 mr-3" />
                    <div class="flex-1">
                        <p class="font-semibold text-orange-800 dark:text-orange-200">Modo Edición Activo</p>
                        <p class="text-orange-700 dark:text-orange-300">Está editando la transacción. Complete los cambios y guarde o cancele la edición.</p>
                    </div>
                    <flux:button 
                        type="button" 
                        wire:click="cancelarEdicion" 
                        variant="ghost" 
                        size="sm"
                        class="text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300">
                        <flux:icon.x-mark class="w-4 h-4 mr-1" />
                        Cancelar
                    </flux:button>
                </div>
            @endif

            {{-- 📝 FORMULARIO DE NUEVA TRANSACCIÓN --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg {{ $editando ? 'ring-2 ring-orange-300 dark:ring-orange-600' : '' }}" id="formulario-transaccion"
                >
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            @if($editando)
                                <flux:icon.pencil class="w-5 h-5 mr-2 text-orange-600 dark:text-orange-400" />
                                <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Editar Transacción</flux:heading>
                            @else
                                <flux:icon.plus class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" />
                                <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Nueva Transacción Contable</flux:heading>
                            @endif
                        </div>
                        @if($editando)
                            <flux:button 
                                type="button" 
                                wire:click="cancelarEdicion" 
                                variant="ghost" 
                                size="sm">
                                <flux:icon.x-mark class="w-4 h-4 mr-2" />
                                Cancelar Edición
                            </flux:button>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    <form wire:submit.prevent="crearTransaccion" class="space-y-6">
                        {{-- Datos básicos de la transacción --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <flux:input 
                                type="date" 
                                wire:model="fecha_transaccion" 
                                label="Fecha" 
                                badge="*" 
                                required />

                            <flux:select 
                                wire:model="tipo_transaccion" 
                                label="Tipo" 
                                badge="*" 
                                required>
                                <flux:select.option value="Ingreso">💰 Ingreso</flux:select.option>
                                <flux:select.option value="Gasto">💸 Gasto</flux:select.option>
                                <flux:select.option value="Transferencia">🔄 Transferencia</flux:select.option>
                                <flux:select.option value="Ajuste">⚖️ Ajuste</flux:select.option>
                                <flux:select.option value="Otro">📝 Otro</flux:select.option>
                            </flux:select>

                            <flux:input 
                                wire:model="num_referencia" 
                                label="Nro. Referencia" 
                                placeholder="Ej: FACT-001" />

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                                <div class="mt-1">
                                    @if($estado === false)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200">
                                            ⏳ Pendiente
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200">
                                            ✅ Procesada
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <flux:input 
                            wire:model="descripcion" 
                            label="Descripción General" 
                            placeholder="Descripción de la transacción" />

                        {{-- 📋 TABLA DE ASIENTOS CONTABLES --}}
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <flux:icon.table-cells class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" />
                                    <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Asientos Contables</flux:heading>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        Partida Doble
                                    </span>
                                </div>
                                <flux:button 
                                    type="button" 
                                    wire:click="addAsiento" 
                                    variant="primary" 
                                    size="sm" 
                                    icon="plus">
                                    Añadir Asiento
                                </flux:button>
                            </div>

                            {{-- Información de ayuda --}}
                            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <div class="flex items-start">
                                    <flux:icon.information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5" />
                                    <div class="text-sm text-blue-800 dark:text-blue-200">
                                        <p class="font-medium">Principio de Partida Doble</p>
                                        <p>• Cada asiento puede tener solo Debe <strong>O</strong> Haber (no ambos)</p>
                                        <p>• El total de Debe debe ser igual al total de Haber</p>
                                        <p>• Se requieren mínimo 2 asientos para completar la transacción</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Cuenta <span class="text-red-500">*</span>
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Debe
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Haber
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Descripción
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Acción
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($asientos as $index => $asiento)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                {{-- Columna Cuenta --}}
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($errors->has('asientos.'.$index.'.cuenta_id'))
                                                        <flux:select 
                                                            wire:model="asientos.{{ $index }}.cuenta_id" 
                                                            placeholder="Seleccionar cuenta..."
                                                            variant="danger">
                                                            @foreach($cuentas_organizacion as $cuenta)
                                                                <flux:select.option value="{{ $cuenta->id }}">
                                                                    {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                                                                </flux:select.option>
                                                            @endforeach
                                                        </flux:select>
                                                    @else
                                                        <flux:select 
                                                            wire:model="asientos.{{ $index }}.cuenta_id" 
                                                            placeholder="Seleccionar cuenta...">
                                                            @foreach($cuentas_organizacion as $cuenta)
                                                                <flux:select.option value="{{ $cuenta->id }}">
                                                                    {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                                                                </flux:select.option>
                                                            @endforeach
                                                        </flux:select>
                                                    @endif
                                                </td>

                                                {{-- Columna Debe --}}
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if(floatval($asiento['haber'] ?? 0) > 0)
                                                        <flux:input 
                                                            type="number" 
                                                            step="0.01" 
                                                            min="0" 
                                                            wire:model.live="asientos.{{ $index }}.debe" 
                                                            placeholder="0.00"
                                                            readonly
                                                            class="bg-gray-100 dark:bg-gray-600" />
                                                    @else
                                                        <flux:input 
                                                            type="number" 
                                                            step="0.01" 
                                                            min="0" 
                                                            wire:model.live="asientos.{{ $index }}.debe" 
                                                            placeholder="0.00" />
                                                    @endif
                                                </td>

                                                {{-- Columna Haber --}}
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if(floatval($asiento['debe'] ?? 0) > 0)
                                                        <flux:input 
                                                            type="number" 
                                                            step="0.01" 
                                                            min="0" 
                                                            wire:model.live="asientos.{{ $index }}.haber" 
                                                            placeholder="0.00"
                                                            readonly
                                                            class="bg-gray-100 dark:bg-gray-600" />
                                                    @else
                                                        <flux:input 
                                                            type="number" 
                                                            step="0.01" 
                                                            min="0" 
                                                            wire:model.live="asientos.{{ $index }}.haber" 
                                                            placeholder="0.00" />
                                                    @endif
                                                </td>

                                                {{-- Columna Descripción --}}
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <flux:input 
                                                        wire:model="asientos.{{ $index }}.descripcion" 
                                                        placeholder="Detalle del asiento..." />
                                                </td>

                                                {{-- Columna Acción --}}
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if(count($asientos) > 1)
                                                        <flux:button 
                                                            type="button" 
                                                            wire:click="removeAsiento({{ $index }})" 
                                                            variant="danger" 
                                                            size="sm">
                                                            <flux:icon.trash class="w-4 h-4" />
                                                        </flux:button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        
                                        {{-- Fila de totales --}}
                                        @if(abs($this->diferencia) < 0.01)
                                            <tr class="bg-green-50 dark:bg-green-900/20">
                                        @else
                                            <tr class="bg-red-50 dark:bg-red-900/20">
                                        @endif
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100">
                                                📊 TOTALES:
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-blue-700 dark:text-blue-300">
                                                ${{ number_format($this->total_debe, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-blue-700 dark:text-blue-300">
                                                ${{ number_format($this->total_haber, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(abs($this->diferencia) < 0.01)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                        ✅ Balanceado
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                                        ⚠️ Dif: ${{ number_format($this->diferencia, 2) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(count($asientos) >= 2 && abs($this->diferencia) < 0.01)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                        ✅ Listo
                                                    </span>
                                                @elseif(count($asientos) < 2)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                                        ⚠️ Min. 2 asientos
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                                        ❌ No balanceado
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            @error('asientos') 
                                <div class="mt-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                            @error('partida_doble') 
                                <div class="mt-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror

                            {{-- Resumen del estado de la transacción --}}
                            @if(count($asientos) > 0)
                                @if(abs($this->diferencia) < 0.01 && count($asientos) >= 2 && $this->total_debe > 0)
                                    <div class="mt-4 p-4 border rounded-lg bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800">
                                @else
                                    <div class="mt-4 p-4 border rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800">
                                @endif
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-900 dark:text-gray-100">Estado de la Transacción:</p>
                                            <ul class="mt-1 space-y-1">
                                                <li class="flex items-center">
                                                    @if(count($asientos) >= 2)
                                                        <flux:icon.check-circle class="w-4 h-4 text-green-600 dark:text-green-400 mr-2" />
                                                        <span class="text-green-700 dark:text-green-300">Asientos suficientes ({{ count($asientos) }})</span>
                                                    @else
                                                        <flux:icon.exclamation-circle class="w-4 h-4 text-yellow-600 dark:text-yellow-400 mr-2" />
                                                        <span class="text-yellow-700 dark:text-yellow-300">Se requieren al menos 2 asientos (actual: {{ count($asientos) }})</span>
                                                    @endif
                                                </li>
                                                <li class="flex items-center">
                                                    @if(abs($this->diferencia) < 0.01)
                                                        <flux:icon.check-circle class="w-4 h-4 text-green-600 dark:text-green-400 mr-2" />
                                                        <span class="text-green-700 dark:text-green-300">Debe y Haber balanceados</span>
                                                    @else
                                                        <flux:icon.exclamation-circle class="w-4 h-4 text-red-600 dark:text-red-400 mr-2" />
                                                        <span class="text-red-700 dark:text-red-300">Diferencia: ${{ number_format(abs($this->diferencia), 2) }}</span>
                                                    @endif
                                                </li>
                                                <li class="flex items-center">
                                                    @if($this->total_debe > 0)
                                                        <flux:icon.check-circle class="w-4 h-4 text-green-600 dark:text-green-400 mr-2" />
                                                        <span class="text-green-700 dark:text-green-300">Montos ingresados</span>
                                                    @else
                                                        <flux:icon.exclamation-circle class="w-4 h-4 text-yellow-600 dark:text-yellow-400 mr-2" />
                                                        <span class="text-yellow-700 dark:text-yellow-300">Ingrese los montos de los asientos</span>
                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Botón de guardar --}}
                        <div class="flex justify-end space-x-3">
                            @if(count($asientos) >= 2 && abs($this->diferencia) < 0.01 && $this->total_debe > 0)
                                <flux:button type="submit" variant="primary" icon="check">
                                    @if($editando)
                                        Actualizar Transacción
                                    @else
                                        Guardar Transacción
                                    @endif
                                </flux:button>
                            @else
                                <flux:button type="button" variant="ghost" disabled>
                                    <flux:icon.lock-closed class="w-4 h-4 mr-2" />
                                    @if(count($asientos) < 2)
                                        Agregue más asientos
                                    @elseif(abs($this->diferencia) >= 0.01)
                                        Balance la transacción
                                    @elseif($this->total_debe == 0)
                                        Agregue montos
                                    @else
                                        Complete el formulario
                                    @endif
                                </flux:button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Mensajes de éxito --}}
            @if(session()->has('message'))
                <div class="flex items-center p-4 mb-4 text-sm font-medium rounded-lg bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800 shadow-lg">
                    <flux:icon.check-circle class="w-5 h-5 mr-2" />
                    <div>
                        <span class="font-semibold">¡Éxito!</span> {{ session('message') }}
                    </div>
                </div>
            @endif

            {{-- Mensajes de error --}}
            @if(session()->has('error'))
                <div class="flex items-center p-4 mb-4 text-sm font-medium rounded-lg bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800 shadow-lg">
                    <flux:icon.exclamation-triangle class="w-5 h-5 mr-2" />
                    <div>
                        <span class="font-semibold">Error:</span> {{ session('error') }}
                    </div>
                </div>
            @endif
            {{-- 📈 HISTORIAL DE TRANSACCIONES --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg" id="tabla-transacciones">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <flux:icon.document-text class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" />
                            <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Historial de Transacciones</flux:heading>
                        </div>
                        <div class="flex items-center space-x-4">
                            <flux:input 
                                wire:model.live="buscar" 
                                placeholder="Buscar por referencia o descripción..."
                                icon="magnifying-glass"
                                class="w-64"
                            />
                            <flux:select wire:model.live="contenido" class="w-48">
                                <flux:select.option value="num_referencia">Por Referencia</flux:select.option>
                                <flux:select.option value="descripcion">Por Descripción</flux:select.option>
                                <flux:select.option value="tipo_transaccion">Por Tipo</flux:select.option>
                            </flux:select>
                            @if($buscar)
                                <flux:button 
                                    wire:click="$set('buscar', '')"
                                    variant="ghost" 
                                    size="sm"
                                    icon="x-mark"
                                    tooltip="Limpiar búsqueda">
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Referencia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($transacciones as $transaccion)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($transaccion->fecha_transaccion)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($transaccion->num_referencia)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200">
                                                    {{ $transaccion->num_referencia }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $transaccion->descripcion ?: 'Sin descripción' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $tipo = $transaccion->tipo_transaccion;
                                            @endphp
                                            
                                            @if($tipo === 'Ingreso')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                    💰 {{ $tipo }}
                                                </span>
                                            @elseif($tipo === 'Gasto')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                                    💸 {{ $tipo }}
                                                </span>
                                            @elseif($tipo === 'Transferencia')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                                    🔄 {{ $tipo }}
                                                </span>
                                            @elseif($tipo === 'Ajuste')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                                    ⚖️ {{ $tipo }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                    📝 {{ $tipo }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            ${{ number_format($transaccion->asientosDiarios->sum('monto_debe'), 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($transaccion->estado === false)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                                    ⏳ Pendiente
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                    ✅ Procesada
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <div class="flex justify-end space-x-2">
                                                {{-- Botón de cambio de estado (solo para pendientes) --}}
                                                @if($transaccion->estado === false)
                                                    <div>
                                                        <flux:modal.trigger name="cambio_estado">
                                                            <flux:tooltip content="Marcar como procesada" placement="top">
                                                                <flux:button 
                                                                    wire:click="mostrarModalCambioEstado({{ $transaccion->id }})"
                                                                    variant="primary"
                                                                    icon="check-circle"
                                                                    class="bg-green-400 hover:bg-green-500">
                                                                </flux:button>
                                                            </flux:tooltip>
                                                        </flux:modal.trigger>
                                                    </div>
                                                @endif

                                                {{-- Botón de editar (solo para pendientes) --}}
                                                @if($transaccion->estado === false)
                                                <div>
                                                    <flux:tooltip content="Editar transacción" placement="top">
                                                        <flux:button 
                                                            wire:click="editarTransaccion({{ $transaccion->id }})"
                                                            wire:loading.attr="disabled"
                                                            variant="primary"
                                                            icon="pencil"
                                                            class="bg-emerald-400 hover:bg-emerald-500">
                                                        </flux:button>
                                                    </flux:tooltip>
                                                </div>

                                                    {{-- Botón de eliminar (solo para pendientes) --}}
                                                    <div>
                                                        <flux:modal.trigger name="eliminar-transaccion">
                                                            <flux:tooltip content="Eliminar transacción" placement="top">
                                                                <flux:button 
                                                                    wire:click="mostrarModalEliminar({{ $transaccion->id }})"
                                                                    variant="primary"
                                                                    icon="trash"
                                                                    class="bg-rose-400 hover:bg-rose-500">
                                                                </flux:button>
                                                            </flux:tooltip>
                                                        </flux:modal.trigger>
                                                    </div>
                                                @else
                                                    {{-- Indicador para transacciones procesadas --}}
                                                    <div>
                                                        <flux:tooltip content="Transacción procesada - No editable" placement="top">
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
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <flux:icon.document-text class="mx-auto w-12 h-12 text-gray-400 dark:text-gray-500" />
                                            <flux:heading size="lg" class="mt-2 text-gray-900 dark:text-gray-100">No hay transacciones</flux:heading>
                                            <flux:subheading class="text-gray-600 dark:text-gray-400">
                                                @if($selected_org_id)
                                                    No se encontraron transacciones para la organización {{ $selected_org_name }}.
                                                @else
                                                    Selecciona una organización para ver sus transacciones.
                                                @endif
                                            </flux:subheading>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    @if($transacciones->hasPages())
                <div class="mt-4">
                {{ $transacciones->links() }}
            </div>
        @endif
                </div>
            </div>
        </div>
    @endif

    {{-- 🔄 MODAL DE CONFIRMACIÓN CAMBIO DE ESTADO --}}
    <flux:modal name="cambio_estado" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Cambiar estado de transacción</flux:heading>
                
                <flux:text class="mt-2">
                    <p>¿Está seguro de que desea cambiar el estado de esta transacción?</p>
                    <p>Una vez que esté <span class="font-semibold text-green-600">procesada</span> no podrá realizar cambios.</p>
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button 
                    wire:click="confirmarCambioEstado"
                    x-on:click="$nextTick(() => $wire.dispatch('close-modal', 'cambio-estado'))"
                    type="submit" 
                    variant="primary">
                    Confirmar cambio
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- 🗑️ MODAL DE CONFIRMACIÓN ELIMINAR TRANSACCIÓN --}}
    <flux:modal name="eliminar-transaccion" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar transacción?</flux:heading>
                
                <flux:text class="mt-2">
                    <p>¿Está seguro de que desea eliminar esta transacción?</p>
                    <p>Esta acción no se puede revertir.</p>
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button 
                    wire:click="confirmarEliminarTransaccion"
                    x-on:click="$nextTick(() => $wire.dispatch('close-modal', 'eliminar-transaccion'))"
                    type="submit" 
                    variant="danger">
                    Eliminar transacción
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('transaccion-cargada', () => {
            // Scroll suave hacia el formulario de transacción
            const formulario = document.getElementById('formulario-transaccion');
            if (formulario) {
                formulario.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        });
    });
</script>