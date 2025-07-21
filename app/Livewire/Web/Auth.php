<?php

namespace App\Livewire\Web;

use App\Livewire\Forms\Web\AuthForm;
use App\Services\AuthService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Auth extends Component
{
    public $mode = 'login';
    public string $login_email = '';
    public string $login_password = '';

    public AuthForm $form;

    public function register(AuthService $authService)
    {
        $this->form->validate();

        $authService->register([
            'name' => $this->form->name,
            'email' => $this->form->email,
            'password' => $this->form->password,
        ]);

        //session()->flash('message', 'Conta criada com sucesso! Faça login para continuar.');
        $this->form->reset();
    }

    public function login(AuthService $authService)
    {
        $this->validate([
            'login_email' => 'required|email',
            'login_password' => 'required',
        ]);

        if (!$authService->login([
            'email' => $this->login_email,
            'password' => $this->login_password,
        ])) {
            throw ValidationException::withMessages([
                'login_email' => 'O E-mail pode estar errado.',
                'login_password' => 'A senha pode estar errada.',
            ]);
        }
        return redirect()->route('pix.list');
    }

    public function render()
    {
        return view('livewire.web.auth');
    }
}
