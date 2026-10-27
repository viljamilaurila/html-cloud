import { test } from 'node:test';
import assert from 'node:assert/strict';
import { spawn } from 'node:child_process';
import { fileURLToPath } from 'node:url';

import { createHandler } from '../lib/stdio-server.js';

const BIN = fileURLToPath(new URL('../bin/html-cloud-mcp.js', import.meta.url));

/** Run the real server, send each message as a line, collect the response lines. */
async function converse(messages) {
  const child = spawn(process.execPath, [BIN], { stdio: ['pipe', 'pipe', 'inherit'] });
  let out = '';
  child.stdout.on('data', (chunk) => { out += chunk; });
  for (const message of messages) {
    child.stdin.write((typeof message === 'string' ? message : JSON.stringify(message)) + '\n');
  }
  const expected = messages.filter((m) => typeof m === 'string' || m.id !== undefined).length;
  while (out.split('\n').filter(Boolean).length < expected) {
    await new Promise((resolve) => child.stdout.once('data', resolve));
  }
  child.stdin.end();
  return out.split('\n').filter(Boolean).map((line) => JSON.parse(line));
}

test('the server completes the MCP handshake and lists both tools over stdio', async () => {
  const [init, list] = await converse([
    { jsonrpc: '2.0', id: 1, method: 'initialize', params: { protocolVersion: '2025-06-18', capabilities: {}, clientInfo: { name: 't', version: '1' } } },
    { jsonrpc: '2.0', method: 'notifications/initialized' },
    { jsonrpc: '2.0', id: 2, method: 'tools/list' },
  ]);

  assert.equal(init.result.protocolVersion, '2025-06-18');
  assert.equal(init.result.serverInfo.name, 'html-cloud');
  assert.match(init.result.instructions, /update_html/);
  assert.deepEqual(list.result.tools.map((t) => t.name), ['share_html', 'update_html']);
  assert.equal(list.result.tools[0].inputSchema.type, 'object');
  assert.equal(list.result.tools[0].handler, undefined);
});

test('a malformed line gets a parse error instead of crashing the server', async () => {
  const [parseError, ping] = await converse(['{not json', { jsonrpc: '2.0', id: 7, method: 'ping' }]);

  assert.equal(parseError.error.code, -32700);
  assert.deepEqual(ping, { jsonrpc: '2.0', id: 7, result: {} });
});

const handle = createHandler({
  name: 'x',
  version: '0',
  instructions: '',
  tools: [{
    name: 'echo',
    title: 'Echo',
    description: '',
    annotations: {},
    inputSchema: {
      type: 'object',
      properties: { text: { type: 'string', maxLength: 5 }, mode: { type: 'string', enum: ['a', 'b'] } },
      required: ['text'],
    },
    handler: async ({ text }) => ({ content: [{ type: 'text', text }] }),
  }],
});

const call = (args) => handle({ jsonrpc: '2.0', id: 1, method: 'tools/call', params: { name: 'echo', arguments: args } });

test('tool arguments are checked against the schema before the handler runs', async () => {
  assert.equal((await call({ text: 'hi' })).result.content[0].text, 'hi');

  for (const [args, problem] of [
    [{}, /text is required/],
    [{ text: 'toolong' }, /at most 5/],
    [{ text: 'hi', mode: 'c' }, /one of a, b/],
    [{ text: 3 }, /must be a string/],
    [{ text: 'hi', other: 'x' }, /unknown argument other/],
  ]) {
    const { result } = await call(args);
    assert.equal(result.isError, true);
    assert.match(result.content[0].text, problem);
  }
});

test('an unsupported protocol version is answered with the newest one', async () => {
  const { result } = await handle({ jsonrpc: '2.0', id: 1, method: 'initialize', params: { protocolVersion: '1999-01-01' } });

  assert.equal(result.protocolVersion, '2025-11-25');
});

test('unknown methods and tools are JSON-RPC errors; notifications get no response', async () => {
  assert.equal((await handle({ jsonrpc: '2.0', id: 1, method: 'resources/list' })).error.code, -32601);
  assert.equal((await handle({ jsonrpc: '2.0', id: 1, method: 'tools/call', params: { name: 'nope' } })).error.code, -32602);
  assert.equal((await handle({ jsonrpc: '2.0', id: 1, method: 'toString' })).error.code, -32601);
  assert.equal(await handle({ jsonrpc: '2.0', method: 'notifications/cancelled' }), null);
});
