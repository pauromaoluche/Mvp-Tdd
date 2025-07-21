<?php

namespace Tests\Feature\Pix;

use App\Models\User;
use App\Models\Pix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Livewire\Livewire;

class PixCreationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function usuario_autenticado_pode_gerar_pix()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Acessa a página de criação de PIX
        $response = $this->get(route('pix.create'));
        $response->assertStatus(200);

        // Usa Livewire para gerar o PIX
        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Web\PixCreator::class)
            ->call('generatePix')
            ->assertHasNoErrors();

        // Verifica se o PIX foi criado no banco
        $this->assertDatabaseHas('pixes', [
            'user_id' => $user->id,
            'status' => 'generated',
        ]);

        $pix = Pix::first();
        $this->assertNotNull($pix->token);
        $this->assertTrue($pix->expires_at->gt(now()));
    }

    #[Test]
    public function visitantes_nao_podem_gerar_pix()
    {
        $response = $this->get(route('pix.create'));
        
        $response->assertRedirect(route('index.auth', 'login'));
        $this->assertDatabaseEmpty('pixes');
    }

    #[Test]
    public function pix_expira_em_10_minutos()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $beforeCreation = now();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Web\PixCreator::class)
            ->call('generatePix');
            
        $afterCreation = now();

        $pix = Pix::first();
        
        // Verifica se expira entre 9 e 11 minutos (tolerância para tempo de execução)
        $this->assertTrue($pix->expires_at->between(
            $beforeCreation->addMinutes(9),
            $afterCreation->addMinutes(11)
        ));
    }

    #[Test]
    public function usuario_pode_acessar_pagina_de_criacao()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('pix.create'));

        $response->assertStatus(200);
        $response->assertSee('Gerar PIX Fake');
    }

    #[Test]
    public function visitante_nao_pode_acessar_pagina_de_criacao()
    {
        $response = $this->get(route('pix.create'));
        
        $response->assertRedirect(route('index.auth', 'login'));
    }

    #[Test]
    public function usuario_pode_listar_seus_pixs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Cria alguns PIXs
        Livewire::actingAs($user)
            ->test(\App\Livewire\Web\PixCreator::class)
            ->call('generatePix');
            
        Livewire::actingAs($user)
            ->test(\App\Livewire\Web\PixCreator::class)
            ->call('generatePix');

        $response = $this->get(route('pix.list'));

        $response->assertStatus(200);
        $response->assertSee('Meus PIXs');
    }
}
