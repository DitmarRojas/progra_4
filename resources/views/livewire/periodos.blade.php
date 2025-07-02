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
        <flux:heading size="xl" class="text-gray-900 dark:text-gray-100">Gestión de Períodos Contables</flux:heading>
        <flux:subheading class="text-gray-600 dark:text-gray-400">Administra los períodos fiscales y de ejercicio contable</flux:subheading>
    </div>

    {{-- Panel principal de gestión --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <flux:icon.calendar-days class="w-5 h-5 mr-2 text-emerald-600 dark:text-emerald-400" />
                    <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Lista de Períodos</flux:heading>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <div class="flex-1 sm:w-80">
                        <flux:input 
                            wire:model.live="buscar" 
                            icon="magnifying-glass" 
                            placeholder="Buscar período..."
                            class="w-full">
                            <x-slot name="iconTrailing">
                                @if($buscar)
                                    <flux:button wire:click="vaciarFormulario" size="sm" variant="ghost" icon="x-mark"/>
                                @endif
                            </x-slot>
                        </flux:input>
                    </div>
                    
                    <flux:modal.trigger name="crearPeriodo">
                        <flux:button 
                            wire:click="crearModal" 
                            variant="primary"
                            icon="plus">
                            Nuevo Período
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
                            <th wire:click="ordenar('nombre')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center">
                                    <flux:icon.calendar-days class="w-4 h-4 mr-2" />
                                    Período
                                    <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                </div>
                            </th>
                            <th wire:click="ordenar('fecha_inicio')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center">
                                    <flux:icon.calendar class="w-4 h-4 mr-2" />
                                    Fecha Inicio
                                    <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                </div>
                            </th>
                            <th wire:click="ordenar('fecha_fin')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center">
                                    <flux:icon.calendar class="w-4 h-4 mr-2" />
                                    Fecha Fin
                                    <flux:icon.chevron-up-down class="w-4 h-4 ml-1" />
                                </div>
                            </th>
                            <th wire:click="ordenar('organizacion_id')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Organización</th>
                            <th wire:click="ordenar('estado')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($periodos as $periodo)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $periodo->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-violet-100 text-violet-800 dark:bg-purple-800 dark:text-purple-100">
                                    {{ $periodo->fecha_inicio}}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-yellow-800 dark:text-yellow-100">
                                    {{ $periodo->fecha_fin }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $periodo->organizaciones->nombre ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $periodo->estado === 'Abierto' ? 
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                        ($periodo->estado === 'Cerrado' ? 
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                            'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200') }}">
                                    {{ $periodo->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <div>
                                    <flux:modal.trigger name="editarPeriodo">
                                        <flux:tooltip content="Modificar" placement="top">
                                            <flux:button 
                                                wire:click="editarModal({{ $periodo->id }})"
                                                variant="primary"
                                                icon="pencil"
                                                class="bg-emerald-400 hover:bg-emerald-500">
                                            </flux:button>
                                        </flux:tooltip>
                                    </flux:modal.trigger>
                                </div>
                                <div>
                                    <flux:modal.trigger name="eliminarPeriodo">
                                        <flux:tooltip content="Eliminar"            placement="top">
                                            <flux:button 
                                            wire:click="eliminarModal({{ $periodo->id }})"
                                            variant="primary"
                                            icon="trash"
                                            class="bg-rose-400 hover:bg-rose-500">
                                            </flux:button>
                                        </flux:tooltip>
                                    </flux:modal.trigger>
                                </div>
                                <div>
                                    <flux:tooltip content="Sumas y Saldos" placement="top">
                                        <flux:button 
                                            wire:click="verSumasYSaldos({{ $periodo->id }})"
                                            variant="primary"
                                            icon="document-chart-bar"
                                            class="bg-indigo-400 hover:bg-indigo-500">
                                        </flux:button>
                                    </flux:tooltip>
                                </div>
                                @if($periodo->estado === 'Cerrado')
                                <div>
                                    <flux:tooltip content="Descargar Excel" placement="top">
                                        <flux:button 
                                            wire:click="descargarExcel({{ $periodo->id }})"
                                            variant="primary"
                                            icon="document-arrow-down"
                                            class="bg-green-400 hover:bg-green-500">
                                        </flux:button>
                                    </flux:tooltip>
                                </div>
                                @endif
                                <div>
                                    @if($periodo->estado !== 'Cerrado')
                                        <flux:tooltip content="Cerrar periodo" placement="top">
                                            <flux:button 
                                                wire:click="estadoPeriodo({{ $periodo->id }})"
                                                variant="primary"
                                                icon="circle-x"
                                                class="bg-yellow-400 hover:bg-yellow-500">
                                            </flux:button>
                                        </flux:tooltip>
                                    @else
                                        <flux:tooltip content="Abrir periodo" placement="top">
                                            <flux:button 
                                            wire:click="estadoPeriodo({{ $periodo->id }})"
                                            variant="primary"
                                            icon="circle-check"
                                            class="bg-cyan-400 hover:bg-cyan-500">
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
                                No hay periodos registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($periodos->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                {{ $periodos->links() }}
            </div>
            @endif
        </div>
    </div>

    <flux:modal name="crearPeriodo" size="lg">
        <div class="space-y-4 p-4">
            <div>
                <flux:heading size="lg">Registrar periodo</flux:heading>
                <flux:text class="mt-1 text-gray-600">Completa los campos para registrar un nuevo periodo.</flux:text>
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
                            placeholder="Nombre del periodo"/>
                    </div>
                    <div>
                    <flux:input 
                        class="w-full" 
                        type="date" 
                        badge="*" 
                        wire:model.live="fecha_inicio" 
                        label="Fecha de inicio" 
                        placeholder="Ingresa una fecha valida"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <flux:input 
                            class="w-full" 
                            wire:model.live="fecha_fin" 
                            label="Fecha de fin" 
                            placeholder="Ingresa una fecha valida" 
                            type="date" 
                        />
                    </div>
                    <div>
                        <flux:select 
                            class="w-full" 
                            wire:model.live="organizacion_id" 
                            label="Organización" >
                            <option value="">Seleccione una organización</option>
                            @foreach($organizaciones as $organizacion)
                                <flux:select.option value="{{ $organizacion->id }}">{{ $organizacion->nombre }}</flux:select.option>
                            @endforeach
                        </flux:select>
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

    <flux:modal name="editarPeriodo" size="lg">
        <div class="space-y-4 p-4">
            <div>
                <flux:heading size="lg">Modificar periodo</flux:heading>
                <flux:text class="mt-1 text-gray-600">Completa los campos para modificar el periodo.</flux:text>
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
                            placeholder="Nombre del periodo"/>
                    </div>
                    <div>
                    <flux:input 
                        class="w-full" 
                        type="date" 
                        badge="*" 
                        wire:model.live="fecha_inicio" 
                        label="Fecha de inicio" 
                        placeholder="Ingresa una fecha valida"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <flux:input 
                            class="w-full" 
                            wire:model.live="fecha_fin" 
                            label="Fecha de fin" 
                            placeholder="Ingresa una fecha valida" 
                            type="date" 
                        />
                    </div>
                    <div>
                        <flux:select 
                            class="w-full" 
                            wire:model.live="organizacion_id" 
                            label="Organización" >
                            <option value="">Seleccione una organización</option>
                            @foreach($organizaciones as $organizacion)
                                <flux:select.option value="{{ $organizacion->id }}">{{ $organizacion->nombre }}</flux:select.option>
                            @endforeach
                        </flux:select>
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

    <flux:modal name="eliminarPeriodo" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Estas seguro de eliminar?
                </flux:heading>
                <flux:text class="mt-2">
                    <p>Se eliminará el periodo <span class=" text-red-400">{{ $nombre }}</span>.</p>
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