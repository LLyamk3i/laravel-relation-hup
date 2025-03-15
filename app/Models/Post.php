<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[UseFactory(PostFactory::class)]
class Post extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasUlids;
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $casts = [
        'enabled' => 'boolean',
    ];

    //
    public function user(): HasOne
    {
        return $this->hasOne(related: User::class);
    }
}
