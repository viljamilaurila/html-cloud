import { test } from 'node:test';
import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';

const SCRIPT = fileURLToPath(new URL('../scripts/sync-plugin.js', import.meta.url));

test('the copy of the server inside the Claude plugin matches mcp/ and cli/', () => {
  const { status, stderr } = spawnSync(process.execPath, [SCRIPT, '--check'], { encoding: 'utf8' });

  assert.equal(status, 0, stderr);
});
