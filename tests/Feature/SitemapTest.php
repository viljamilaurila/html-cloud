<?php

namespace Tests\Feature;

use Tests\TestCase;

class SitemapTest extends TestCase
{
    public function test_sitemap_lists_content_pages_but_not_privacy_policies_or_the_uploads_list(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertSee('<loc>'.url('/security').'</loc>', false);
        $response->assertSee('<loc>'.url('/mcp').'</loc>', false);
        $response->assertDontSee('/extension-privacy', false);
        $response->assertDontSee('/mcp-privacy', false);
        $response->assertDontSee('/uploads', false);
    }
}
