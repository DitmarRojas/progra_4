<div>
    {{-- Alertas de éxito o error --}}
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-center">
            <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" />
            <div class="flex-1">
                <p class="font-semibold text-green-800 dark:text-green-200">¡Éxito!</p>
                <p class="text-green-700 dark:text-green-300">{{ session('message') }}</p>
            </div>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-center">
            <flux:icon.exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400 mr-3" />
            <div class="flex-1">
                <p class="font-semibold text-red-800 dark:text-red-200">¡Error!</p>
                <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Header principal --}}
    <div class="mb-8">
        <flux:heading size="xl" class="text-gray-900 dark:text-gray-100">Gestión de Roles</flux:heading>
        <flux:subheading class="text-gray-600 dark:text-gray-400">Administra los roles y permisos del sistema contable</flux:subheading>
    </div>

    {{-- Panel principal de gestión --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <flux:icon.key class="w-5 h-5 mr-2 text-amber-600 dark:text-amber-400" />
                    <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Lista de Roles</flux:heading>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <div class="flex-1 sm:w-80">
                        <flux:input 
                            wire:model.live="buscar" 
                            icon="magnifying-glass" 
                            placeholder="Buscar rol..."
                            class="w-full">
                            <x-slot name="iconTrailing">
                                @if($buscar ?? false)
                                    <flux:button wire:click="vaciarFormulario" size="sm" variant="ghost" icon="x-mark"/>
                                @endif
                            </x-slot>
                        </flux:input>
                    </div>
                    
                    <flux:modal.trigger name="crearRol">
                        <flux:button 
                            wire:click="crearModal" 
                            variant="primary"
                            icon="plus">
                            Nuevo Rol
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center">
                                    <flux:icon.key class="w-4 h-4 mr-2" />
                                    Rol
                                    <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <flux:icon.document-text class="w-4 h-4 mr-2" />
                                    Descripción
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <flux:icon.users class="w-4 h-4 mr-2" />
                                    Usuarios
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <flux:icon.calendar class="w-4 h-4 mr-2" />
                                    Creado
                                </div>
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($roles ?? [] as $rol)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 bg-gradient-to-r from-amber-500 to-orange-600 rounded-lg flex items-center justify-center text-white font-bold">
                                                {{ substr($rol->nombre ?? 'R', 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $rol->nombre ?? 'Sin nombre' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $rol->descripcion ?? 'Sin descripción' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                        {{ $rol->usuarios_count ?? 0 }} usuarios
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <flux:icon.calendar class="w-4 h-4 mr-2" />
                                        {{ $rol->created_at ? $rol->created_at->format('d/m/Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center space-x-2">
                                        <div>
                                            <flux:modal.trigger name="editarRol">
                                                <flux:tooltip content="Modificar" placement="top">
                                                    <flux:button 
                                                        wire:click="editarModal({{ $rol->id ?? 0 }})"
                                                        variant="primary"
                                                        icon="pencil"
                                                        class="bg-emerald-400 hover:bg-emerald-500">
                                                    </flux:button>
                                                </flux:tooltip>
                                            </flux:modal.trigger>
                                        </div>
                                        
                                        <div>
                                            <flux:modal.trigger name="eliminarRol">
                                                <flux:tooltip content="Eliminar" placement="top">
                                                    <flux:button 
                                                        wire:click="eliminarModal({{ $rol->id ?? 0 }})"
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
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <flux:icon.key class="mx-auto w-12 h-12 text-gray-400 dark:text-gray-500" />
                                    <flux:heading size="lg" class="mt-2 text-gray-900 dark:text-gray-100">No hay roles</flux:heading>
                                    <flux:subheading class="text-gray-600 dark:text-gray-400">Comienza creando tu primer rol del sistema.</flux:subheading>
                                    <div class="mt-4">
                                        <flux:modal.trigger name="crearRol">
                                            <flux:button 
                                                wire:click="crearModal" 
                                                variant="primary"
                                                icon="plus">
                                                Nuevo Rol
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
