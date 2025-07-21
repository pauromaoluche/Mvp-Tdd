<?php

namespace App\Livewire\Web;

use App\Services\PixService;
use Livewire\Component;

class PixCreator extends Component
{
    public $isLoading = false;
    public $createdPix = null;

    protected PixService $pixService;

    public function boot()
    {
        $this->pixService = app(PixService::class);
    }

    public function generatePix()
    {
        $this->isLoading = true;
        
        try {
            $this->createdPix = $this->pixService->create();
            
            session()->flash('success', 'PIX gerado com sucesso!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao gerar PIX: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function resetForm()
    {
        $this->createdPix = null;
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.web.pix-creator');
    }
} 