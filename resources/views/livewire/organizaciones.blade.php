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
        <flux:heading size="xl" class="text-gray-900 dark:text-gray-100">Gestión de Organizaciones</flux:heading>
        <flux:subheading class="text-gray-600 dark:text-gray-400">Administra las organizaciones empresariales del sistema contable</flux:subheading>
    </div>

    {{-- Panel principal de gestión --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <flux:icon.building-office class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" />
                    <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Lista de Organizaciones</flux:heading>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <div class="flex-1 sm:w-80">
                        <flux:input 
                            wire:model.live="buscar" 
                            icon="magnifying-glass" 
                            placeholder="Buscar organización..."
                            class="w-full">
                            <x-slot name="iconTrailing">
                                @if($buscar)
                                    <flux:button wire:click="vaciarFormulario" size="sm" variant="ghost" icon="x-mark"/>
                                @endif
                            </x-slot>
                        </flux:input>
                    </div>
                    
                    <flux:modal.trigger name="crearOrganizacion">
                        <flux:button 
                            wire:click="crearModal" 
                            variant="primary"
                            icon="plus">
                            Nueva Organización
                        </flux:button>
                    </flux:modal.trigger>
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
                                    <th wire:click="ordenar('nombre')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.building-office class="w-4 h-4 mr-2" />
                                            Nombre
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th wire:click="ordenar('nit')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.identification class="w-4 h-4 mr-2" />
                                            NIT
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th wire:click="ordenar('direccion')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.map-pin class="w-4 h-4 mr-2" />
                                            Dirección
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th wire:click="ordenar('telefono')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.phone class="w-4 h-4 mr-2" />
                                            Teléfono
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th wire:click="ordenar('created_at')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center">
                                            <flux:icon.calendar class="w-4 h-4 mr-2" />
                                            Registro
                                            <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($organizaciones as $organizacion)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                                                <span class="text-white font-medium text-sm">
                                                    {{ strtoupper(substr($organizacion->nombre, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $organizacion->nombre }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    Organización
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $organizacion->nit }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $organizacion->direccion ?? 'No especificada' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $organizacion->telefono ?? 'No especificado' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $organizacion->created_at->format('d/m/y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <flux:modal.trigger name="editarOrganizacion">
                                                <flux:tooltip content="Modificar" placement="top">
                                                    <flux:button 
                                                        wire:click="editarModal({{ $organizacion->id }})"
                                                        variant="primary"
                                                        icon="pencil"
                                                        class="bg-emerald-400 hover:bg-emerald-500">
                                                    </flux:button>
                                                </flux:tooltip>
                                            </flux:modal.trigger>
                                            
                                            <flux:modal.trigger name="eliminarOrganizacion">
                                                <flux:tooltip content="Eliminar" placement="top">
                                                    <flux:button 
                                                        wire:click="eliminarModal({{ $organizacion->id }})"
                                                        variant="primary"
                                                        icon="trash"
                                                        class="bg-rose-400 hover:bg-rose-500">
                                                    </flux:button>
                                                </flux:tooltip>
                                            </flux:modal.trigger>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <flux:icon.building-office class="w-12 h-12 text-gray-400 mb-4" />
                                            <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No hay organizaciones registradas</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-sm">Comienza creando tu primera organización</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($organizaciones->hasPages())
                    <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        {{ $organizaciones->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Crear Organización --}}
    <flux:modal name="crearOrganizacion" size="lg">
        <div class="space-y-4 p-4">
            <div>
                <flux:heading size="lg">Registrar organización</flux:heading>
                <flux:text class="mt-1 text-gray-600">Completa los campos para registrar una nueva organización.</flux:text>
            </div>
        
            <form wire:submit.prevent="crear" class="space-y-3">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div>
                        <flux:input 
                            class="w-full" 
                            badge="*" 
                            wire:model.live="nombre" 
                            label="Nombre" 
                            type="text" 
                            placeholder="Nombre de la organización"/>
                    </div>
                    <div>
                        <flux:input 
                            class="w-full" 
                            type="text" 
                            badge="*" 
                            wire:model.live="nit" 
                            label="NIT" 
                            placeholder="Número único"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <flux:input 
                            class="w-full" 
                            wire:model.live="direccion" 
                            label="Dirección" 
                            placeholder="Dirección de la organización" 
                            type="text" 
                        />
                    </div>
                    <div>
                        <flux:input 
                            class="w-full" 
                            wire:model.live="telefono" 
                            label="Teléfono" 
                            placeholder="Número de teléfono" 
                            type="text" 
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

    {{-- Modal Editar Organización --}}
    <flux:modal name="editarOrganizacion" size="lg">
        <div class="space-y-4 p-4">
            <div>
                <flux:heading size="lg">Modificar organización</flux:heading>
                <flux:text class="mt-1 text-gray-600">Modifica la información de la organización.</flux:text>
            </div>
        
            <form wire:submit.prevent="editar" class="space-y-3">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div>
                        <flux:input 
                            class="w-full" 
                            badge="*" 
                            wire:model.live="nombre" 
                            label="Nombre" 
                            type="text" 
                            placeholder="Nombre de la organización"/>
                    </div>
                    <div>
                        <flux:input 
                            class="w-full" 
                            type="text" 
                            badge="*" 
                            wire:model.live="nit" 
                            label="NIT" 
                            placeholder="Número único"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <flux:input 
                            class="w-full" 
                            wire:model.live="direccion" 
                            label="Dirección" 
                            placeholder="Dirección de la organización" 
                            type="text" 
                        />
                    </div>
                    <div>
                        <flux:input 
                            class="w-full" 
                            wire:model.live="telefono" 
                            label="Teléfono" 
                            placeholder="Número de teléfono" 
                            type="text" 
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

    {{-- Modal Eliminar Organización --}}
    <flux:modal name="eliminarOrganizacion" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Estás seguro de eliminar la organización?</flux:heading>
                <flux:text class="mt-2">
                    <p>Se eliminará la organización <span class="font-semibold text-red-600 dark:text-red-400">{{ $nombre }}</span>.</p>
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
</div>