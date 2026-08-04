<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ig_posts', function (Blueprint $table) {
            $table->id();
            $table->string('ig_id')->unique();
            $table->string('image_url')->nullable();
            $table->string('permalink')->nullable();
            $table->text('caption')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ig_posts');
    }
};
