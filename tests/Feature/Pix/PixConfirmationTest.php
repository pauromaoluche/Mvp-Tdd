<?php

namespace Tests\Feature\Pix;

use App\Models\User;
use App\Models\Pix;
use App\Services\PixService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;

class PixConfirmationTest extends TestCase
{
    use RefreshDatabase;

    private PixService $pixService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pixService = app(PixService::class);
    }

    #[Test]
    public function pode_confirmar_pagamento_de_pix_valido()
    {
        $user = User::factory()->create();
        $pix = Pix::create([
            'user_id' => $user->id,
            'token' => 'test-token-123',
            'status' => 'generated',
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $response = $this->get(route('pix.confirm', 'test-token-123'));

        $response->assertStatus(200);
        $response->assertViewIs('web.pix.confirm');
        $response->assertViewHas('result.success', true);
        $response->assertViewHas('result.message', 'Pagamento confirmado com sucesso!');
        $response->assertSee('Pagamento Confirmado!');

        $this->assertDatabaseHas('pixes', [
            'token' => 'test-token-123',
            'status' => 'paid',
        ]);
    }

    #[Test] 
    public function nao_pode_confirmar_pagamento_de_pix_expirado()
    {
        $user = User::factory()->create();
        $pix = Pix::create([
            'user_id' => $user->id,
            'token' => 'expired-token-123',
            'status' => 'generated',
            'expires_at' => Carbon::now()->subMinutes(5), // Expirado
        ]);

        $response = $this->get(route('pix.confirm', 'expired-token-123'));

        $response->assertStatus(200);
        $response->assertViewIs('web.pix.confirm');
        $response->assertViewHas('result.success', false);
        $response->assertViewHas('result.status', 'expired');
        $response->assertSee('PIX Expirado');

        $this->assertDatabaseHas('pixes', [
            'token' => 'expired-token-123',
            'status' => 'expired',
        ]);
    }

    #[Test]
    public function nao_pode_confirmar_pagamento_de_pix_ja_pago()
    {
        $user = User::factory()->create();
        $pix = Pix::create([
            'user_id' => $user->id,
            'token' => 'paid-token-123',
            'status' => 'paid',
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $response = $this->get(route('pix.confirm', 'paid-token-123'));

        $response->assertStatus(200);
        $response->assertViewIs('web.pix.confirm');
        $response->assertViewHas('result.success', false);
        $response->assertViewHas('result.status', 'paid');
        $response->assertSee('PIX Já Pago');

        // Status deve permanecer como 'paid'
        $this->assertDatabaseHas('pixes', [
            'token' => 'paid-token-123',
            'status' => 'paid',
        ]);
    }

    #[Test]
    public function retorna_erro_para_token_inexistente()
    {
        $response = $this->get(route('pix.confirm', 'token-inexistente'));

        $response->assertStatus(200);
        $response->assertViewIs('web.pix.confirm');
        $response->assertViewHas('result.success', false);
        $response->assertViewHas('result.status', null);
        $response->assertSee('PIX Não Encontrado');
    }

    #[Test]
    public function service_confirma_pagamento_corretamente()
    {
        $user = User::factory()->create();
        $pix = Pix::create([
            'user_id' => $user->id,
            'token' => 'service-test-token',
            'status' => 'generated',
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $result = $this->pixService->confirmPayment('service-test-token');

        $this->assertTrue($result['success']);
        $this->assertEquals('Pagamento confirmado com sucesso!', $result['message']);
        $this->assertEquals('paid', $result['status']);
        $this->assertInstanceOf(Pix::class, $result['pix']);

        $pix->refresh();
        $this->assertEquals('paid', $pix->status);
    }

    #[Test]
    public function service_marca_pix_como_expirado_automaticamente()
    {
        $user = User::factory()->create();
        $pix = Pix::create([
            'user_id' => $user->id,
            'token' => 'auto-expire-token',
            'status' => 'generated',
            'expires_at' => Carbon::now()->subMinutes(1), // Expirado
        ]);

        $result = $this->pixService->confirmPayment('auto-expire-token');

        $this->assertFalse($result['success']);
        $this->assertEquals('Este PIX está expirado.', $result['message']);
        $this->assertEquals('expired', $result['status']);

        $pix->refresh();
        $this->assertEquals('expired', $pix->status);
    }


} 