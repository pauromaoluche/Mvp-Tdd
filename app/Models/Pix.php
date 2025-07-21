<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Pix extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'token', 'status', 'expires_at'];
    
    protected $casts = [
        'expires_at' => 'datetime'
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se o PIX está expirado
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Marca o PIX como pago se não estiver expirado
     */
    public function markAsPaid(): bool
    {
        if ($this->isExpired()) {
            $this->update(['status' => 'expired']);
            return false;
        }

        $this->update(['status' => 'paid']);
        return true;
    }

    /**
     * Marca PIXs expirados automaticamente
     */
    public static function markExpiredPixs(): void
    {
        static::where('status', 'generated')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'expired']);
    }
}
