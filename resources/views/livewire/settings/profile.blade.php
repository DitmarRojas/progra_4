<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Perfil')" :subheading="__('Actualiza la informacion de tu perfil')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="nombres" :label="__('Nombres')" type="text" autofocus autocomplete="nombres" />
            <flux:input wire:model="apellidos" :label="__('Apellidos')" type="text" autofocus autocomplete="apellidos" />
            <flux:input wire:model="telefono" :label="__('Teléfono')" type="tel" autofocus autocomplete="telefono" />
            <flux:input wire:model="username" :label="__('Nombre de usuario')" type="text" autofocus autocomplete="username" />
            <div>
                <flux:input wire:model="email" :label="__('Correo electrónico')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Tu correo electrónico no está verificado.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Haz clic aquí para reenviar el correo electrónico de verificación.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Guardar') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Guardado.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
