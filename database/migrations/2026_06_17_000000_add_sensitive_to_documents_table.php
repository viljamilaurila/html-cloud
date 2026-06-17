<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // When true, the viewer strips the view key from the address bar
            // (for sensitive docs). Default false = shareable: the address bar
            // is the working share link.
            $table->boolean('sensitive')->default(false)->after('size');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('sensitive');
        });
    }
};
