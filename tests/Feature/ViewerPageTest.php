<?php

namespace Tests\Feature;

use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewerPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewer_ships_the_owner_replace_controls_hidden(): void
    {
        $document = Document::factory()->create();

        $response = $this->get("/v/{$document->id}");

        $response->assertOk();
        $response->assertSee('id="replace-drop" class="replace-drop hidden"', false);
        $response->assertSee('id="replace-confirm" class="replace-confirm hidden"', false);
        $response->assertSee('class="hc-badge-replace hidden"', false);
    }
}
