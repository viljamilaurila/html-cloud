<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    // GET /sitemap.xml — lists public, indexable content pages only.
    // Auto-includes any param-free GET route; dynamic document routes
    // (/v/{id}, /e/{id}) and the API are excluded by construction.
    public function index(): Response
    {
        $locs = [];

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
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach (array_keys($locs) as $loc) {
            $xml .= '  <url><loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc></url>' . "\n";
        }

        $xml .= '</urlset>' . "\n";

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
