<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'readable_code',
        'qr_code',
        'valid_until',
        'personal_app_link',
        'business_app_link',
        'corporate_app_link',
        'amount',
        'currency',
        'status',
        'declining_reason',
        'declined_at',
        'paid_by_name',
        'paid_by_iban',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'declined_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
