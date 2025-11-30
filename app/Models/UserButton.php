<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserButton extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function buttonLink(): BelongsTo
    {
        return $this->belongsTo(ButtonLink::class, 'button_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}
