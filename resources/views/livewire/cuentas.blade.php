<div>
    <x-action-message 
            on="alertas" 
            class="flex items-center p-4 mb-4 text-sm font-medium rounded-lg bg-green-50 text-green-800 border border-green-200 shadow-lg pointer-events-auto">
            <div>
                <span class="font-semibold">¡Éxito!</span> {{ session('message') }}
            </div>
    </x-action-message>

    {{-- Header principal --}}
    <div class="mb-8">
        <flux:heading size="xl" class="text-gray-900 dark:text-gray-100">Gestión de Cuentas</flux:heading>
        <flux:subheading class="text-gray-600 dark:text-gray-400">Administra las cuentas contables del sistema empresarial</flux:subheading>
    </div>

    {{-- Sección de selección de organización --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex items-center">
                <flux:icon.building-office class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" />
                <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Selección de Organización</flux:heading>
            </div>
        </div>
        <div class="p-6">
            <div class="w-full sm:w-120">
                <flux:input.group label="Seleccione una organización">
                    <flux:select wire:model.live="organizacion_id" class="max-w-fit">
                        <flux:select.option value="">Seleccione una ORG...</flux:select.option>
                        @forelse($organizaciones as $org)
                            <flux:select.option value="{{ $org->id }}">
                            {{ $org->nombre }}
                            </flux:select.option>
                        @empty
                            <flux:select.option value="" disabled>No hay organizaciones disponibles</flux:select.option>
                        @endforelse
                    </flux:select>
                    <flux:input wire:model.live="buscarOrg" placeholder="Ingrese el NIT" />
                </flux:input.group>
            </div>
        </div>
    </div>

    {{-- Panel principal de gestión --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <flux:icon.calculator class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" />
                    <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Lista de Cuentas Contables</flux:heading>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 my-4">
                    <div>
                        @if($organizacion)
                        <flux:modal.trigger name="crearCuentasOrg">
                            <flux:button class="w-full bg-purple-800 hover:bg-purple-700" variant="primary">Asociar cuentas</flux:button>
                        </flux:modal.trigger>
                        @else
                        <flux:button class="w-full bg-purple-800 hover:bg-purple-700" variant="primary" disabled>Asociar cuentas</flux:button>
                        @endif
                    </div>
                    
                    <div class="w-full">
                        <flux:input wire:model.live="buscar" icon="magnifying-glass" placeholder="Buscar cuenta">
                            <x-slot name="iconTrailing">
                                <flux:button wire:click="vaciarFormulario" size="sm" variant="subtle" icon="x-mark"/>
                            </x-slot>
                        </flux:input>
                    </div>
                    <div class="w-full">
                        <flux:modal.trigger name="crearCuenta">
                            <flux:button wire:click="crearModal" 
                            variant="primary"
                            icon="plus"
                            class="w-full bg-emerald-700 hover:bg-emerald-800"> Nueva cuenta 
                            </flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="mx-auto">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-max">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th wire:click="" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <flux:icon.check-circle class="w-4 h-4 mr-2" />
                                            Seleccionar
                                        </div>
                                    </th>
                                    <th wire:click="ordenar('id')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.hashtag class="w-4 h-4 mr-2" />
                                            ID
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th wire:click="ordenar('codigo')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.document-text class="w-4 h-4 mr-2" />
                                            Código
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th wire:click="ordenar('nombre')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.user class="w-4 h-4 mr-2" />
                                            Nombre
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th wire:click="ordenar('tipo')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.tag class="w-4 h-4 mr-2" />
                                            Tipo
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th wire:click="ordenar('nivel')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.bars-3-bottom-left class="w-4 h-4 mr-2" />
                                            Nivel
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th wire:click="ordenar('created_at')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.calendar class="w-4 h-4 mr-2" />
                                            Fecha de registro
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($cuentas as $cuenta)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($organizacion)
                                        <flux:checkbox type="checkbox" wire:model.live="seleccionados" value="{{ $cuenta->id }}" wire:key="checkbox-{{ $cuenta->id }} "/>
                                        @else
                                        <flux:checkbox type="checkbox" disabled />
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $cuenta->id }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ $cuenta->codigo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                                                <span class="text-white font-medium text-sm">
                                                    {{ strtoupper(substr($cuenta->nombre, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $cuenta->nombre }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    Cuenta contable
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $cuenta->tipo === 'Activo' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                               ($cuenta->tipo === 'Pasivo' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                ($cuenta->tipo === 'Patrimonio' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 
                                                 ($cuenta->tipo === 'Ingresos' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                                  'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'))) }}">
                                            {{ $cuenta->tipo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            Nivel {{ $cuenta->nivel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $cuenta->created_at->format('d/m/y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <div>
                                            <flux:modal.trigger name="editarCuenta">
                                                <flux:tooltip content="Modificar" placement="top">
                                                    <flux:button 
                                                        wire:click="editarModal({{ $cuenta->id }})"
                                                        variant="primary"
                                                        icon="pencil"
                                                        class="bg-emerald-400 hover:bg-emerald-500">
                                                    </flux:button>
                                                </flux:tooltip>
                                            </flux:modal.trigger>
                                        </div>
                                        <div>
                                            <flux:modal.trigger name="eliminarCuenta">
                                                <flux:tooltip content="Eliminar" placement="top">
                                                    <flux:button 
                                                    wire:click="eliminarModal({{ $cuenta->id }})"
                                                    variant="primary"
                                                    icon="trash"
                                                    class="bg-rose-400 hover:bg-rose-500">
                                                    </flux:button>
                                                </flux:tooltip>
                                            </flux:modal.trigger>
                                        </div>
                                    </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <flux:icon.calculator class="w-12 h-12 text-gray-400 mb-4" />
                                            <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No hay cuentas registradas</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-sm">Comienza creando tu primera cuenta contable</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($cuentas->hasPages())
                    <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        {{ $cuentas->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Crear Cuenta --}}
    <flux:modal name="crearCuenta" size="lg" :dismissible="false">
        <div class="space-y-4 p-4">
            <div>
                <flux:heading size="lg">Registrar cuenta</flux:heading>
                <flux:text class="mt-1 text-gray-600">Completa los campos para registrar una nueva cuenta.</flux:text>
            </div>
        
            <form wire:submit.prevent="crear" class="space-y-3">
                    <div class="w-full">
                        <flux:radio.group wire:model="tipo" label="Tipo de cuenta" variant="segmented" size="sm" badge="*">
                            <flux:radio value="Activo" label="Activo" />
                            <flux:radio value="Pasivo" label="Pasivo" />
                            <flux:radio value="Patrimonio" label="Patrimonio" />
                            <flux:radio value="Ingresos" label="Ingresos" />
                            <flux:radio value="Egresos" label="Egresos" />
                        </flux:radio.group>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <flux:textarea 
                        class="w-full" 
                        rows="1"
                        type="text" 
                        badge="*" 
                        wire:model.live="nombre" 
                        label="Nombre" 
                        placeholder="Nombre de la cuenta"/>
                    </div>
                    <div>
                        <flux:input 
                            class="w-full" 
                            badge="*" 
                            wire:model.live="codigo" 
                            label="Codigo" 
                            type="text" 
                            placeholder="Codigo de la cuenta"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <flux:textarea 
                            class="w-full" 
                            rows="2"
                            type="text" 
                            wire:model="descripcion" 
                            label="Descripcion" 
                            placeholder="Descripcion opcional"/>
                    </div>
                    <div>
                        <flux:input 
                            class="w-full" 
                            wire:model.live="nivel" 
                            label="Nivel" 
                            placeholder="Nivel de la cuenta" 
                            type="number"
                        />
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <flux:button type="submit" variant="primary" class="w-full md:w-auto">
                        Registrar
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Modal Editar Cuenta --}}
    <flux:modal name="editarCuenta" size="lg">
        <div class="space-y-4 p-4">
            <div>
                <flux:heading size="lg">Modificar cuenta</flux:heading>
                <flux:text class="mt-1 text-gray-600">Modifica la informacion de la cuenta</flux:text>
            </div>
        
            <form wire:submit.prevent="editar" class="space-y-3">
                <div class="w-full">
                    <flux:radio.group wire:model="tipo" label="Tipo de cuenta" variant="segmented" size="sm" badge="*">
                        <flux:radio value="Activo" label="Activo" />
                        <flux:radio value="Pasivo" label="Pasivo" />
                        <flux:radio value="Patrimonio" label="Patrimonio" />
                        <flux:radio value="Ingresos" label="Ingresos" />
                        <flux:radio value="Egresos" label="Egresos" />
                    </flux:radio.group>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="col-span-2">
                    <flux:textarea 
                    class="w-full" 
                    rows="1"
                    type="text" 
                    badge="*" 
                    wire:model.live="nombre" 
                    label="Nombre" 
                    placeholder="Nombre de la cuenta"/>
                </div>
                <div>
                    <flux:input 
                        class="w-full" 
                        badge="*" 
                        wire:model.live="codigo" 
                        label="Codigo" 
                        type="text" 
                        placeholder="Codigo de la cuenta"/>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="col-span-2">
                    <flux:textarea 
                        class="w-full" 
                        rows="2"
                        type="text" 
                        wire:model.live="descripcion" 
                        label="Descripcion" 
                        placeholder="Descripcion opcional"/>
                </div>
                <div>
                    <flux:input 
                        class="w-full" 
                        wire:model="nivel" 
                        label="Nivel" 
                        placeholder="Nivel de la cuenta" 
                        type="number" 
                        min="1" 
                        max="5" 
                    />
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <flux:button type="submit" variant="primary" class="w-full md:w-auto">
                    Modificar
                </flux:button>
            </div>
            </form>
        </div>
    </flux:modal>

    {{-- Modal Eliminar Cuenta --}}
    <flux:modal name="eliminarCuenta" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Estás seguro de eliminar la cuenta?</flux:heading>
                <flux:text class="mt-2">
                    <p>Se eliminará la cuenta <span class="font-semibold text-red-600 dark:text-red-400">{{ $nombre }}</span>.</p>
                    <p class="mt-2">Esta acción no puede revertirse.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button wire:click="eliminar" variant="danger">Eliminar</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Asociar Cuentas --}}
    <flux:modal name="crearCuentasOrg" variant="flyout" size="md">
        <form wire:submit.prevent="asociarCuentas">
        <div class="space-y-6">
            <div>
                <flux:heading size="xl">Lista de cuentas marcadas</flux:heading>
                @if($organizacion)
                    <flux:text class="mt-2 text-violet-600 dark:text-violet-400">{{$organizacion->nombre}} - NIT: {{$organizacion->nit}}</flux:text>
                @else
                    <flux:text class="mt-2 text-rose-600 dark:text-rose-400">¡Selecciona una organización!</flux:text>
                @endif
            </div>
    <div class="space-y-4">
        <flux:text class="mt-2 text-violet-600 dark:text-violet-400">Verifica el código y nombre de las cuentas que marcaste.</flux:text>
        @if(empty($seleccionados))
                <div class="text-center py-8">
                    <flux:icon.exclamation-circle class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <flux:text class="text-gray-500">No hay cuentas seleccionadas.</flux:text>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($cuentasSeleccionadas as $cuenta)
                    <flux:badge color="lime" class="mr-2 mb-2">
                        {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                        <flux:badge.close wire:click="eliminarCuentaSeleccionada({{ $cuenta->id }})" />
                    </flux:badge>
                    @endforeach
                </div>
            @endif
    </div>

        <div class="flex">
            <flux:spacer />
                <flux:button type="submit" variant="primary">Confirmar</flux:button>
            </div>
        </div>
        </form>
    </flux:modal>
</div>