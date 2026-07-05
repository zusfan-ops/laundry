<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Register extends Component
{
    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('required|string|max:20|unique:users,phone')]
    public string $phone = '';

    #[Validate('nullable|email|max:150|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|min:6|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public function register()
    {
        $data = $this->validate();

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?: null,
            'password' => Hash::make($data['password']),
            'role' => 'customer',
            'phone_verified_at' => now(), // OTP simulated as verified in this build
        ]);

        Auth::login($user, true);
        session()->regenerate();

        return $this->redirectRoute('home', navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
