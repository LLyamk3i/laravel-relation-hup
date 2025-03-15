<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final readonly class PostRelationships
{
    public function __construct(
        private readonly User $model,
    ) {
        //
    }

    public function list(): HasMany
    {
        return $this->model->hasMany(related: Post::class);
    }

    public function latest(): HasOne
    {
        return $this->model->hasOne(related: Post::class)->latestOfMany();
    }

    public function older(): HasOne
    {
        return $this->model->hasOne(related: Post::class)->oldestOfMany();
    }

    public function actives(): HasMany
    {
        return $this->model->hasMany(related: Post::class)->where('enabled', true);
    }
}
