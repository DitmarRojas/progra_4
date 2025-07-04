<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|max:255')]
    public string $username = '';

    #[Validate('required|string|min:6|max:255')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
{
    $this->validate();

    $this->ensureIsNotRateLimited();

    if (! Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => __('auth.failed'),
        ]);
    }

    $usuario = Auth::user();
    
    if ($usuario->estado === 'Bloqueado') {
        Auth::logout();
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'username' => 'Tu usuario está bloqueado. Contacta al administrador.',
        ]);
    }

    $usuario->estado = 'Activo';
    $usuario->save();

    RateLimiter::clear($this->throttleKey());
    Session::regenerate();

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
}

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }
        
        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->username).'|'.request()->ip());
    }
}
