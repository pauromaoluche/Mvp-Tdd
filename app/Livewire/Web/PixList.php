<?php

namespace App\Livewire\Web;

use App\Services\PixService;
use Livewire\Component;

class PixList extends Component
{
    public $pixes = [];
    public $isLoading = false;

    protected PixService $pixService;

    public function boot()
    {
        $this->pixService = app(PixService::class);
    }

    public function mount()
    {
        $this->loadPixes();
    }

    public function loadPixes()
    {
        $this->isLoading = true;
        
        try {
            $this->pixes = $this->pixService->listUserPixs();
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar PIXs: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function refreshData()
    {
        $this->loadPixes();
        session()->flash('message', 'Lista atualizada!');
    }

    public function render()
    {
        return view('livewire.web.pix-list');
    }
} 