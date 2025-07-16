<?php

namespace Tests\Feature\Livewire\Web\Auth;

use App\Livewire\Web\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function register_page_contains_livewire_auth_component()
    {
        $response = $this->get(route('index.auth', ['mode' => 'register']));

        $response->assertStatus(200);
        $response->assertSeeLivewire(Auth::class);
        $response->assertSee('Crie sua conta');
    }

    #[Test]
    public function can_register_a_new_user_successfully()
    {
        Livewire::test(Auth::class, ['mode' => 'register'])
            ->set('form.name', 'John Doe')
            ->set('form.email', 'john@example.com')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'password123')
            ->call('register');

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        $this->assertGuest();
    }
}
