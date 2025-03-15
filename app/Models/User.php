<?php

namespace App\Models;

use App\Models\User\PostRelationships;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[UseFactory(UserFactory::class)]
class User extends Authenticatable
{
    use \App\Concerns\Eloquent\ForwardToHubRelationshipMethod;
    use \Illuminate\Database\Eloquent\Concerns\HasUlids;
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function demo(): HasMany
    {
        return $this->hasMany(related: Post::class);
    }

    public function posts(): PostRelationships
    {
        return new PostRelationships(model: $this);
    }
}
