<?php

namespace Tests\Feature;

use App\Models\DailyStat;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guard rails for the zero-knowledge claim /security makes.
 *
 * /v/{id}#{viewKey} and /e/{id}#{editKey} carry the decryption key in the URL
 * fragment. Any third-party script on those pages could read
 * document.location.href and exfiltrate it — so the site serves no third-party
 * scripts anywhere, and every page except the viewer enforces that with CSP.
 * If one of these fails, do not "fix" it by loosening the assertion.
 */
class SiteSecurityTest extends TestCase
{
    use RefreshDatabase;

    private const PAGES = ['/', '/security', '/cli', '/mcp', '/vs/codepen', '/share-claude-artifact', '/extension-privacy', '/uploads'];

    private function makeDocument(): Document
    {
        return Document::factory()->permanent()->create();
    }

    public function test_no_page_loads_a_third_party_script(): void
    {
        $doc = $this->makeDocument();

        foreach ([...self::PAGES, "/v/{$doc->id}", "/e/{$doc->id}"] as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            preg_match_all('/<script[^>]+src="([^"]+)"/i', $html, $m);
            foreach ($m[1] as $src) {
                $this->assertStringNotContainsString('//', str_replace(config('app.url'), '', $src),
                    "{$path} loads an external script: {$src}");
            }
            $this->assertStringNotContainsString('googletagmanager', $html);
        }
    }

    public function test_every_page_except_the_viewer_sends_a_strict_csp(): void
    {
        $doc = $this->makeDocument();

        foreach ([...self::PAGES, "/e/{$doc->id}"] as $path) {
            $csp = $this->get($path)->assertOk()->headers->get('Content-Security-Policy');

            $this->assertNotNull($csp, "{$path} has no CSP");
            $this->assertStringContainsString("default-src 'self'", $csp);
            $this->assertMatchesRegularExpression("/script-src 'self' 'nonce-[^']+'/", $csp);
        }
    }

    public function test_viewer_sends_no_csp_so_srcdoc_documents_render_intact(): void
    {
        $doc = $this->makeDocument();

        $response = $this->get("/v/{$doc->id}")->assertOk();

        // srcdoc frames inherit the embedder's CSP; a policy here would break
        // the arbitrary HTML people upload.
        $this->assertFalse($response->headers->has('Content-Security-Policy'));
        $this->assertSame('no-referrer', $response->headers->get('Referrer-Policy'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
    }

    public function test_hsts_is_sent_only_in_production(): void
    {
        $this->assertFalse($this->get('/')->headers->has('Strict-Transport-Security'));

        $this->app['env'] = 'production';
        $this->assertSame(
            'max-age=31536000; includeSubDomains',
            $this->get('/')->headers->get('Strict-Transport-Security'),
        );
    }

    public function test_security_txt_is_published_and_not_expired(): void
    {
        $txt = file_get_contents(public_path('.well-known/security.txt'));

        $this->assertStringContainsString('Contact: https://', $txt);
        preg_match('/^Expires: (.+)$/m', $txt, $m);
        $this->assertTrue(strtotime($m[1]) > time(), 'security.txt has expired — bump the Expires line');
    }

    public function test_upload_counts_survive_document_expiry(): void
    {
        $payload = [
            'ciphertext' => 'ZmFrZQ==',
            'encrypted_view_key' => 'ZmFrZQ==',
            'edit_auth' => str_repeat('b', 64),
            'expires_in' => '7',
            'size' => 42,
        ];
        $this->postJson('/api/documents', $payload)->assertCreated();
        $this->postJson('/api/documents', $payload)->assertCreated();

        $this->assertSame(2, DailyStat::find(now()->toDateString())->uploads);

        Document::query()->update(['expires_at' => now()->subDay()]);
        $this->artisan('model:prune')->assertSuccessful();

        $this->assertSame(0, Document::count());
        $this->assertSame(2, DailyStat::find(now()->toDateString())->uploads);
    }
}
