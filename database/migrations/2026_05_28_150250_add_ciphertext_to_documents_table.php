<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Store ciphertext in DB instead of filesystem so it survives deployments.
            // mediumText supports up to ~16 MB — covers 10 MB files with base64 overhead.
            // Nullable so existing rows don't break on migration.
            $table->mediumText('ciphertext')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('ciphertext');
        });
    }
};
