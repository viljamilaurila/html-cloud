<?php

namespace App\Models;

use App\Enums\StatCounter;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * One row per day of aggregate counts (see StatCounter). Documents are
 * hard-deleted when they expire, so this is the only place history survives.
 */
#[Table(key: 'date', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['date', 'uploads', 'tiny_uploads', 'web_uploads', 'cli_uploads', 'mcp_uploads', 'updates', 'opens', 'deletes'])]
class DailyStat extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'uploads' => 'integer',
            'tiny_uploads' => 'integer',
            'web_uploads' => 'integer',
            'cli_uploads' => 'integer',
            'mcp_uploads' => 'integer',
            'updates' => 'integer',
            'opens' => 'integer',
            'deletes' => 'integer',
        ];
    }

    /**
     * Add one to each given counter for today, in a single upsert. Increments
     * are qualified with the table name because Postgres also exposes the
     * incoming row as "excluded", making a bare column name ambiguous.
     */
    public static function bump(?StatCounter ...$counters): void
    {
        $columns = collect($counters)->filter()->map(fn (StatCounter $counter) => $counter->value)->unique();

        if ($columns->isEmpty()) {
            return;
        }

        static::upsert(
            [['date' => now()->toDateString(), ...$columns->mapWithKeys(fn (string $column) => [$column => 1])]],
            ['date'],
            $columns->mapWithKeys(fn (string $column) => [$column => DB::raw("daily_stats.{$column} + 1")])->all(),
        );
    }
}
