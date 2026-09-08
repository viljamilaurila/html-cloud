<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * One row per day of aggregate upload counts. Documents are hard-deleted when
 * they expire, so this is the only place upload history survives.
 */
#[Table(key: 'date', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['date', 'uploads'])]
class DailyStat extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'uploads' => 'integer',
        ];
    }

    /**
     * The increment is qualified with the table name because Postgres also
     * exposes the incoming row as "excluded", making a bare "uploads" ambiguous.
     */
    public static function recordUpload(): void
    {
        static::upsert(
            [['date' => now()->toDateString(), 'uploads' => 1]],
            ['date'],
            ['uploads' => DB::raw('daily_stats.uploads + 1')],
        );
    }
}
