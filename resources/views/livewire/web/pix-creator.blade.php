<div>
    <div class="container py-5">
        <h2 class="text-3xl font-bold text-yellow-400 mb-6 text-center">Gerar PIX Fake</h2>

        @if (session()->has('success'))
            <div class="bg-green-500 text-white p-3 rounded-lg text-center mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-500 text-white p-3 rounded-lg text-center mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if (!$createdPix)
            <div class="text-center">
                <p class="text-sm text-gray-400 mb-4">
                    Clique abaixo para gerar um novo PIX válido por 10 minutos.
                </p>

                <button wire:click="generatePix"
                        wire:loading.attr="disabled"
                        class="w-full py-3 bg-yellow-400 hover:bg-yellow-500 text-black font-bold rounded-lg transition duration-200 {{ $isLoading ? 'opacity-50 cursor-not-allowed' : '' }}">
                    
                    <span wire:loading.remove>Gerar PIX</span>
                    <span wire:loading class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Gerando PIX...
                    </span>
                </button>
            </div>
        @else
            <div class="bg-green-800 text-white p-4 rounded mb-4 shadow-sm">
                <h4 class="text-yellow-400 mb-2">✅ PIX Gerado com Sucesso!</h4>
                <div class="space-y-2">
                    <p><strong>Token:</strong> <code class="bg-gray-700 px-2 py-1 rounded">{{ $createdPix->token }}</code></p>
                    <p><strong>Link de Pagamento:</strong> 
                        <a href="{{ route('pix.confirm', $createdPix->token) }}" 
                           class="text-yellow-300 hover:underline break-all" 
                           target="_blank">
                            {{ url('/pix/' . $createdPix->token) }}
                        </a>
                    </p>
                    <p><strong>Expira em:</strong> 
                        @if(function_exists('formatarDataBR'))
                            {{ formatarDataBR($createdPix->expires_at) }}
                        @else
                            {{ $createdPix->expires_at->format('d/m/Y H:i') }}
                        @endif
                        (
                        @if(function_exists('tempoRestanteBR'))
                            {{ tempoRestanteBR($createdPix->expires_at) }}
                        @else
                            {{ $createdPix->expires_at->diffForHumans() }}
                        @endif
                        )
                    </p>
                </div>
                
                <div class="mt-4 flex gap-3">
                    <button wire:click="resetForm"
                            class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded transition duration-200">
                        Gerar Outro PIX
                    </button>
                    
                    <button onclick="copyToClipboard('{{ url('/pix/' . $createdPix->token) }}')"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        Copiar Link
                    </button>
                </div>
            </div>
        @endif

        <div class="text-center mt-6">
            <a href="{{ route('pix.list') }}" 
               class="text-yellow-300 hover:underline text-sm"
               wire:navigate>
                Ver PIXs Criados
            </a>
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