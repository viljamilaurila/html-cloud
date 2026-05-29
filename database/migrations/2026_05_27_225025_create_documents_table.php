<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->string('id', 12)->primary();
            // AES-256-GCM viewKey, encrypted with editKey — base64url encoded
            $table->text('encrypted_view_key');
            // SHA-256(editKey) hex — used to verify edit authorization
            $table->string('edit_auth', 64);
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('size')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
