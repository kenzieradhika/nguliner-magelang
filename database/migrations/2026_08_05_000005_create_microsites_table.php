<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('microsites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained()->cascadeOnDelete();
            $table->string('hero_title')->nullable();
            $table->string('hero_image')->nullable();
            $table->text('about')->nullable();
            $table->json('menu')->nullable();
            $table->json('gallery')->nullable();
            $table->json('socials')->nullable();
            $table->string('map_embed')->nullable();
            $table->string('accent_color')->default('#111111');
            $table->string('cta_text')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('microsites');
    }
};
