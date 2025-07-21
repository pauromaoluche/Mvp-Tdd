<div>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="text-yellow-400 mb-2">Meus PIXs</h1>
                <p class="text-light">Gerencie todos os seus PIXs gerados</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('pix.create') }}" 
                   class="btn btn-primary me-2"
                   wire:navigate>
                    <i class="fas fa-plus"></i> Novo PIX
                </a>
                <button wire:click="refreshData" 
                        class="btn btn-info"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="fas fa-refresh"></i>
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success text-center mb-4">{{ session('success') }}</div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger text-center mb-4">{{ session('error') }}</div>
        @endif

        @if (session()->has('message'))
            <div class="alert alert-info text-center mb-4">{{ session('message') }}</div>
        @endif

        <!-- Tabela de PIXs -->
        <div class="card" wire:loading.class="opacity-50">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Token</th>
                                <th>Status</th>
                                <th>Criado</th>
                                <th>Expira</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pixes as $pix)
                                <tr>
                                    <td>
                                        <code class="text-warning">{{ Str::limit($pix->token, 25) }}</code>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($pix->status === 'paid') bg-success
                                            @elseif($pix->status === 'expired') bg-danger
                                            @else bg-warning text-dark
                                            @endif">
                                            @if($pix->status === 'generated') Pendente
                                            @elseif($pix->status === 'paid') Pago
                                            @else Expirado
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            @if(function_exists('formatarDataBR'))
                                                {{ formatarDataBR($pix->created_at) }}
                                            @else
                                                {{ $pix->created_at->format('d/m/Y H:i') }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            @if(function_exists('formatarDataBR'))
                                                {{ formatarDataBR($pix->expires_at) }}
                                            @else
                                                {{ $pix->expires_at->format('d/m/Y H:i') }}
                                            @endif
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            @if(function_exists('tempoRestanteBR'))
                                                ({{ tempoRestanteBR($pix->expires_at) }})
                                            @else
                                                ({{ $pix->expires_at->diffForHumans() }})
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($pix->status === 'generated')
                                                <form method="POST" action="{{ route('pix.confirm', $pix->token) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-success btn-sm" 
                                                            title="Simular Pagamento">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <button class="btn btn-info btn-sm" 
                                                    onclick="copyToClipboard('{{ route('pix.confirm', $pix->token) }}')"
                                                    title="Copiar Link">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-qrcode fa-2x mb-2 d-block"></i>
                                            Você ainda não gerou nenhum PIX.
                                            <br>
                                            <a href="{{ route('pix.create') }}" 
                                               class="btn btn-sm btn-primary mt-2"
                                               wire:navigate>
                                                Gerar Primeiro PIX
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link copiado para a área de transferência!');
            });
        }
    </script>
</div> 