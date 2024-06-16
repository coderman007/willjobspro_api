<?php

namespace App\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Register extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;

    protected $rules = [

    ];

    public function register()
    {

        try {
            $response = Http::post('http://127.0.0.1:8000/api/register', [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role,
            ]);

            if ($response->successful()) {
                // Manejar el éxito
                return redirect()->route('dashboard');
            } else {
                // Manejar el error
                return 'Error';
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.auth.register');
    }
}
