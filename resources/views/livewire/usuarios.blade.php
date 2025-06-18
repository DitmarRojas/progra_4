<div>
    <x-auth-header 
        :title="__('Gestión de usuarios')" 
        :description="__('Visualiza y administra los usuarios de la aplicación')"
    />
    
    <x-auth-session-status class="mx-auto max-w-7xl mb-6" :status="session('message')" />

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Lista de usuarios</h1>
            <div class="flex w-full sm:w-auto">
                <flux:input wire:model.live="buscar" icon="magnifying-glass" placeholder="Buscar usuario">
                    <x-slot name="iconTrailing">
                        <flux:button wire:click="vaciarFormulario" size="sm" variant="subtle" icon="x-mark"/>
                    </x-slot>
                </flux:input>
            </div>
            <flux:modal.trigger name="usuarioModal">
                <flux:button wire:click="crear" variant="primary"
                icon="plus"
                class="w-full sm:w-auto bg-green-700 hover:bg-green-800"> Nuevo usuario </flux:button>
            </flux:modal.trigger>
        </div>

        <!-- Tabla de usuarios -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full min-w-max">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th wire:click="ordenar('nombres')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                            <th wire:click="ordenar('telefono')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teléfono</th>
                            <th wire:click="ordenar('email')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Correo</th>
                            <th wire:click="ordenar('rol_id')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($usuarios as $usuario)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                        <span class="text-indigo-600 dark:text-indigo-300 font-medium">
                                            {{ strtoupper(substr($usuario->nombres, 0, 1)) }}{{ strtoupper(substr($usuario->apellidos, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $usuario->nombres }} {{ $usuario->apellidos }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $usuario->username }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $usuario->telefono }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $usuario->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $usuario->roles->nombre === 'Administrador' ? 
                                        'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 
                                        ($usuario->roles->nombre === 'Contador' ? 
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200') }}">
                                    {{ $usuario->roles->nombre }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <div>
                                    <flux:modal.trigger name="usuarioModal">
                                        <flux:button 
                                            wire:click="editar({{ $usuario->id }})"
                                            variant="primary"
                                            icon="pencil"
                                            class="bg-violet-500 hover:bg-violet-600">
                                        </flux:button>
                                    </flux:modal.trigger>
                                </div>
                                <div>
                                    <flux:modal.trigger name="eliminarUsuario">
                                        <flux:button 
                                        wire:click="eliminarUsuario({{ $usuario->id }})"
                                        variant="primary"
                                        icon="trash"
                                        class="bg-red-500 hover:bg-red-600"
                                        >
                                        </flux:button>
                                    </flux:modal.trigger>
                                </div>
                            </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($usuarios->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                {{ $usuarios->links() }}
            </div>
            @endif
        </div>
    </div>

    <flux:modal name="usuarioModal" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Registrar usuario</flux:heading>
            <flux:text class="mt-2">Completa los campos para registrar un nuevo usuario.</flux:text>
        </div>
        <form wire:submit.prevent="registrarEditar">
        <flux:input class="mb-1" badge="*" wire:model.live="nombres" label="Nombres" type="text" placeholder="nombres"/>
        <flux:input class="mb-1" type="text" badge="*" wire:model.live="apellidos" label="Apellidos" placeholder="apellidos" />
        <flux:input class="mb-1" wire:model.live="telefono" label="Telefono" placeholder="telefono" />
        <flux:input class="mb-1" badge="*" wire:model.live="email" label="Correo" placeholder="correo electronico" :readonly="$activo"/>
        <flux:input class="mb-1" badge="*" wire:model.live="username" label="Username" placeholder="nombre de usuario" :readonly="$activo"/>
        <flux:input class="mb-1" badge="*" wire:model.live="password" label="Contraseña" placeholder="contraseña" type="password" :disabled="$activo"/>
        <flux:input class="mb-1" badge="*" wire:model.live="password_confirmation" label="Confirmar contraseña" placeholder="confirmar contraseña" type="password" 
        :disabled="$activo"/>
        <flux:select class="mb-1" wire:model.live="rol_id" label="Tipo de usuario" badge="*">
            <flux:select.option value="0">Selecciona un tipo de usuario</flux:select.option>
            <flux:select.option value="1">Administrador</flux:select.option>
            <flux:select.option value="2">Contador</flux:select.option>
            <flux:select.option value="3">Invitado</flux:select.option>
        </flux:select>
        <div class="flex mt-2">
            <flux:button type="submit" variant="primary">{{ $usuario_id ? 'Actualizar' : 'Registrar' }}</flux:button>
        </div>
    </form>
    </div>
    </flux:modal>

    <flux:modal name="eliminarUsuario" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">¿Estas seguro de eliminar a <span class=" text-md text-red-500">{{ $nombres }}</span>?
            </flux:heading>

            <flux:text class="mt-2">
                <p>Esta acción no puede revertirse.</p>
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">Cancelar</flux:button>
            </flux:modal.close>

            <flux:button wire:click="eliminarUsuario" variant="danger">Eliminar</flux:button>
        </div>
    </div>
</flux:modal>
</div>