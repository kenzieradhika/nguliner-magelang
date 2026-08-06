<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('NGuliner');
            $table->string('tagline')->default('Referensi Kuliner No.1 di Magelang');
            $table->string('whatsapp', 30)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('instagram', 60)->default('@ngulinermagelang');
            $table->string('instagram_url', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('hero_eyebrow', 120)->default('Kuliner Magelang & Sekitarnya');
            $table->string('hero_title', 120)->default('Referensi Kuliner');
            $table->string('hero_title_italic', 80)->default('No.1 di Magelang');
            $table->text('hero_subtitle')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('copyright', 120)->default('NGuliner Magelang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
