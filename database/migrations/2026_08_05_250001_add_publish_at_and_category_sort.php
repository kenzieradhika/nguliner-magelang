<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->timestamp('publish_at')->nullable()->after('is_published');
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->timestamp('publish_at')->nullable()->after('is_published');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn('publish_at');
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('publish_at');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};