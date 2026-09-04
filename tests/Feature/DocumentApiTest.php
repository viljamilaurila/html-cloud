<?php

namespace Tests\Feature;

use App\Models\DailyStat;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentApiTest extends TestCase
{
    use RefreshDatabase;

    /** base64url of 32 random bytes, exactly as the browser puts it after the #. */
    private string $editKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->editKey = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * @return array<string, mixed>
     */
    private function uploadPayload(array $overrides = []): array
    {
        return [
            'ciphertext' => 'ZmFrZQ==',
            'encrypted_view_key' => 'ZmFrZQ==',
            'edit_auth' => str_repeat('b', 64),
            'size' => 42,
            ...$overrides,
        ];
    }

    private function editable(): Document
    {
        return Document::factory()->editableWith($this->editKey)->create();
    }

    public function test_upload_stores_the_ciphertext_and_returns_an_id(): void
    {
        $response = $this->postJson('/api/documents', $this->uploadPayload())->assertCreated();

        $document = Document::findOrFail($response->json('id'));

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{8}$/', $document->id);
        $this->assertSame('ZmFrZQ==', $document->ciphertext);
        $this->assertSame(42, $document->size);
        $this->assertFalse($document->sensitive);
        $this->assertTrue($document->expires_at->isSameDay(now()->addDays(30)), 'defaults to 30-day expiry');
        $this->assertSame(1, DailyStat::findOrFail(now()->toDateString())->uploads);
    }

    public function test_upload_honours_expiry_and_sensitivity_options(): void
    {
        $week = $this->postJson('/api/documents', $this->uploadPayload(['expires_in' => '7', 'sensitive' => true]))->assertCreated();
        $never = $this->postJson('/api/documents', $this->uploadPayload(['expires_in' => 'never']))->assertCreated();

        $this->assertTrue(Document::findOrFail($week->json('id'))->expires_at->isSameDay(now()->addDays(7)));
        $this->assertTrue(Document::findOrFail($week->json('id'))->sensitive);
        $this->assertNull(Document::findOrFail($never->json('id'))->expires_at);
    }

    public function test_upload_rejects_malformed_payloads(): void
    {
        $this->postJson('/api/documents', $this->uploadPayload(['edit_auth' => 'nope']))
            ->assertUnprocessable()->assertJsonValidationErrors('edit_auth');

        $this->postJson('/api/documents', $this->uploadPayload(['expires_in' => 'tomorrow']))
            ->assertUnprocessable()->assertJsonValidationErrors('expires_in');

        $this->postJson('/api/documents', $this->uploadPayload(['size' => 11 * 1024 * 1024]))
            ->assertUnprocessable()->assertJsonValidationErrors('size');

        // Must fit MySQL's 16 MB mediumText column rather than be truncated.
        $this->postJson('/api/documents', $this->uploadPayload(['ciphertext' => str_repeat('a', 14 * 1024 * 1024 + 1)]))
            ->assertUnprocessable()->assertJsonValidationErrors('ciphertext');

        $this->assertSame(0, Document::count());
    }

    public function test_uploads_are_rate_limited(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->postJson('/api/documents', $this->uploadPayload())->assertCreated();
        }

        $this->postJson('/api/documents', $this->uploadPayload())->assertTooManyRequests();
    }

    public function test_show_returns_what_the_viewer_needs_to_decrypt(): void
    {
        $document = Document::factory()->create(['sensitive' => true]);

        $this->getJson("/api/documents/{$document->id}")
            ->assertOk()
            ->assertExactJson([
                'id' => $document->id,
                'ciphertext' => $document->ciphertext,
                'encrypted_view_key' => $document->encrypted_view_key,
                'expires_at' => $document->expires_at->toIso8601String(),
                'size' => 42,
                'sensitive' => true,
            ]);
    }

    public function test_expired_and_unknown_documents_are_indistinguishable(): void
    {
        $expired = Document::factory()->expired()->create();

        foreach ([$expired->id, 'nope1234'] as $id) {
            $this->getJson("/api/documents/{$id}")
                ->assertNotFound()
                ->assertExactJson(['error' => 'Not found or expired']);

            $this->get("/v/{$id}")->assertNotFound()->assertSee('This file is gone');
            $this->get("/e/{$id}")->assertNotFound()->assertSee('This file is gone');
        }

        $this->deleteJson("/api/documents/{$expired->id}", ['edit_key' => $this->editKey])->assertNotFound();
    }

    public function test_viewer_and_editor_pages_render_for_live_documents(): void
    {
        $document = Document::factory()->permanent()->create();

        $this->get("/v/{$document->id}/my-report")->assertOk()->assertSee('my-report — html.cloud');
        $this->get("/e/{$document->id}")->assertOk()->assertSee($document->id);
    }

    public function test_replacing_content_requires_the_edit_key(): void
    {
        $document = $this->editable();
        $payload = ['ciphertext' => 'bmV3', 'encrypted_view_key' => 'bmV3', 'size' => 3];

        $this->putJson("/api/documents/{$document->id}", $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('edit_key');

        $this->putJson("/api/documents/{$document->id}", [...$payload, 'edit_key' => 'd3Jvbmc'])
            ->assertForbidden()->assertExactJson(['error' => 'Invalid edit key']);

        $this->putJson("/api/documents/{$document->id}", [...$payload, 'edit_key' => $this->editKey])
            ->assertOk()->assertExactJson(['ok' => true]);

        $this->assertSame('bmV3', $document->fresh()->ciphertext);
        $this->assertSame(3, $document->fresh()->size);
    }

    public function test_expiry_and_sensitivity_can_be_changed_with_the_edit_key(): void
    {
        $document = $this->editable();

        $this->patchJson("/api/documents/{$document->id}/expiry", ['expires_in' => 'never', 'edit_key' => 'd3Jvbmc'])
            ->assertForbidden();

        $this->patchJson("/api/documents/{$document->id}/expiry", ['expires_in' => 'never', 'edit_key' => $this->editKey])
            ->assertOk()->assertExactJson(['expires_at' => null]);
        $this->assertNull($document->fresh()->expires_at);

        $this->patchJson("/api/documents/{$document->id}/expiry", ['expires_in' => '7', 'edit_key' => $this->editKey])
            ->assertOk();
        $this->assertTrue($document->fresh()->expires_at->isSameDay(now()->addDays(7)));

        $this->patchJson("/api/documents/{$document->id}/settings", ['sensitive' => true, 'edit_key' => $this->editKey])
            ->assertOk()->assertExactJson(['sensitive' => true]);
        $this->assertTrue($document->fresh()->sensitive);
    }

    public function test_deleting_requires_the_edit_key(): void
    {
        $document = $this->editable();

        $this->deleteJson("/api/documents/{$document->id}", ['edit_key' => 'd3Jvbmc'])->assertForbidden();
        $this->assertModelExists($document);

        $this->deleteJson("/api/documents/{$document->id}", ['edit_key' => $this->editKey])
            ->assertOk()->assertExactJson(['ok' => true]);
        $this->assertModelMissing($document);
    }

    public function test_pages_set_no_cookies_and_start_no_session(): void
    {
        $document = Document::factory()->permanent()->create();

        foreach (['/', '/uploads', "/v/{$document->id}", "/e/{$document->id}"] as $path) {
            $this->assertSame([], $this->get($path)->assertOk()->headers->getCookies(), "{$path} sets a cookie");
        }
    }
}
