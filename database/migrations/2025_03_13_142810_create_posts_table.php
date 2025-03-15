<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'posts', callback: function (Blueprint $table) {
            $table->ulid(column: 'id')->primary();
            $table->string(column: 'title');
            $table->text(column: 'content');
            $table->string(column: 'slug')->unique();
            $table->boolean(column: 'enabled')->default(false);
            $table->foreignUlid(column: 'user_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'posts');
    }
};
