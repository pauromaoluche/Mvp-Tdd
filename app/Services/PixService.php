<?php


namespace App\Services;

use App\Models\Pix;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PixService
{
    /**
     * Cria um novo PIX
     */
    public function create(): Pix
    {
        return Pix::create([
            'user_id' => Auth::id(),
            'token' => Str::uuid(),
            'status' => 'generated',
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
    }

    /**
     * Lista todos os PIXs do usuário logado
     */
    public function listUserPixs(): \Illuminate\Database\Eloquent\Collection
    {
        Pix::markExpiredPixs();
        
        return Pix::where('user_id', Auth::id())
                ->orderByDesc('created_at')
                ->get();
    }

    /**
     * Busca um PIX pelo token
     */
    public function findByToken(string $token): ?Pix
    {
        return Pix::where('token', $token)->first();
    }

    /**
     * Confirma o pagamento de um PIX
     */
    public function confirmPayment(string $token): array
    {
        $pix = $this->findByToken($token);

        if (!$pix) {
            return [
                'success' => false,
                'message' => 'PIX não encontrado.',
                'status' => null
            ];
        }

        // Marca PIXs expirados antes de processar
        Pix::markExpiredPixs();

        // Recarrega o PIX para verificar se foi marcado como expirado
        $pix->refresh();

        if ($pix->status === 'paid') {
            return [
                'success' => false,
                'message' => 'Este PIX já foi pago anteriormente.',
                'status' => 'paid',
                'pix' => $pix
            ];
        }

        if ($pix->status === 'expired' || $pix->isExpired()) {
            $pix->update(['status' => 'expired']);
            return [
                'success' => false,
                'message' => 'Este PIX está expirado.',
                'status' => 'expired',
                'pix' => $pix
            ];
        }

        // Marca como pago
        $success = $pix->markAsPaid();

        return [
            'success' => $success,
            'message' => $success ? 'Pagamento confirmado com sucesso!' : 'Erro ao processar pagamento.',
            'status' => 'paid',
            'pix' => $pix
        ];
    }
}
