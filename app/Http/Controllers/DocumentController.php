<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    private const MAX_SIZE_BYTES = 10 * 1024 * 1024; // 10 MB

    // GET /
    public function home(): Response
    {
        return response()->view('home');
    }

    // GET /v/{id}
    public function viewer(string $id): Response
    {
        $doc = Document::find($id);
        if (! $doc || $doc->isExpired()) {
            return response()->view('expired', [], 404);
        }
        return response()->view('viewer', ['doc' => $doc]);
    }

    // GET /e/{id}
    public function editor(string $id): Response
    {
        $doc = Document::find($id);
        if (! $doc || $doc->isExpired()) {
            return response()->view('expired', [], 404);
        }
        return response()->view('editor', ['doc' => $doc]);
    }

    // POST /api/documents  — upload encrypted blob
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'ciphertext'         => 'required|string',
            'encrypted_view_key' => 'required|string|max:1000',
            'edit_auth'          => 'required|string|size:64|regex:/^[0-9a-f]+$/',
            'expires_in'         => 'nullable|in:7,30,never',
            'size'               => 'required|integer|min:1|max:' . self::MAX_SIZE_BYTES,
        ]);

        if (strlen($request->ciphertext) > self::MAX_SIZE_BYTES * 2) {
            return response()->json(['error' => 'Payload too large'], 413);
        }

        $id = $this->generateId();
        $expiresAt = match ($request->input('expires_in', '30')) {
            '7'  => now()->addDays(7),
            '30' => now()->addDays(30),
            default => null,
        };

        Document::create([
            'id'                 => $id,
            'ciphertext'         => $request->ciphertext,
            'encrypted_view_key' => $request->encrypted_view_key,
            'edit_auth'          => $request->edit_auth,
            'expires_at'         => $expiresAt,
            'size'               => $request->size,
        ]);

        return response()->json(['id' => $id], 201);
    }

    // GET /api/documents/{id}  — fetch metadata + ciphertext for decryption
    public function show(string $id): JsonResponse
    {
        $doc = Document::find($id);
        if (! $doc || $doc->isExpired()) {
            return response()->json(['error' => 'Not found or expired'], 404);
        }

        if (! $doc->ciphertext) {
            return response()->json(['error' => 'Content missing'], 404);
        }

        return response()->json([
            'id'                 => $doc->id,
            'ciphertext'         => $doc->ciphertext,
            'encrypted_view_key' => $doc->encrypted_view_key,
            'expires_at'         => $doc->expires_at?->toIso8601String(),
            'size'               => $doc->size,
        ]);
    }

    // PUT /api/documents/{id}  — replace content (requires valid editKey)
    public function update(Request $request, string $id): JsonResponse
    {
        $doc = Document::find($id);
        if (! $doc || $doc->isExpired()) {
            return response()->json(['error' => 'Not found or expired'], 404);
        }

        $request->validate([
            'ciphertext'         => 'required|string',
            'encrypted_view_key' => 'required|string|max:1000',
            'edit_key'           => 'required|string',
            'size'               => 'required|integer|min:1|max:' . self::MAX_SIZE_BYTES,
        ]);

        if (! $this->verifyEditKey($request->edit_key, $doc->edit_auth)) {
            return response()->json(['error' => 'Invalid edit key'], 403);
        }

        if (strlen($request->ciphertext) > self::MAX_SIZE_BYTES * 2) {
            return response()->json(['error' => 'Payload too large'], 413);
        }

        $doc->update([
            'ciphertext'         => $request->ciphertext,
            'encrypted_view_key' => $request->encrypted_view_key,
            'size'               => $request->size,
        ]);

        return response()->json(['ok' => true]);
    }

    // PATCH /api/documents/{id}/expiry  — change expiry
    public function updateExpiry(Request $request, string $id): JsonResponse
    {
        $doc = Document::find($id);
        if (! $doc || $doc->isExpired()) {
            return response()->json(['error' => 'Not found or expired'], 404);
        }

        $request->validate([
            'expires_in' => 'required|in:7,30,never',
            'edit_key'   => 'required|string',
        ]);

        if (! $this->verifyEditKey($request->edit_key, $doc->edit_auth)) {
            return response()->json(['error' => 'Invalid edit key'], 403);
        }

        $doc->update([
            'expires_at' => match ($request->expires_in) {
                '7'  => now()->addDays(7),
                '30' => now()->addDays(30),
                default => null,
            },
        ]);

        return response()->json([
            'expires_at' => $doc->fresh()->expires_at?->toIso8601String(),
        ]);
    }

    // DELETE /api/documents/{id}
    public function destroy(Request $request, string $id): JsonResponse
    {
        $doc = Document::find($id);
        if (! $doc) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $request->validate(['edit_key' => 'required|string']);

        if (! $this->verifyEditKey($request->edit_key, $doc->edit_auth)) {
            return response()->json(['error' => 'Invalid edit key'], 403);
        }

        $doc->delete();

        return response()->json(['ok' => true]);
    }

    private function generateId(): string
    {
        do {
            $id = Str::random(8);
        } while (Document::find($id));
        return $id;
    }

    private function verifyEditKey(string $editKey, string $storedHash): bool
    {
        $std     = str_replace(['-', '_'], ['+', '/'], $editKey);
        $decoded = base64_decode($std, true);
        if ($decoded === false) {
            return false;
        }
        return hash_equals($storedHash, hash('sha256', $decoded));
    }
}
