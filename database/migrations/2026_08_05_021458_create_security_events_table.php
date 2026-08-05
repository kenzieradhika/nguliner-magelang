<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60)->index();            // login_failed, login_locked, session_hijack, csrf_mismatch, login_success
            $table->string('severity', 20)->default('medium'); // info | low | medium | high | critical
            $table->string('message', 255);
            $table->json('details')->nullable();
            $table->string('ip', 45)->nullable()->index();
            $table->string('user_agent', 500)->nullable();
            $table->string('url', 500)->nullable();
            $table->unsignedInteger('count')->default(1);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'ip']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
