<x-layouts.app :title="__('Dashboard')">
    <div class="space-y-6">
        {{-- Header principal --}}
        <div class="mb-8">
            <flux:heading size="xl" class="text-gray-900 dark:text-gray-100">Dashboard del Sistema Contable</flux:heading>
            <flux:subheading class="text-gray-600 dark:text-gray-400">Panel de control y resumen ejecutivo</flux:subheading>
        </div>

        {{-- Métricas principales --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Organizaciones --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <flux:icon.building-office class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <flux:heading size="sm" class="text-gray-500 dark:text-gray-400">Organizaciones</flux:heading>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ \App\Models\Organizacion::count() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Usuarios --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                            <flux:icon.users class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <flux:heading size="sm" class="text-gray-500 dark:text-gray-400">Usuarios</flux:heading>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ \App\Models\Usuario::count() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Transacciones --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <flux:icon.document-text class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <flux:heading size="sm" class="text-gray-500 dark:text-gray-400">Transacciones</flux:heading>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ \App\Models\Transaccion::count() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Cuentas --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <flux:icon.banknotes class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <flux:heading size="sm" class="text-gray-500 dark:text-gray-400">Cuentas</flux:heading>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ \App\Models\Cuenta::count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección de accesos rápidos --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Accesos Rápidos</flux:heading>
                <flux:subheading class="text-gray-600 dark:text-gray-400">Funciones principales del sistema</flux:subheading>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Nueva Transacción --}}
                    <a href="{{ route('transacciones') }}" class="group" wire:navigate>
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                                    <flux:icon.plus class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="ml-3">
                                    <flux:heading size="sm" class="text-gray-900 dark:text-gray-100">Nueva Transacción</flux:heading>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Registrar asientos contables</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Gestionar Organizaciones --}}
                    <a href="{{ route('organizaciones') }}" class="group" wire:navigate>
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors">
                                    <flux:icon.building-office class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div class="ml-3">
                                    <flux:heading size="sm" class="text-gray-900 dark:text-gray-100">Organizaciones</flux:heading>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Gestionar empresas</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Plan de Cuentas --}}
                    <a href="{{ route('cuentas') }}" class="group" wire:navigate>
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-green-300 dark:hover:border-green-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors">
                                    <flux:icon.banknotes class="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="ml-3">
                                    <flux:heading size="sm" class="text-gray-900 dark:text-gray-100">Plan de Cuentas</flux:heading>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Catálogo contable</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Asientos Diarios --}}
                    <a href="{{ route('asientos-diarios') }}" class="group" wire:navigate>
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-orange-300 dark:hover:border-orange-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center group-hover:bg-orange-200 dark:group-hover:bg-orange-900/50 transition-colors">
                                    <flux:icon.table-cells class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                                </div>
                                <div class="ml-3">
                                    <flux:heading size="sm" class="text-gray-900 dark:text-gray-100">Asientos Diarios</flux:heading>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Registro detallado</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Períodos Contables --}}
                    <a href="{{ route('periodos') }}" class="group" wire:navigate>
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 dark:group-hover:bg-indigo-900/50 transition-colors">
                                    <flux:icon.calendar-days class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                                </div>
                                <div class="ml-3">
                                    <flux:heading size="sm" class="text-gray-900 dark:text-gray-100">Períodos</flux:heading>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Ejercicios fiscales</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Usuarios del Sistema --}}
                    <a href="{{ route('users') }}" class="group" wire:navigate>
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-cyan-300 dark:hover:border-cyan-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center group-hover:bg-cyan-200 dark:group-hover:bg-cyan-900/50 transition-colors">
                                    <flux:icon.users class="w-5 h-5 text-cyan-600 dark:text-cyan-400" />
                                </div>
                                <div class="ml-3">
                                    <flux:heading size="sm" class="text-gray-900 dark:text-gray-100">Usuarios</flux:heading>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Gestión de accesos</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Información del estado del sistema --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Transacciones recientes --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Transacciones Recientes</flux:heading>
                </div>
                <div class="p-6">
                    @php
                        $transaccionesRecientes = \App\Models\Transaccion::with('asientosDiarios')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @forelse($transaccionesRecientes as $transaccion)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <flux:icon.document-text class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $transaccion->descripcion ?: 'Sin descripción' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $transaccion->fecha_transaccion }} • {{ $transaccion->tipo_transaccion }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    ${{ number_format($transaccion->asientosDiarios->sum('monto_debe'), 2) }}
                                </p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $transaccion->estado ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200' }}">
                                    {{ $transaccion->estado ? 'Procesada' : 'Pendiente' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <flux:icon.document-text class="mx-auto w-12 h-12 text-gray-400 dark:text-gray-500" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No hay transacciones registradas</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Estado del sistema --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Estado del Sistema</flux:heading>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @php
                            $transaccionesPendientes = \App\Models\Transaccion::where('estado', false)->count();
                            $transaccionesProcesadas = \App\Models\Transaccion::where('estado', true)->count();
                        @endphp
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <flux:icon.clock class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                                </div>
                                <span class="text-sm text-gray-900 dark:text-gray-100">Transacciones Pendientes</span>
                            </div>
                            <span class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ $transaccionesPendientes }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <flux:icon.check-circle class="w-4 h-4 text-green-600 dark:text-green-400" />
                                </div>
                                <span class="text-sm text-gray-900 dark:text-gray-100">Transacciones Procesadas</span>
                            </div>
                            <span class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $transaccionesProcesadas }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <flux:icon.table-cells class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <span class="text-sm text-gray-900 dark:text-gray-100">Asientos Registrados</span>
                            </div>
                            <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ \App\Models\AsientosDiario::count() }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <flux:icon.calendar-days class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                </div>
                                <span class="text-sm text-gray-900 dark:text-gray-100">Períodos Activos</span>
                            </div>
                            <span class="text-lg font-semibold text-purple-600 dark:text-purple-400">{{ \App\Models\Periodo::count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
