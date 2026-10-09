<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Still aggregate counters only — one row per day, nothing per document or
     * per visitor. `uploads` keeps counting every upload; the new columns break
     * it down and add what happens to documents after they're shared.
     */
    public function up(): void
    {
        Schema::table('daily_stats', function (Blueprint $table) {
            $table->unsignedInteger('tiny_uploads')->default(0);
            $table->unsignedInteger('web_uploads')->default(0);
            $table->unsignedInteger('cli_uploads')->default(0);
            $table->unsignedInteger('mcp_uploads')->default(0);
            $table->unsignedInteger('updates')->default(0);
            $table->unsignedInteger('opens')->default(0);
            $table->unsignedInteger('deletes')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('daily_stats', function (Blueprint $table) {
            $table->dropColumn(['tiny_uploads', 'web_uploads', 'cli_uploads', 'mcp_uploads', 'updates', 'opens', 'deletes']);
        });
    }
};
