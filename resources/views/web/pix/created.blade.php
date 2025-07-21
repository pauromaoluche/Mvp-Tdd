@extends('layouts.web')

@section('title', 'PIX Criados')

@section('content')
<div class="container py-5">
    <h2 class="text-center text-yellow-400 mb-4">PIXs Gerados</h2>

    @if (session('success'))
        <div class="alert alert-success text-center mb-4">{{ session('success') }}</div>
    @endif

    @if (session('new_pix'))
        @php $newPix = session('new_pix'); @endphp
        <div class="bg-green-800 text-white p-4 rounded mb-4 shadow-sm">
            <h4 class="text-yellow-400 mb-2">✅ PIX Gerado com Sucesso!</h4>
            <p><strong>Token:</strong> {{ $newPix->token }}</p>
            <p><strong>Link de Pagamento:</strong> 
                <a href="{{ route('pix.confirm', $newPix->token) }}" 
                   class="text-yellow-300 hover:underline">
                    {{ url('/pix/' . $newPix->token) }}
                </a>
            </p>
            <p><strong>Expira em:</strong> 
                @if(function_exists('formatarDataBR'))
                    {{ formatarDataBR($newPix->expires_at) }}
                @else
                    {{ $newPix->expires_at->format('d/m/Y H:i') }}
                @endif
                (
                @if(function_exists('tempoRestanteBR'))
                    {{ tempoRestanteBR($newPix->expires_at) }}
                @else
                    {{ $newPix->expires_at->diffForHumans() }}
                @endif
                )
            </p>
        </div>
    @endif

    @forelse ($pixes as $pix)
        <div class="bg-gray-800 text-white p-4 rounded mb-3 shadow-sm">
            <div class="row">
                <div class="col-md-8">
                    <p><strong>Token:</strong> {{ $pix->token }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge 
                            @if($pix->status === 'paid') bg-success
                            @elseif($pix->status === 'expired') bg-danger
                            @else bg-warning
                            @endif">
                            {{ ucfirst($pix->status) }}
                        </span>
                    </p>
                    <p><strong>Criado:</strong> 
                        @if(function_exists('formatarDataBR'))
                            {{ formatarDataBR($pix->created_at) }}
                        @else
                            {{ $pix->created_at->format('d/m/Y H:i') }}
                        @endif
                    </p>
                    <p><strong>Expira:</strong> 
                        @if(function_exists('formatarDataBR'))
                            {{ formatarDataBR($pix->expires_at) }}
                        @else
                            {{ $pix->expires_at->format('d/m/Y H:i') }}
                        @endif
                        (
                        @if(function_exists('tempoRestanteBR'))
                            {{ tempoRestanteBR($pix->expires_at) }}
                        @else
                            {{ $pix->expires_at->diffForHumans() }}
                        @endif
                        )
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    @if($pix->status === 'generated')
                        <a href="{{ route('pix.confirm', $pix->token) }}" 
                           class="btn btn-sm btn-warning mb-2">
                            Simular Pagamento
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p class="text-center text-white">Nenhum PIX gerado ainda.</p>
    @endforelse

    <div class="text-center mt-4">
        <a href="{{ route('pix.create') }}" class="btn btn-primary me-2">Gerar Novo PIX</a>
                                <a href="{{ route('pix.list') }}" class="btn btn-secondary">Ver Todos os PIXs</a>
    </div>
</div>
@endsection
