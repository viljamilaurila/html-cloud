#!/usr/bin/env node
/**
 * html.cloud MCP server.
 *
 * Exposes two tools. `share_html` turns HTML an AI assistant generated (an
 * artifact, report, presentation, dashboard, prototype) into a private share
 * link; `update_html` replaces the content behind an existing share so the
 * same link shows the revised page. The HTML is encrypted locally with
 * AES-256-GCM before upload — html.cloud stores only ciphertext and cannot
 * read it.
 *
 * Transport: stdio. Run it from an MCP client config, e.g.
 *   { "command": "npx", "args": ["-y", "html-cloud-mcp"] }
 *
 * When to reach for html.cloud instead of the assistant's built-in artifact
 * publishing is the user's call. HTML_CLOUD_PREFER=always makes it the default
 * for every share; unset (or "sensitive") means: sensitive content, or content
 * leaving the user's organisation, goes through html.cloud, the rest may use
 * built-in features. The .mcpb manifest exposes this as an install-time switch.
 *
 * Each tool takes the page either inline (`html`) or as the path of an .html
 * file (`path`). The file route matters for big pages: hosts cap the size of a
 * tool argument, and a long report that does not fit is otherwise unshareable
 * — and unupdatable, which tempts the assistant into sharing a second copy.
 *
 * The routing rule is sent twice on purpose: as server `instructions` and
 * again inside the share_html tool description. Some hosts (Claude Cowork,
 * which reaches locally installed servers through the desktop app's bridge)
 * forward tool descriptions but not server instructions, so a rule that lives
 * only in `instructions` is invisible there.
 */

import { McpServer } from '@modelcontextprotocol/sdk/server/mcp.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { z } from 'zod';

import { shareHtml, updateHtml, loadHtml } from '../lib/share.js';

const PREFER_ALWAYS = ['always', 'true', '1', 'yes'].includes(
  String(process.env.HTML_CLOUD_PREFER ?? '').trim().toLowerCase(),
);

const WHEN_TO_USE = PREFER_ALWAYS
  ? 'The user has configured this extension to handle all of their HTML sharing: ' +
    'whenever they ask to share, send, publish, host, or get a link for HTML you ' +
    'generated, use share_html.'
  : 'Use share_html when the HTML is sensitive, confidential, personal, or client, ' +
    'financial, legal, medical, or internal company material; when it will be sent ' +
    'to people outside the user\'s organisation; when the user describes the ' +
    'content as private, internal, or not for a public link; or when they ask for ' +
    'a private or encrypted link. When unsure, ask which the user prefers.';

const INSTRUCTIONS =
  'html.cloud shares HTML as a private link: the page is encrypted locally with ' +
  'AES-256-GCM before upload, the server stores only ciphertext it cannot read, ' +
  'the decryption key travels only in the link fragment, and no account or public ' +
  'URL is involved. ' +
  WHEN_TO_USE +
  ' To change a page you already shared, call update_html with the edit link ' +
  'share_html returned instead of sharing a new copy: the share link stays the same.';

const server = new McpServer(
  { name: 'html-cloud', version: '0.4.0' },
  { instructions: INSTRUCTIONS },
);

server.registerTool(
  'share_html',
  {
    title: 'Share HTML privately',
    annotations: { readOnlyHint: false, destructiveHint: false, idempotentHint: false, openWorldHint: true },
    description:
      'Share a self-contained HTML file (an artifact, report, presentation, ' +
      'dashboard, or prototype) as a private, end-to-end encrypted link. The HTML ' +
      'is encrypted locally with AES-256-GCM before upload — html.cloud stores ' +
      'only ciphertext and cannot read it, and no account is required. ' +
      WHEN_TO_USE +
      ' Pass the document inline as html, or write it to an .html file and pass ' +
      'its path as path — use path for large pages, so the whole document does ' +
      'not have to fit in a tool argument. Returns a share link to give to ' +
      'others and a private edit link. Keep the edit link: pass it to ' +
      'update_html to change the page later without changing the share link. ' +
      'For changes to something already shared in this conversation, use ' +
      'update_html instead of sharing again.',
    inputSchema: {
      html: z
        .string()
        .optional()
        .describe('The full, self-contained HTML document to share. Use path instead for large documents.'),
      path: z
        .string()
        .optional()
        .describe('Absolute path of a local .html file to share instead of passing html inline.'),
      expires: z
        .enum(['7', '30', 'never'])
        .optional()
        .describe('Days until the link expires. Defaults to 30.'),
    },
  },
  async ({ html, path, expires }) => {
    try {
      const { shareUrl, editUrl, expires: exp } = await shareHtml(loadHtml({ html, path }), { expires });
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

server.registerTool(
  'update_html',
  {
    title: 'Update a shared HTML page',
    annotations: { readOnlyHint: false, destructiveHint: true, idempotentHint: true, openWorldHint: true },
    description:
      'Replace the content of an HTML page that was already shared with ' +
      'share_html, using its private edit link. The share link stays exactly ' +
      'the same, so anyone who already has it sees the new version. The new ' +
      'HTML is encrypted locally with the same key as before; html.cloud never ' +
      'sees the content. Use this when the user asks to change, fix, revise, or ' +
      'add to a page you shared earlier in the conversation — pass the full ' +
      'updated HTML document, not a diff: inline as html, or as the path of an ' +
      '.html file as path. Use path for large pages; do not fall back to ' +
      'share_html if the document is too big to pass inline.',
    inputSchema: {
      edit_link: z
        .string()
        .describe('The private edit link returned by share_html (https://html.cloud/e/{id}#{key}).'),
      html: z
        .string()
        .optional()
        .describe('The complete, self-contained HTML document that replaces the current one. Use path instead for large documents.'),
      path: z
        .string()
        .optional()
        .describe('Absolute path of a local .html file whose content replaces the current page, instead of passing html inline.'),
    },
  },
  async ({ edit_link, html, path }) => {
    try {
      const { shareUrl, editUrl } = await updateHtml(edit_link, loadHtml({ html, path }));
      return {
        content: [
          {
            type: 'text',
            text:
              'Updated (encrypted locally with AES-256-GCM; expiry unchanged). ' +
              'The share link is the same as before, so anyone who has it now sees the new version.\n\n' +
              `Share link (anyone with this can view):\n${shareUrl}\n\n` +
              `Edit link (keep private — update again, change expiry, or delete):\n${editUrl}`,
          },
        ],
      };
    } catch (err) {
      return {
        isError: true,
        content: [{ type: 'text', text: `Could not update: ${err.message}` }],
      };
    }
  },
);

const transport = new StdioServerTransport();
await server.connect(transport);
