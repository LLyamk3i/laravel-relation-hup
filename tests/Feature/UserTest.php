<?php

use App\Enums\PostRelations;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has many posts', function () {
    $user = User::factory()->create();
    Post::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->posts()->list()->get())->toHaveCount(3);
    expect($user->posts()->list()->get())->each(fn ($post) => $post->user_id === $user->id);
    expect(User::with(PostRelations::LIST->value)->first()->getRelation(PostRelations::LIST->value))->toHaveCount(3);
});

it('has latest post', function () {
    $user = User::factory()->create();
    Post::factory()->count(3)->create(['user_id' => $user->id]);
    $latestPost = Post::factory()->create(['user_id' => $user->id, 'created_at' => now()]);

    expect($user->posts()->latest()->first())->toBeInstanceOf(Post::class);
    expect($user->posts()->latest()->first()->is($latestPost))->toBeTrue();
    expect(User::with(PostRelations::LATEST->value)->first()->getRelation(PostRelations::LATEST->value))->toBeInstanceOf(Post::class);
});

it('has oldest post', function () {
    $user = User::factory()->create();
    $oldestPost = Post::factory()->create(['user_id' => $user->id, 'created_at' => now()->subDays(10)]);
    Post::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->posts()->older()->first())->toBeInstanceOf(Post::class);
    expect($user->posts()->older()->first()->is($oldestPost))->toBeTrue();
    expect(User::with(PostRelations::OLDER->value)->first()->getRelation(PostRelations::OLDER->value))->toBeInstanceOf(Post::class);
});

it('has active post', function () {
    $user = User::factory()->create();
    Post::factory()->count(3)->create(['user_id' => $user->id, 'enabled' => false]);
    $activePost = Post::factory()->create(['user_id' => $user->id, 'enabled' => true]);

    expect($user->posts()->actives()->first())->toBeInstanceOf(Post::class);
    expect($user->posts()->actives()->first()->is($activePost))->toBeTrue();
    expect(User::with(PostRelations::ACTIVES->value)->first()->getRelation(PostRelations::ACTIVES->value)->first())->toBeInstanceOf(Post::class);
});
