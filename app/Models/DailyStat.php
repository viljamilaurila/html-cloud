<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DailyStat extends Model
{
    protected $primaryKey = 'date';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['date', 'uploads'];

    protected $casts = [
        'date'    => 'date',
        'uploads' => 'integer',
    ];

    public static function recordUpload(): void
    {
        static::upsert(
            [['date' => now()->toDateString(), 'uploads' => 1]],
            ['date'],
            ['uploads' => DB::raw('uploads + 1')],
        );
    }
}
