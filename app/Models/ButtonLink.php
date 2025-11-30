<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ButtonLink extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function userButtons(): HasMany
    {
        return $this->hasMany(UserButton::class);
    }
}
