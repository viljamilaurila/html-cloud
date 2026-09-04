<?php

namespace App\Http\Controllers;

use App\Enums\Expiry;
use App\Models\DailyStat;
use App\Models\Document;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * Writes (update, expiry, settings, destroy) are guarded by the VerifyEditKey
 * middleware on their routes; a missing or expired {document} is a 404 before
 * any method here runs (see Document::resolveRouteBinding).
 */
class DocumentController extends Controller
{
    private const MAX_SIZE_BYTES = 10 * 1024 * 1024; // 10 MB of plaintext

    // Base64 of 10 MB plus IV and tag is ~13.4M characters. Capping at 14 MB
    // keeps the value inside MySQL's 16 MB mediumText column, so an oversized
    // upload fails validation instead of being truncated or erroring on insert.
    private const MAX_CIPHERTEXT_CHARS = 14 * 1024 * 1024;

    // GET /v/{document}/{slug?}
    // The slug is cosmetic — only {document} identifies the file. It exists so
    // the link is self-describing and so link previews (WhatsApp, Slack, …) can
    // show a title, which crawlers read from the server-rendered meta tags (they
    // never get the #key). Nothing about the slug is stored.
    public function viewer(Document $document, ?string $slug = null): View
    {
        return view('viewer', [
            'doc' => $document,
            'slugTitle' => $slug, // shown verbatim, in slug form
        ]);
    }

    // GET /e/{document}
    public function editor(Document $document): View
    {
        return view('editor', ['doc' => $document]);
    }

    // POST /api/documents — upload an encrypted blob
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            ...$this->payloadRules(),
            'edit_auth' => ['required', 'string', 'size:64', 'regex:/^[0-9a-f]+$/'],
            'expires_in' => ['nullable', Rule::enum(Expiry::class)],
            'sensitive' => ['nullable', 'boolean'],
        ]);

        $document = Document::create([
            ...Arr::only($data, ['ciphertext', 'encrypted_view_key', 'edit_auth', 'size']),
            'id' => Document::generateId(),
            'expires_at' => ($request->enum('expires_in', Expiry::class) ?? Expiry::Month)->expiresAt(),
            'sensitive' => $request->boolean('sensitive'),
        ]);

        DailyStat::recordUpload();

        return response()->json(['id' => $document->id], 201);
    }

    // GET /api/documents/{document} — ciphertext plus what the client needs to decrypt it
    public function show(Document $document): JsonResponse
    {
        if ($document->ciphertext === null) {
            return response()->json(['error' => 'Content missing'], 404);
        }

        return response()->json([
            'id' => $document->id,
            'ciphertext' => $document->ciphertext,
            'encrypted_view_key' => $document->encrypted_view_key,
            'expires_at' => $document->expires_at?->toIso8601String(),
            'size' => $document->size,
            'sensitive' => $document->sensitive,
        ]);
    }

    // PUT /api/documents/{document} — replace content
    public function update(Request $request, Document $document): JsonResponse
    {
        $document->update($request->validate($this->payloadRules()));

        return response()->json(['ok' => true]);
    }

    // PATCH /api/documents/{document}/expiry
    public function updateExpiry(Request $request, Document $document): JsonResponse
    {
        $request->validate(['expires_in' => ['required', Rule::enum(Expiry::class)]]);

        $document->update([
            'expires_at' => $request->enum('expires_in', Expiry::class)->expiresAt(),
        ]);

        return response()->json(['expires_at' => $document->expires_at?->toIso8601String()]);
    }

    // PATCH /api/documents/{document}/settings
    public function updateSettings(Request $request, Document $document): JsonResponse
    {
        $request->validate(['sensitive' => ['required', 'boolean']]);

        $document->update(['sensitive' => $request->boolean('sensitive')]);

        return response()->json(['sensitive' => $document->sensitive]);
    }

    // DELETE /api/documents/{document}
    public function destroy(Document $document): JsonResponse
    {
        $document->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * The encrypted payload itself, shared by upload and replace.
     *
     * @return array<string, array<int, string|Rule>>
     */
    private function payloadRules(): array
    {
        return [
            'ciphertext' => ['required', 'string', 'max:'.self::MAX_CIPHERTEXT_CHARS],
            'encrypted_view_key' => ['required', 'string', 'max:1000'],
            'size' => ['required', 'integer', 'min:1', 'max:'.self::MAX_SIZE_BYTES],
        ];
    }
}
