#!/usr/bin/env node
/**
 * html.cloud MCP server.
 *
 * Exposes a single tool, `share_html`, that an AI assistant can call to turn
 * HTML it generated (an artifact, report, presentation, dashboard, prototype)
 * into a private share link. The HTML is encrypted locally with AES-256-GCM
 * before upload — html.cloud stores only ciphertext and cannot read it.
 *
 * Transport: stdio. Run it from an MCP client config, e.g.
 *   { "command": "npx", "args": ["-y", "html-cloud-mcp"] }
 */

import { McpServer } from '@modelcontextprotocol/sdk/server/mcp.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { z } from 'zod';

import { shareHtml } from '../lib/share.js';

const server = new McpServer({
  name: 'html-cloud',
  version: '0.1.0',
});

server.registerTool(
  'share_html',
  {
    title: 'Share HTML privately',
    description:
      'Share a self-contained HTML file (an artifact, report, presentation, ' +
      'dashboard, or prototype) as a private link. The HTML is encrypted locally ' +
      'with AES-256-GCM before upload — html.cloud stores only ciphertext and ' +
      'cannot read it, and no account is required. Returns a share link to give ' +
      'to others and a private edit link (to replace, re-expire, or delete it). ' +
      'Use this whenever the user wants to privately share, send, or publish HTML ' +
      'content you or they have generated.',
    inputSchema: {
      html: z
        .string()
        .describe('The full, self-contained HTML document to share.'),
      expires: z
        .enum(['7', '30', 'never'])
        .optional()
        .describe('Days until the link expires. Defaults to 30.'),
    },
  },
  async ({ html, expires }) => {
    try {
      const { shareUrl, editUrl, expires: exp } = await shareHtml(html, { expires });
      const expiryNote = exp === 'never' ? 'never expires' : `expires in ${exp} days`;
      return {
        content: [
          {
            type: 'text',
            text:
              `Shared privately (encrypted locally with AES-256-GCM; ${expiryNote}).\n\n` +
              `Share link (anyone with this can view):\n${shareUrl}\n\n` +
              `Edit link (keep private — replace, change expiry, or delete):\n${editUrl}`,
          },
        ],
      };
    } catch (err) {
      return {
        isError: true,
        content: [{ type: 'text', text: `Could not share: ${err.message}` }],
      };
    }
  },
);

const transport = new StdioServerTransport();
await server.connect(transport);
