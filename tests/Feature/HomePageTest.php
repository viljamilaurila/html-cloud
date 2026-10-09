<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_hero_and_drop_zone(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Share an HTML file in seconds.', false);
        $response->assertSee('hd-hero-window', false);
        $response->assertSee('id="dropzone"', false);
    }

    public function test_home_page_ships_the_new_version_prompt_and_recent_uploads_hidden(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('class="version-prompt hidden" id="version-prompt"', false);
        $response->assertSee('class="recent-uploads hidden" id="recent-uploads"', false);
    }

    public function test_home_page_shows_the_claude_story_with_a_link_to_set_it_up(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Ask Claude to share it.', false);
        $response->assertSee('id="claude-story"', false);
        $response->assertSee(route('mcp'), false);
        $response->assertSee('Why not just publish the artifact?', false);
        $response->assertSee(route('vs.artifacts'), false);
    }

    public function test_home_page_explains_the_key_in_the_link_and_links_to_the_details(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('The key lives in the link.', false);
        $response->assertSee(route('security').'#threat-model', false);
        $this->get(route('security'))->assertSee('id="threat-model"', false);
    }

    public function test_home_page_names_the_problem_and_shows_real_situations(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('not a public page.', false);
        $response->assertSeeInOrder(['id="dropzone"', 'id="who-uses"', 'id="claude-story"'], false);
        foreach (['consultant', 'founder', 'designer', 'analyst', 'lead'] as $role) {
            $response->assertSee("id=\"who-panel-{$role}\"", false);
        }
    }

    public function test_home_page_title_leads_with_the_brand_as_words(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<title>HTML Cloud — Private HTML file sharing</title>', false);
    }

    public function test_home_page_no_longer_offers_the_extra_private_toggle(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('sensitive-toggle', false);
        $response->assertDontSee('Extra-private link', false);
    }

    public function test_home_page_preloads_the_headline_font(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '/<link rel="preload" href="[^"]*\/build\/assets\/inter-variable-latin-[^"]+\.woff2" as="font" type="font\/woff2" crossorigin>/',
            $response->getContent(),
        );
    }
}
