<?php

namespace Tests\Feature\Livewire\Web;

use App\Livewire\Web\PixCreator;
use App\Models\User;
use Tests\TestCase;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PixCreatorTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function componente_pode_ser_renderizado()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(PixCreator::class);

        $component->assertStatus(200);
        $component->assertSee('Gerar PIX Fake');
        $component->assertSee('Gerar PIX');
    }

    #[Test]
    public function pode_gerar_pix_com_sucesso()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(PixCreator::class)
            ->call('generatePix');

        $component->assertSet('createdPix', function ($pix) use ($user) {
            return $pix !== null && $pix->user_id === $user->id;
        });
        
        $component->assertSee('PIX Gerado com Sucesso!');
    }

    #[Test]
    public function pode_resetar_formulario()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(PixCreator::class)
            ->call('generatePix')
            ->call('resetForm');

        $component->assertSet('createdPix', null);
        $component->assertSet('isLoading', false);
    }

    #[Test]
    public function loading_state_funciona_corretamente()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(PixCreator::class);

        $component->assertSet('isLoading', false);
        
        // Simula o estado de loading durante a geração
        $component->set('isLoading', true);
        $component->assertSet('isLoading', true);
    }
} 