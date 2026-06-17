<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'ciphertext',
        'encrypted_view_key',
        'edit_auth',
        'expires_at',
        'size',
        'sensitive',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'sensitive'  => 'boolean',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
