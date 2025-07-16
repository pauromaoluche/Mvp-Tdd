<?php

namespace Tests\Feature\Livewire\Web\Auth;

use App\Livewire\Web\Auth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_page_contains_livewire_auth_component()
    {
        $response = $this->get(route('index.auth', ['mode' => 'login']));

        $response->assertStatus(200);
        $response->assertSeeLivewire(Auth::class);
        $response->assertSee('Faça login');
    }

    #[Test]
    public function can_login_an_existing_user_successfully()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Auth::class, ['mode' => 'login'])
            ->set('login_email', 'test@example.com')
            ->set('login_password', 'password123')
            ->call('login')
            ->assertRedirect(route('index.index'));

        // 3. Verifique se o usuário foi autenticado
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function login_fails_with_invalid_credentials()
    {
        // 1. Crie um usuário válido para testar a falha com credenciais erradas
        User::factory()->create([
            'email' => 'valid@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        // 2. Tente fazer login com credenciais inválidas
        Livewire::test(Auth::class, ['mode' => 'login'])
            ->set('login_email', 'valid@example.com')
            // Senha incorreta
            ->set('login_password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['login_email' => 'O E-mail pode estar errado.']);

        // 3. Verifique se o usuário NÃO foi autenticado
        $this->assertGuest();
    }
}
