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
        <flux:heading size="xl" class="text-gray-900 dark:text-gray-100">Gestión de Usuarios</flux:heading>
        <flux:subheading class="text-gray-600 dark:text-gray-400">Administra los usuarios del sistema contable empresarial</flux:subheading>
    </div>

    {{-- Panel principal de gestión --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <flux:icon.users class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" />
                    <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Lista de Usuarios</flux:heading>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <div class="flex-1 sm:w-80">
                        <flux:input 
                            wire:model.live="buscar" 
                            icon="magnifying-glass" 
                            placeholder="Buscar usuario..."
                            class="w-full">
                            <x-slot name="iconTrailing">
                                @if($buscar)
                                    <flux:button wire:click="vaciarFormulario" size="sm" variant="ghost" icon="x-mark"/>
                                @endif
                            </x-slot>
                        </flux:input>
                    </div>
                    
                    <flux:modal.trigger name="registrarUsuario">
                        <flux:button 
                            wire:click="crearModal" 
                            variant="primary"
                            icon="plus">
                            Nuevo Usuario
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
                            <th wire:click="ordenar('nombres')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center">
                                    <flux:icon.user class="w-4 h-4 mr-2" />
                                    Usuario
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
                            <th wire:click="ordenar('email')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center">
                                    <flux:icon.envelope class="w-4 h-4 mr-2" />
                                    Correo
                                    <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                </div>
                            </th>
                            <th wire:click="ordenar('estado')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center">
                                    <flux:icon.shield-check class="w-4 h-4 mr-2" />
                                    Estado
                                    <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                </div>
                            </th>
                            <th wire:click="ordenar('rol_id')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center">
                                    <flux:icon.key class="w-4 h-4 mr-2" />
                                    Rol
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
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($usuarios as $usuario)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-amber-200 dark:bg-amber-900 flex items-center justify-center">
                                        <span class="text-red-900 dark:text-indigo-300 font-medium">
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
                                    {{ $usuario->estado === 'Activo' ? 
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                        ($usuario->estado === 'Inactivo' ? 
                                            'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : 
                                            'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200') }}">
                                    {{ $usuario->estado }}
                                </span>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $usuario->created_at->format('d/m/y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <div>
                                    <flux:modal.trigger name="editarUsuario">
                                        <flux:tooltip content="Modificar" placement="top">
                                            <flux:button 
                                                wire:click="editarModal({{ $usuario->id }})"
                                                variant="primary"
                                                icon="pencil"
                                                class="bg-emerald-400 hover:bg-emerald-500">
                                            </flux:button>
                                        </flux:tooltip>
                                    </flux:modal.trigger>
                                </div>
                                <div>
                                    <flux:modal.trigger name="eliminarUsuario">
                                        <flux:tooltip content="Eliminar"            placement="top">
                                            <flux:button 
                                            wire:click="eliminarModal({{ $usuario->id }})"
                                            variant="primary"
                                            icon="trash"
                                            class="bg-rose-400 hover:bg-rose-500">
                                            </flux:button>
                                        </flux:tooltip>
                                    </flux:modal.trigger>
                                </div>
                                <div>
                                    @if($usuario->id === auth()->user()->id)
                                        <flux:tooltip content="No se puede bloquear/desbloquear a sí mismo" placement="top">
                                            <flux:button 
                                                variant="primary"
                                                icon="lock-closed"
                                                class="bg-gray-400 hover:bg-gray-500 cursor-not-allowed"
                                                title="No se puede bloquear/desbloquear a sí mismo">
                                            </flux:button>
                                        </flux:tooltip>
                                    @elseif($usuario->estado !== 'Bloqueado')
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

    <flux:modal name="registrarUsuario" size="lg">
    <div class="space-y-4 p-4">
        <div>
            <flux:heading size="lg">Registrar usuario</flux:heading>
            <flux:text class="mt-1 text-gray-600">Completa los campos para registrar un nuevo usuario.</flux:text>
        </div>
        
        <form wire:submit.prevent="registrarEditar" class="space-y-3">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                <div>
                    <flux:input 
                        class="w-full" 
                        badge="*" 
                        wire:model.live="nombres" 
                        label="Nombres" 
                        type="text" 
                        placeholder="Nombres completos"
                    />
                </div>
                <div>
                    <flux:input 
                        class="w-full" 
                        type="text" 
                        badge="*" 
                        wire:model.live="apellidos" 
                        label="Apellidos" 
                        placeholder="Apellidos completos"
                    />
                </div>
                <div class="mt-2">
                    <flux:input 
                        class="w-full" 
                        type="text"
                        wire:model.live="telefono" 
                        label="Teléfono" 
                placeholder="Número de teléfono"
                />
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <flux:input 
                            class="w-full" 
                            badge="*" 
                            wire:model.live="email" 
                            label="Correo" 
                            placeholder="correo@ejemplo.com" 
                            :readonly="$activo"
                        />
                    </div>
                    <div>
            <flux:select 
                class="w-full" 
                wire:model.live="rol_id" 
                label="Tipo de usuario" 
                badge="*"
                >
            <flux:select.option value="" readonly>Seleccione una opcion</flux:select.option>
            @foreach($roles as $rol)
                <flux:select.option value="{{$rol->id}}">{{$rol->nombre}}</flux:select.option>
            @endforeach
            </flux:select>
        </div>
    </div>
            <flux:input 
                class="w-full" 
                badge="*" 
                wire:model.live="username" 
                label="Username" 
                placeholder="nombre de usuario" 
                :readonly="$activo"
            />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <flux:input 
                        class="w-full" 
                        badge="*" 
                        wire:model.live="password" 
                        label="Contraseña" 
                        placeholder="••••••••" 
                        type="password" 
                        :disabled="$activo"
                    />
                </div>
                <div>
                    <flux:input 
                        class="w-full" 
                        badge="*" 
                        wire:model.live="password_confirmation" 
                        label="Confirmar contraseña" 
                        placeholder="••••••••" 
                        type="password" 
                        :disabled="$activo"
                    />
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <flux:button type="submit" variant="primary" class="w-full md:w-auto">
                    {{ $usuario_id ? 'Actualizar' : 'Registrar' }}
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>

    <flux:modal name="editarUsuario" size="lg">
        <div class="space-y-4 p-4">
        <div>
            <flux:heading size="lg">Modificar usuario</flux:heading>
            <flux:text class="mt-2">Solo puede modificar algunos campos.</flux:text>
        </div>
        <form wire:submit.prevent="registrarEditar" class="space-y-3">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                <div>
                    <flux:input 
                        class="w-full" 
                        badge="*" 
                        wire:model.live="nombres" 
                        label="Nombres" 
                        type="text" 
                        placeholder="Nombres completos"
                    />
                </div>
                <div>
                    <flux:input 
                        class="w-full" 
                        type="text" 
                        badge="*" 
                        wire:model.live="apellidos" 
                        label="Apellidos" 
                        placeholder="Apellidos completos"
                    />
                </div>
                <div class="mt-2">
                    <flux:input 
                        class="w-full" 
                        type="text"
                        wire:model.live="telefono" 
                        label="Teléfono" 
                placeholder="Número de teléfono"
                />
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <flux:input 
                            class="w-full" 
                            badge="*" 
                            wire:model.live="email" 
                            label="Correo" 
                            placeholder="correo@ejemplo.com" 
                            :readonly="$activo"
                        />
                    </div>
                    <div>
            <flux:select 
                class="w-full" 
                wire:model.live="rol_id" 
                label="Tipo de usuario" 
                badge="*"
            >
            @foreach($roles as $rol)
                <flux:select.option value="{{$rol->id}}">{{$rol->nombre}}</flux:select.option>
            @endforeach
            </flux:select>
        </div>
    </div>
            <flux:input 
                class="w-full" 
                badge="*" 
                wire:model.live="username" 
                label="Username" 
                placeholder="nombre de usuario" 
                :readonly="$activo"
            />
            <div class="flex justify-end mt-4">
                <flux:button type="submit" variant="primary" class="w-full md:w-auto">
                    {{ $usuario_id ? 'Actualizar' : 'Registrar' }}
                </flux:button>
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

            <flux:button wire:click="eliminar" variant="danger">Eliminar</flux:button>
        </div>
    </div>
</flux:modal>
</div>