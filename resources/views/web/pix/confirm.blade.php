@extends('layouts.web')

@section('title', 'Confirmação de Pagamento PIX')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card bg-gray-800 text-white">
                <div class="card-header text-center">
                    <h2 class="text-yellow-400 mb-0">
                        <i class="fas fa-qrcode me-2"></i>
                        Confirmação de Pagamento PIX
                    </h2>
                </div>
                
                <div class="card-body text-center">
                    @if (session()->has('success'))
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($result['success'])
                        @if($result['status'] === 'paid')
                            <!-- PIX Pago -->
                            <div class="mb-4">
                                <div class="bg-success text-white p-3 rounded mb-3">
                                    <i class="fas fa-check-circle fa-3x mb-2"></i>
                                    <h4>Pagamento Confirmado!</h4>
                                    <p class="mb-0">O PIX foi pago com sucesso</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="bg-dark p-3 rounded mb-3">
                                            <h6 class="text-success">Token do PIX</h6>
                                            <code class="text-white">{{ $token }}</code>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-dark p-3 rounded mb-3">
                                    <h6 class="text-success">Status</h6>
                                    <span class="badge bg-success fs-6">Pago</span>
                                </div>
                                
                                <div class="bg-dark p-3 rounded mb-4">
                                    <h6 class="text-success">Confirmado em</h6>
                                    <p class="mb-0">
                                        @if(function_exists('formatarDataBR'))
                                            {{ formatarDataBR($result['paid_at'] ?? now()) }}
                                        @else
                                            {{ ($result['paid_at'] ?? now())->format('d/m/Y H:i') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                        @elseif($result['status'] === 'expired')
                            <!-- PIX Expirado -->
                            <div class="mb-4">
                                <div class="bg-danger text-white p-3 rounded mb-3">
                                    <i class="fas fa-times-circle fa-3x mb-2"></i>
                                    <h4>PIX Expirado</h4>
                                    <p class="mb-0">O tempo para pagamento expirou</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="bg-dark p-3 rounded mb-3">
                                            <h6 class="text-danger">Token do PIX</h6>
                                            <code class="text-white">{{ $token }}</code>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-dark p-3 rounded mb-3">
                                            <h6 class="text-danger">Valor</h6>
                                            <h4 class="text-danger mb-0">R$ {{ number_format($result['amount'] ?? 0, 2, ',', '.') }}</h4>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-dark p-3 rounded mb-3">
                                    <h6 class="text-danger">Status</h6>
                                    <span class="badge bg-danger fs-6">Expirado</span>
                                </div>
                                
                                <div class="bg-dark p-3 rounded mb-4">
                                    <h6 class="text-danger">Expirou em</h6>
                                    <p class="mb-0">
                                        @if(function_exists('formatarDataBR'))
                                            {{ formatarDataBR($result['expires_at'] ?? now()) }}
                                        @else
                                            {{ ($result['expires_at'] ?? now())->format('d/m/Y H:i') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                    @else
                        <!-- Erro -->
                        <div class="mb-4">
                            <div class="bg-danger text-white p-3 rounded mb-3">
                                <i class="fas fa-exclamation-triangle fa-3x mb-2"></i>
                                <h4>PIX Não Encontrado</h4>
                                <p class="mb-0">{{ $result['message'] ?? 'O PIX solicitado não foi encontrado' }}</p>
                            </div>
                            
                            <div class="bg-dark p-3 rounded mb-3">
                                <h6 class="text-danger">Token Informado</h6>
                                <code class="text-white">{{ $token }}</code>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Botões de Ação -->
                    <div class="mt-4">
                        @auth
                            <a href="{{ route('pix.list') }}" class="btn btn-primary me-2">
                                <i class="fas fa-list me-2"></i>Ver Meus PIXs
                            </a>
                            <a href="{{ route('pix.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Gerar Novo PIX
                            </a>
                        @else
                            <a href="{{ route('index.auth', 'login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
