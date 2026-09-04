<?php

namespace App\Models;

use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Table(keyType: 'string', incrementing: false)]
#[Fillable(['id', 'ciphertext', 'encrypted_view_key', 'edit_auth', 'expires_at', 'size', 'sensitive'])]
#[UseFactory(DocumentFactory::class)]
class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory, MassPrunable;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'sensitive' => 'boolean',
        ];
    }

    /**
     * Documents that can still be opened: no expiry, or one in the future.
     */
    #[Scope]
    protected function live(Builder $query): void
    {
        $query->where(fn (Builder $q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    /**
     * Expired documents are hard-deleted by the daily `model:prune` run.
     */
    public function prunable(): Builder
    {
        return static::whereNotNull('expires_at')->where('expires_at', '<=', now());
    }

    /**
     * Route binding never resolves an expired document: to visitors it is gone,
     * whether or not the prune job has caught up yet.
     */
    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->newQuery()->live()->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    /**
     * The edit key travels as base64url in the URL fragment; only its SHA-256
     * hex digest (edit_auth) is ever stored.
     */
    public function authorizesEdit(string $editKey): bool
    {
        $raw = base64_decode(strtr($editKey, '-_', '+/'), strict: true);

        return $raw !== false && hash_equals($this->edit_auth, hash('sha256', $raw));
    }

    public static function generateId(): string
    {
        do {
            $id = Str::random(8);
        } while (static::whereKey($id)->exists());

        return $id;
    }
}
