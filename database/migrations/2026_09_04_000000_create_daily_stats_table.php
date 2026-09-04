<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aggregate counters only — one row per day, nothing per document.
        // documents:prune hard-deletes expired rows, so this is the only place
        // upload history survives.
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->date('date')->primary();
            $table->unsignedInteger('uploads')->default(0);
        });

        // Seed from whatever is still in the documents table so history does
        // not start from zero today.
        $existing = DB::table('documents')
            ->selectRaw('DATE(created_at) as day, COUNT(*) as n')
            ->groupBy('day')
            ->get();

        foreach ($existing as $row) {
            DB::table('daily_stats')->insert(['date' => $row->day, 'uploads' => $row->n]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_stats');
    }
};
