<div>
        <x-action-message 
            on="alertaCuenta" 
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

    <x-auth-header 
        :title="__('Gestión de cuentas')" 
        :description="__('Visualiza y administra las cuentas de la aplicación')"
    />

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Lista de cuentas</h1>
            <div class="flex w-full sm:w-auto">
                <flux:input wire:model.live="buscar" icon="magnifying-glass" placeholder="Buscar cuenta">
                    <x-slot name="iconTrailing">
                        <flux:button wire:click="vaciarFormulario" size="sm" variant="subtle" icon="x-mark"/>
                    </x-slot>
                </flux:input>
            </div>
                <flux:modal.trigger name="crearCuenta">
                    <flux:button wire:click="crearModal" 
                    variant="primary"
                    icon="plus"
                    class="w-full sm:w-auto bg-emerald-700 hover:bg-emerald-800"> Nueva cuenta 
                    </flux:button>
                </flux:modal.trigger>
        </div>

        <!-- Tabla de organizaciones -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full min-w-max">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th wire:click="ordenar('codigo')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Codigo</th>
                            <th wire:click="ordenar('nombre')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                            <th wire:click="ordenar('tipo')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                            <th wire:click="ordenar('nivel')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nivel</th>
                            <th wire:click="ordenar('created_at')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha de registro</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($cuentas as $cuenta)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $cuenta->codigo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $cuenta->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $cuenta->tipo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $cuenta->nivel }}
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
                                {{-- <div>
                                    @if($usuario->estado !== 'Bloqueado')
                                        <flux:tooltip content="Bloquear" placement="top">
                                            <flux:button 
                                                wire:click="bloquearUsuario({{ $usuario->id }})"
                                                variant="primary"
                                                icon="lock-closed"
                                                class="bg-yellow-400 hover:bg-yellow-500"
                                                title="Bloquear usuario">
                                            </flux:button>
                                        </flux:tooltip>
                                    @else
                                        <flux:tooltip content="Desbloquear" placement="top">
                                            <flux:button 
                                            wire:click="desbloquearUsuario({{ $usuario->id }})"
                                            variant="primary"
                                            icon="lock-open"
                                            class="bg-green-400 hover:bg-green-500"
                                            title="Desbloquear usuario">
                                            </flux:button>
                                        </flux:tooltip>
                                    @endif
                                </div> --}}
                            </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No hay cuentas registradas.
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

    <livewire:cuentas-por-org/>

    <flux:modal name="crearCuenta" size="lg" :dismissible="false">
        <x-action-message 
            on="alertaCuenta" 
            class="flex items-center p-4 mb-4 text-sm font-medium rounded-lg bg-green-50 text-green-800 border border-green-200 shadow-lg pointer-events-auto">
            <div>
                <span class="font-semibold">¡Éxito!</span> {{ session('message') }}
            </div>
        </x-action-message>
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

    <flux:modal name="eliminarCuenta" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Estas seguro de eliminar?
                </flux:heading>
                <flux:text class="mt-2">
                    <p>Se eliminará la cuenta <span class=" text-red-400">{{ $nombre }}</span>.</p>
                    <flux:spacer/>
                    
                    <p>Esta acción no puede revertirse.</p>
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
</div>