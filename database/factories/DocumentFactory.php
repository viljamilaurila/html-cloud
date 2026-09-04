<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::random(8),
            'ciphertext' => base64_encode(random_bytes(32)),
            'encrypted_view_key' => base64_encode(random_bytes(32)),
            'edit_auth' => hash('sha256', random_bytes(32)),
            'expires_at' => now()->addDays(30),
            'size' => 42,
            'sensitive' => false,
        ];
    }

    /**
     * A document whose edit key (base64url, as it appears in /e/{id}#key) the
     * test knows, so it can authorize writes.
     */
    public function editableWith(string $editKey): static
    {
        return $this->state([
            'edit_auth' => hash('sha256', base64_decode(strtr($editKey, '-_', '+/'))),
        ]);
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }

    public function permanent(): static
    {
        return $this->state(['expires_at' => null]);
    }
}
