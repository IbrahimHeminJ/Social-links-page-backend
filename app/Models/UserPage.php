<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPage extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function themePreset(): BelongsTo
    {
        return $this->belongsTo(ThemePreset::class, 'theme_id', 'id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
