<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    // Images to advertise per page URI (path relative to public/), each
    // included only if the file actually exists.
    private const IMAGES = [
        'vs/netlify-drop' => 'screenshots/netlify-drop.webp',
        'vs/codepen'      => 'screenshots/codepen.webp',
    ];

    // GET /sitemap.xml — lists public, indexable content pages only.
    // Auto-includes any param-free GET route; dynamic document routes
    // (/v/{id}, /e/{id}) and the API are excluded by construction.
    public function index(): Response
    {
        $locs = [];

        $imageMap = [];
        foreach (self::IMAGES as $uri => $rel) {
            if (is_file(public_path($rel))) {
                $imageMap[url('/' . $uri)][] = asset($rel);
            }
        }

        foreach (Route::getRoutes() as $route) {
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }

            $uri = $route->uri();

            if (str_contains($uri, '{')) {
                continue; // skip routes with parameters
            }
            if ($uri === 'api' || str_starts_with($uri, 'api/')) {
                continue; // skip API
            }
            if (in_array($uri, ['sitemap.xml', 'up'], true)) {
                continue; // skip self and the framework health check
            }

            $locs[url($uri === '/' ? '/' : '/' . $uri)] = true;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
            . ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach (array_keys($locs) as $loc) {
            $xml .= '  <url><loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc>';
            foreach ($imageMap[$loc] ?? [] as $img) {
                $xml .= '<image:image><image:loc>'
                    . htmlspecialchars($img, ENT_XML1)
                    . '</image:loc></image:image>';
            }
            $xml .= '</url>' . "\n";
        }

        $xml .= '</urlset>' . "\n";

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
