<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_image',
        'phone_no',
        'role',
        'is_deleted',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_deleted' => 'boolean', 
        ];
    }

    //relationships

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'user_tags')->withTimestamps();
    }
    public function userButtons(): HasMany
    {
        return $this->hasMany(UserButton::class);
    }
    public function userPage(): HasOne
    {
        return $this->hasOne(UserPage::class);
    }
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
    public function handledReports(): HasMany
    {
        return $this->hasMany(Report::class,'handled_by','id');
    }
}
