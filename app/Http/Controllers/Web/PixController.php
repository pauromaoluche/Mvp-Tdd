<?php

namespace App\Http\Controllers\Web;

use App\Services\PixService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PixController extends Controller
{
    public function __construct(protected PixService $pixService) {}

    /**
     * Confirma pagamento via token (rota pública)
     */
    public function confirm(string $token)
    {
        $result = $this->pixService->confirmPayment($token);
        
        if ($result['success'] && $result['status'] === 'paid') {
            return redirect()->route('pix.list')
                ->with('success', 'Pagamento confirmado com sucesso!');
        }
        
        // Se não conseguiu confirmar, redireciona com erro
        return redirect()->route('pix.list')
            ->with('error', $result['message'] ?? 'Erro ao confirmar pagamento');
    }

    /**
     * Exibe tela de confirmação de pagamento
     */
    public function show(string $token)
    {
        $pix = $this->pixService->findByToken($token);
        
        if (!$pix) {
            return view('web.pix.confirm', [
                'result' => [
                    'success' => false,
                    'message' => 'PIX não encontrado'
                ],
                'token' => $token
            ]);
        }

        // Verifica se venceu
        if (now() > $pix->expires_at) {
            $pix->update(['status' => 'expired']);
            $status = 'expired';
        } else {
            $pix->update(['status' => 'paid']);
            $status = 'paid';
        }

        $result = [
            'success' => true,
            'status' => $status,
            'amount' => $pix->amount,
            'expires_at' => $pix->expires_at,
            'paid_at' => $status === 'paid' ? now() : null
        ];

        return view('web.pix.confirm', [
            'result' => $result,
            'token' => $token
        ]);
    }
}
