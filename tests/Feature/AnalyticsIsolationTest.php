<?php

namespace Tests\Feature;

use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The Google Ads tag reads document.location.href. On /v/{id}#{viewKey} and
 * /e/{id}#{editKey} that URL contains the decryption key, so the tag must never
 * be served on those routes — doing so would hand the key to a third-party
 * script and break the zero-knowledge claim /security makes.
 *
 * These tests are the guard rail for that invariant. If one fails, do not
 * "fix" it by loosening the assertion.
 */
class AnalyticsIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.google_ads.id' => 'AW-TEST123']);
    }

    private function makeDocument(): Document
    {
        return Document::create([
            'id'                 => 'testdoc12345',
            'ciphertext'         => 'ZmFrZQ==',
            'encrypted_view_key' => 'ZmFrZQ==',
            'edit_auth'          => str_repeat('a', 64),
            'expires_at'         => null,
            'size'               => 42,
            'sensitive'          => false,
        ]);
    }

    public function test_the_tag_is_absent_from_the_viewer(): void
    {
        $doc = $this->makeDocument();

        $this->get("/v/{$doc->id}")
            ->assertOk()
            ->assertDontSee('googletagmanager.com', escape: false)
            ->assertDontSee('AW-TEST123');
    }

    public function test_the_tag_is_absent_from_the_editor(): void
    {
        $doc = $this->makeDocument();

        $this->get("/e/{$doc->id}")
            ->assertOk()
            ->assertDontSee('googletagmanager.com', escape: false)
            ->assertDontSee('AW-TEST123');
    }

    public function test_the_tag_is_present_on_the_home_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('googletagmanager.com', escape: false)
            ->assertSee('AW-TEST123');
    }

    public function test_the_tag_is_present_on_marketing_pages(): void
    {
        foreach (['/security', '/cli', '/mcp', '/vs/codepen', '/share-claude-artifact'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('AW-TEST123');
        }
    }

    public function test_no_tag_is_emitted_when_the_id_is_unset(): void
    {
        config(['services.google_ads.id' => null]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('googletagmanager.com', escape: false);
    }

    public function test_viewer_sends_no_csp_so_srcdoc_documents_render_intact(): void
    {
        $doc = $this->makeDocument();

        $response = $this->get("/v/{$doc->id}")->assertOk();

        // srcdoc frames inherit the embedder's CSP; a policy here would break
        // the arbitrary HTML people upload.
        $this->assertFalse($response->headers->has('Content-Security-Policy'));
        $this->assertSame('no-referrer', $response->headers->get('Referrer-Policy'));
    }

    public function test_editor_sends_a_strict_csp(): void
    {
        $doc = $this->makeDocument();

        $csp = $this->get("/e/{$doc->id}")->assertOk()
            ->headers->get('Content-Security-Policy');

        $this->assertNotNull($csp);
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertMatchesRegularExpression("/script-src 'self' 'nonce-[^']+'/", $csp);
        $this->assertStringNotContainsString('googletagmanager', $csp);
    }
}
