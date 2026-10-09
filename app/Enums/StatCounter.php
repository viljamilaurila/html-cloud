<?php

namespace App\Enums;

/**
 * The daily_stats columns. Each is a plain per-day total.
 */
enum StatCounter: string
{
    case Uploads = 'uploads';
    case TinyUploads = 'tiny_uploads';
    case WebUploads = 'web_uploads';
    case CliUploads = 'cli_uploads';
    case McpUploads = 'mcp_uploads';
    case Updates = 'updates';
    case Opens = 'opens';
    case Deletes = 'deletes';

    /**
     * Uploads smaller than this are counted separately: no real page is this
     * small, but directory scanners testing the MCP server send dozens at once.
     */
    public const TINY_UPLOAD_BYTES = 100;
}
