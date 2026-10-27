#!/usr/bin/env node
/**
 * Copy the MCP server's source into the Claude plugin (plugin/server/).
 *
 * The plugin runs the server with `node ${CLAUDE_PLUGIN_ROOT}/server/bin/...`
 * instead of `npx html-cloud-mcp`, so the directory can read every line it
 * will run and nothing is downloaded at install time. The copies are
 * generated: edit mcp/ (and cli/ for the crypto), then run this script.
 *
 *   node scripts/sync-plugin.js          write plugin/server/
 *   node scripts/sync-plugin.js --check  exit 1 if plugin/server/ is stale
 */

import { readFileSync, writeFileSync, mkdirSync, existsSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const REPO = join(dirname(fileURLToPath(import.meta.url)), '..', '..');
const SERVER = join(REPO, 'plugin', 'server');

const header = (source) => `// Generated from ${source} by mcp/scripts/sync-plugin.js. Edit the original.\n`;

/**
 * Each entry: where it lands in plugin/server/, its source in the repo, and
 * how the import paths change. The html-cloud package's crypto sits beside
 * share.js instead of in node_modules, so its bare import becomes relative.
 */
const FILES = [
  { to: 'bin/html-cloud-mcp.js', from: 'mcp/bin/html-cloud-mcp.js' },
  { to: 'lib/stdio-server.js', from: 'mcp/lib/stdio-server.js' },
  {
    to: 'lib/share.js',
    from: 'mcp/lib/share.js',
    rewrite: (source) => source.replace("from 'html-cloud/share-core.js'", "from './html-cloud/share-core.js'"),
  },
  { to: 'lib/html-cloud/share-core.js', from: 'cli/share-core.js' },
  { to: 'lib/html-cloud/crypto.js', from: 'cli/crypto.js' },
];

/** Keep a shebang on the first line, where node expects it. */
function withHeader(source, from) {
  if (source.startsWith('#!')) {
    const firstLineEnd = source.indexOf('\n') + 1;
    return source.slice(0, firstLineEnd) + header(from) + source.slice(firstLineEnd);
  }
  return header(from) + source;
}

const check = process.argv.includes('--check');
const stale = [];

for (const { to, from, rewrite = (source) => source } of FILES) {
  const expected = withHeader(rewrite(readFileSync(join(REPO, from), 'utf8')), from);
  const target = join(SERVER, to);
  const current = existsSync(target) ? readFileSync(target, 'utf8') : null;
  if (current === expected) {
    continue;
  }
  if (check) {
    stale.push(`plugin/server/${to}`);
    continue;
  }
  mkdirSync(dirname(target), { recursive: true });
  writeFileSync(target, expected);
  console.log(`wrote plugin/server/${to}`);
}

if (stale.length > 0) {
  console.error(`Out of date (run: node mcp/scripts/sync-plugin.js):\n  ${stale.join('\n  ')}`);
  process.exit(1);
}
