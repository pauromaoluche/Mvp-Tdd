<?php

namespace Tests\Feature\Livewire\Web;

use App\Livewire\Web\PixList;
use App\Models\User;
use App\Models\Pix;
use Tests\TestCase;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Carbon\Carbon;

class PixListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function componente_pode_ser_renderizado()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(PixList::class);

        $component->assertStatus(200);
        $component->assertSee('Meus PIXs');
        $component->assertSee('Novo PIX');
    }

    #[Test]
    public function exibe_pixes_do_usuario()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pix = Pix::create([
            'user_id' => $user->id,
            'token' => 'test-token-123',
            'status' => 'generated',
            'expires_at' => Carbon::now()->addMinutes(10)
        ]);

        $component = Livewire::test(PixList::class);

        $component->assertSee('test-token-123');
        $component->assertSee('Pendente');
    }

    #[Test]
    public function nao_exibe_pixes_de_outros_usuarios()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $this->actingAs($user1);

        $pix = Pix::create([
            'user_id' => $user2->id,
            'token' => 'other-user-token',
            'status' => 'generated',
            'expires_at' => Carbon::now()->addMinutes(10)
        ]);

        $component = Livewire::test(PixList::class);

        $component->assertDontSee('other-user-token');
    }

    #[Test]
    public function exibe_mensagem_quando_nao_ha_pixes()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(PixList::class);

        $component->assertSee('Você ainda não gerou nenhum PIX.');
        $component->assertSee('Gerar Primeiro PIX');
    }

    #[Test]
    public function refresh_data_funciona_corretamente()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(PixList::class)
            ->call('refreshData');

        $component->assertHasNoErrors();
        $component->assertSee('Lista atualizada!');
    }

    #[Test]
    public function exibe_diferentes_status_corretamente()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pixPending = Pix::create([
            'user_id' => $user->id,
            'token' => 'pending-token',
            'status' => 'generated',
            'expires_at' => Carbon::now()->addMinutes(10)
        ]);

        $pixPaid = Pix::create([
            'user_id' => $user->id,
            'token' => 'paid-token', 
            'status' => 'paid',
            'expires_at' => Carbon::now()->addMinutes(10)
        ]);

        $pixExpired = Pix::create([
            'user_id' => $user->id,
            'token' => 'expired-token',
            'status' => 'expired',
            'expires_at' => Carbon::now()->subMinutes(10)
        ]);

        $component = Livewire::test(PixList::class);

        $component->assertSee('Pendente');
        $component->assertSee('Pago');
        $component->assertSee('Expirado');
    }
} 