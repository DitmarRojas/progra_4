<?php

namespace App\Livewire\Settings;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public string $nombres = '';
    public string $apellidos = '';
    public string $telefono = '';
    public string $username = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->nombres = Auth::user()->nombres;
        $this->apellidos = Auth::user()->apellidos;
        $this->telefono = Auth::user()->telefono;
        $this->username = Auth::user()->username;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'nombres' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'apellidos' => ['required','string','max:100', 'regex:/^[\pL\s]+$/u'],
            'telefono' => ['nullable','numeric','digits:8'],
            'username' => ['required', 'string', 'max:50',Rule::unique(Usuario::class)->ignore($user->id)],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Usuario::class)->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', nombres: $user->nombres);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}
