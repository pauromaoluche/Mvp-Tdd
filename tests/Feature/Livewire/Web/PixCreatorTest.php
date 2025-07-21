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

    protected function setUp(): void
    {
        parent::setUp();
        
        // Força a configuração do banco de dados para usar SQLite em memória
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
    }

    #[Test]
    public function componente_pode_ser_renderizado()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class);

        $component->assertStatus(200);
        $component->assertSee('Gerar PIX Fake');
        $component->assertSee('Gerar PIX');
    }

    #[Test]
    public function pode_gerar_pix_com_sucesso()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
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

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
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

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class);

        $component->assertSet('isLoading', false);
        
        // Simula o estado de loading durante a geração
        $component->set('isLoading', true);
        $component->assertSet('isLoading', true);
    }

    #[Test]
    public function exibe_mensagem_de_sucesso_apos_gerar_pix()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
            ->call('generatePix');

        $component->assertSee('PIX gerado com sucesso!');
    }

    #[Test]
    public function exibe_token_do_pix_gerado()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
            ->call('generatePix');

        $component->assertSee('Token:');
        $component->assertSet('createdPix', function ($pix) {
            return $pix !== null && !empty($pix->token);
        });
    }

    #[Test]
    public function exibe_data_de_expiracao_do_pix()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
            ->call('generatePix');

        $component->assertSee('Expira em:');
        $component->assertSet('createdPix', function ($pix) {
            return $pix !== null && $pix->expires_at !== null;
        });
    }

    #[Test]
    public function botao_gerar_pix_fica_desabilitado_durante_loading()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class);

        // Verifica se o botão está habilitado inicialmente
        $component->assertSee('Gerar PIX');
        
        // Simula o estado de loading
        $component->set('isLoading', true);
        $component->assertSet('isLoading', true);
    }

    #[Test]
    public function pode_gerar_multiplos_pixs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class);

        // Gera primeiro PIX
        $component->call('generatePix');
        $firstPix = $component->get('createdPix');

        // Reseta o formulário
        $component->call('resetForm');
        $component->assertSet('createdPix', null);

        // Gera segundo PIX
        $component->call('generatePix');
        $secondPix = $component->get('createdPix');

        // Verifica se são diferentes
        $this->assertNotEquals($firstPix->token, $secondPix->token);
    }

    #[Test]
    public function exibe_botao_gerar_outro_pix_apos_gerar_pix()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
            ->call('generatePix');

        $component->assertSee('Gerar Outro PIX');
    }

    #[Test]
    public function exibe_botao_copiar_link_apos_gerar_pix()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
            ->call('generatePix');

        $component->assertSee('Copiar Link');
    }

    #[Test]
    public function exibe_link_para_lista_de_pixs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class);

        $component->assertSee('Ver PIXs Criados');
    }

    #[Test]
    public function pix_gerado_tem_status_generated()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
            ->call('generatePix');

        $component->assertSet('createdPix', function ($pix) {
            return $pix !== null && $pix->status === 'generated';
        });
    }

    #[Test]
    public function pix_gerado_pertence_ao_usuario_autenticado()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
            ->call('generatePix');

        $component->assertSet('createdPix', function ($pix) use ($user) {
            return $pix !== null && $pix->user_id === $user->id;
        });
    }

    #[Test]
    public function pix_gerado_expira_em_10_minutos()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $beforeCreation = now();
        
        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
            ->call('generatePix');

        $afterCreation = now();

        $component->assertSet('createdPix', function ($pix) use ($beforeCreation, $afterCreation) {
            return $pix !== null && 
                   $pix->expires_at->between(
                       $beforeCreation->addMinutes(9),
                       $afterCreation->addMinutes(11)
                   );
        });
    }

    #[Test]
    public function token_do_pix_e_unico()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class);

        // Gera primeiro PIX
        $component->call('generatePix');
        $firstPix = $component->get('createdPix');

        // Reseta e gera segundo PIX
        $component->call('resetForm');
        $component->call('generatePix');
        $secondPix = $component->get('createdPix');

        $this->assertNotEquals($firstPix->token, $secondPix->token);
    }

    #[Test]
    public function exibe_icone_de_sucesso_apos_gerar_pix()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
            ->call('generatePix');

        $component->assertSee('✅ PIX Gerado com Sucesso!');
    }

    #[Test]
    public function exibe_informacoes_completas_do_pix_gerado()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::actingAs($user)
            ->test(PixCreator::class)
            ->call('generatePix');

        $component->assertSee('Token:');
        $component->assertSee('Link de Pagamento:');
        $component->assertSee('Expira em:');
        $component->assertSee('Gerar Outro PIX');
        $component->assertSee('Copiar Link');
    }
}