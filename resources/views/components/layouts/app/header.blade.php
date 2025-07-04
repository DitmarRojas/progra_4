<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>
            </flux:navbar>
            @if (auth()->user()->rol_id === 1)
            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="users" :href="route('users')" :current="request()->routeIs('users')" wire:navigate>
                    {{ __('Usuarios') }}
                </flux:navbar.item>
                <flux:navbar.item icon="building-2" :href="route('organizaciones')" :current="request()->routeIs('organizaciones')" wire:navigate>
                    {{ __('Organizaciones') }}
                </flux:navbar.item>
                <flux:navbar.item icon="book-text" :href="route('cuentas')" :current="request()->routeIs('cuentas')" wire:navigate>
                    {{ __('Cuentas') }}
                </flux:navbar.item>
                <flux:navbar.item icon="calendar-range" :href="route('periodos')" :current="request()->routeIs('periodos')" wire:navigate>
                    {{ __('Periodos') }}
                </flux:navbar.item>
                <flux:navbar.item icon="calendar-range" :href="route('transacciones')" :current="request()->routeIs('transacciones')" wire:navigate>
                    {{ __('Transacciones') }}
                </flux:navbar.item>
                <flux:navbar.item icon="" :href="route('asientos_diarios')" :current="request()->routeIs('asientos_diarios')" wire:navigate>
                    {{ __('Asientos Diarios') }}
                </flux:navbar.item>
            </flux:navbar>
            @endif

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">

            </flux:navbar>

            <!-- Desktop User Menu -->
            <flux:dropdown position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Ajustes') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Cerrar session') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')">
                    <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:navlist.item>
                    @if (auth()->user()->rol_id === 1)
                    <flux:navlist.item icon="users" :href="route('users')" :current="request()->routeIs('users')" wire:navigate>
                        {{ __('Usuarios') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="building-2" :href="route('organizaciones')" :current="request()->routeIs('organizaciones')" wire:navigate>
                        {{ __('Organizaciones') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="book-text" :href="route('cuentas')" :current="request()->routeIs('cuentas')" wire:navigate>
                        {{ __('Cuentas') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="calendar-range" :href="route('periodos')" :current="request()->routeIs('periodos')" wire:navigate>
                        {{ __('Periodos') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="calendar-range" :href="route('asientos_diarios')" :current="request()->routeIs('asientos_diarios')" wire:navigate>
                        {{ __('Asientos Diarios') }}
                    </flux:navlist.item>
                    @endif
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />
        </flux:sidebar>
        {{ $slot }}
        @fluxScripts
    </body>
</html>
