<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Relations\HasOne;

class ThemePreset extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function userPage(): HasOne
    {
        return $this->hasOne(UserPage::class, 'theme_id');
    }
}
