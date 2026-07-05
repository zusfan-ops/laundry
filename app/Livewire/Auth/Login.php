<?php

namespace App\Livewire\Auth;

use App\Support\RoleRouter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    #[Validate('required|string')]
    public string $login = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = true;

    public function authenticate()
    {
        $this->validate();

        $key = 'login:' . md5($this->login . request()->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'login' => 'Terlalu banyak percobaan. Coba lagi dalam ' . RateLimiter::availableIn($key) . ' detik.',
            ]);
        }

        // Allow login by email or phone.
        $field = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (! Auth::attempt([$field => $this->login, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'login' => 'Nomor/email atau kata sandi salah.',
            ]);
        }

        RateLimiter::clear($key);
        session()->regenerate();

        return $this->redirectRoute(RoleRouter::homeRouteFor(Auth::user()), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
