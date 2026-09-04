<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline security headers, plus a strict Content-Security-Policy on every
 * page except the viewer.
 *
 * Referrer-Policy is belt-and-braces: browsers never put the fragment in a
 * Referer header, so the key cannot leak that way, but `no-referrer` also stops
 * the bare document id in /v/{id} reaching third parties (Google Fonts).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Must be set before the view renders so @vite tags carry the nonce.
        $nonce = $this->needsCsp($request) ? Vite::useCspNonce() : null;

        $response = $next($request);

        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Not sent as CSP frame-ancestors: X-Frame-Options is checked against the
        // HTTP response, so it governs this page without reaching the srcdoc
        // document the viewer renders inside it.
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Production only: TLS terminates at the proxy, so the request itself
        // may look insecure here, and HSTS on a plain-http dev origin would pin
        // localhost to https.
        if (app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($nonce !== null) {
            $response->headers->set('Content-Security-Policy', $this->policy($nonce));
        }

        return $response;
    }

    /**
     * Every page except the viewer gets the strict policy: the site serves no
     * third-party scripts, so `script-src 'self' + nonce` costs nothing.
     *
     * The viewer deliberately gets NO CSP. It renders the decrypted document via
     * `iframe srcdoc`, and srcdoc documents inherit the embedder's CSP — any
     * policy we set here would also apply inside the frame and break the
     * arbitrary HTML people upload (CDN scripts, inline styles, remote images).
     * Containing the viewer properly means serving the frame from a separate
     * sandbox origin; until then the protection there is keeping third-party
     * scripts off the page entirely.
     *
     * Skipped while the Vite dev server is hot, since its client is served from
     * another origin and injects inline styles.
     */
    private function needsCsp(Request $request): bool
    {
        return ! $request->routeIs('viewer') && ! Vite::isRunningHot();
    }

    private function policy(string $nonce): string
    {
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}'",
            // 'unsafe-inline' covers style="" attributes, which nonces cannot.
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data:",
            "connect-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
        ]);
    }
}
