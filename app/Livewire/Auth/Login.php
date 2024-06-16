<?php

namespace App\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Login extends Component
{
    public $email;
    public $password;
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        // Llamar a la API para el login
        $response = Http::post('http://127.0.0.1:8000/api/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if ($response->successful()) {
            session(['token' => $response->json('data.token')]);
            return redirect()->route('dashboard');
        } else {
            session()->flash('error', 'Credenciales incorrectas.');
        }
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
