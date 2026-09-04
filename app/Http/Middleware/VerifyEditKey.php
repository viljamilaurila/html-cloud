<?php

namespace App\Http\Middleware;

use App\Models\Document;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authorizes writes to a document. There are no accounts: whoever presents the
 * edit key (a secret only the uploader holds) may replace or delete the file.
 * Runs after route binding, so `{document}` is already resolved and live.
 */
class VerifyEditKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $editKey = $request->validate(['edit_key' => ['required', 'string']])['edit_key'];

        $document = $request->route('document');

        if (! $document instanceof Document || ! $document->authorizesEdit($editKey)) {
            return response()->json(['error' => 'Invalid edit key'], 403);
        }

        return $next($request);
    }
}
