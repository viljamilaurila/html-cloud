<?php

namespace App\Enums;

use Illuminate\Http\Request;

/**
 * Which html.cloud client sent a request, from the X-HTML-Cloud-Client header
 * our own clients set. Only used for daily totals; older clients that don't
 * send it are simply not attributed.
 */
enum ClientKind: string
{
    case Web = 'web';
    case Cli = 'cli';
    case Mcp = 'mcp';
    case Viewer = 'viewer';

    public const HEADER = 'X-HTML-Cloud-Client';

    public static function fromRequest(Request $request): ?self
    {
        return self::tryFrom(strtolower((string) $request->header(self::HEADER)));
    }

    /**
     * The per-client upload counter, for clients that upload.
     */
    public function uploadCounter(): ?StatCounter
    {
        return match ($this) {
            self::Web => StatCounter::WebUploads,
            self::Cli => StatCounter::CliUploads,
            self::Mcp => StatCounter::McpUploads,
            self::Viewer => null,
        };
    }
}
