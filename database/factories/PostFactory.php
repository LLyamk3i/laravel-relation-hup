<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $title = fake()->sentence(),
            'content' => fake()->paragraph(),
            'slug' => Str::slug($title),
            'enabled' => fake()->boolean(),
            'user_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function user(User $user)
    {
        return $this->state([
            'user_id' => $user->id,
        ]);
    }
}
