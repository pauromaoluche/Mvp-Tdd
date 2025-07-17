<?php

namespace Tests\Feature\Pix;

use App\Models\User;
use App\Models\Pix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PixCreationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function usuario_autenticado_pode_gerar_pix()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('pix.store'));

        $response->assertRedirect(route('pix.list'));
        $response->assertSessionHas('success', 'PIX gerado com sucesso!');
        $response->assertSessionHas('new_pix');

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
        $response = $this->post(route('pix.store'));
        
        $response->assertRedirect(route('login'));
        $this->assertDatabaseEmpty('pixes');
    }

    #[Test]
    public function pix_criado_tem_token_unico()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Cria primeiro PIX
        $this->post(route('pix.store'));
        $firstPix = Pix::first();

        // Cria segundo PIX
        $this->post(route('pix.store'));
        $secondPix = Pix::latest()->first();

        $this->assertNotEquals($firstPix->token, $secondPix->token);
        $this->assertEquals(2, Pix::count());
    }

    #[Test]
    public function pix_expira_em_10_minutos()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $beforeCreation = now();
        $this->post(route('pix.store'));
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
        $response->assertViewIs('web.pix.create');
        $response->assertSee('Gerar PIX Fake');
    }

    #[Test]
    public function visitante_nao_pode_acessar_pagina_de_criacao()
    {
        $response = $this->get(route('pix.create'));
        
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_pode_listar_seus_pixs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Cria alguns PIXs
        $this->post(route('pix.store'));
        $this->post(route('pix.store'));

        $response = $this->get(route('pix.list'));

        $response->assertStatus(200);
        $response->assertViewIs('web.pix.created');
        $response->assertViewHas('pixes');
        $response->assertSee('PIXs Gerados');
    }
}
