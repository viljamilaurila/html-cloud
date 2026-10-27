/**
 * A minimal MCP server over stdio, with no dependencies.
 *
 * MCP's stdio transport is newline-delimited JSON-RPC 2.0, and a server that
 * only offers tools needs five methods: initialize, ping, tools/list,
 * tools/call, and ignoring notifications. Implementing them here instead of
 * pulling in the MCP SDK (and its ~3,400 files of dependencies) keeps the
 * whole server small enough to read in one sitting, and lets the Claude
 * plugin ship it as plain source instead of fetching a package with npx.
 */

import { createInterface } from 'node:readline';

/** Newest first; a client asking for anything else gets the newest. */
export const SUPPORTED_PROTOCOL_VERSIONS = ['2025-11-25', '2025-06-18', '2025-03-26', '2024-11-05'];

const PARSE_ERROR = -32700;
const INVALID_REQUEST = -32600;
const METHOD_NOT_FOUND = -32601;
const INVALID_PARAMS = -32602;

/**
 * Check tool arguments against the small subset of JSON Schema the tools use:
 * an object of string properties with optional `enum`, `maxLength` and
 * `required`. Returns an error message, or null when the arguments are valid.
 *
 * @param {{properties: Record<string, {type: string, enum?: string[], maxLength?: number}>, required?: string[]}} schema
 * @param {unknown} args
 * @returns {string|null}
 */
export function validateArguments(schema, args) {
  if (args === null || typeof args !== 'object' || Array.isArray(args)) {
    return 'arguments must be an object';
  }
  for (const key of schema.required ?? []) {
    if (args[key] === undefined) return `${key} is required`;
  }
  for (const [key, value] of Object.entries(args)) {
    const property = schema.properties[key];
    if (property === undefined) return `unknown argument ${key}`;
    if (value === undefined || value === null) continue;
    if (typeof value !== 'string') return `${key} must be a string`;
    if (property.enum && !property.enum.includes(value)) {
      return `${key} must be one of ${property.enum.join(', ')}`;
    }
    if (property.maxLength !== undefined && value.length > property.maxLength) {
      return `${key} must be at most ${property.maxLength} characters`;
    }
  }
  return null;
}

/**
 * @typedef {object} Tool
 * @property {string} name
 * @property {string} title
 * @property {string} description
 * @property {object} annotations
 * @property {object} inputSchema  JSON Schema for the arguments.
 * @property {(args: object) => Promise<{content: object[], isError?: boolean}>} handler
 */

/**
 * Build the request handler: takes one parsed JSON-RPC message and resolves to
 * the response to send, or null for a notification (which gets no response).
 *
 * @param {{name: string, version: string, instructions: string, tools: Tool[]}} server
 * @returns {(message: unknown) => Promise<object|null>}
 */
export function createHandler({ name, version, instructions, tools }) {
  const toolsByName = new Map(tools.map((tool) => [tool.name, tool]));
  const listing = tools.map(({ handler, ...tool }) => tool);

  const methods = {
    initialize: async (params) => ({
      protocolVersion: SUPPORTED_PROTOCOL_VERSIONS.includes(params?.protocolVersion)
        ? params.protocolVersion
        : SUPPORTED_PROTOCOL_VERSIONS[0],
      capabilities: { tools: {} },
      serverInfo: { name, version },
      instructions,
    }),
    ping: async () => ({}),
    'tools/list': async () => ({ tools: listing }),
    'tools/call': async (params) => {
      const tool = toolsByName.get(params?.name);
      if (!tool) throw rpcError(INVALID_PARAMS, `Unknown tool: ${params?.name}`);
      const args = params.arguments ?? {};
      const problem = validateArguments(tool.inputSchema, args);
      if (problem) {
        return { isError: true, content: [{ type: 'text', text: `Invalid arguments for ${tool.name}: ${problem}` }] };
      }
      try {
        return await tool.handler(args);
      } catch (err) {
        return { isError: true, content: [{ type: 'text', text: `${tool.name} failed: ${err.message}` }] };
      }
    },
  };

  return async (message) => {
    if (message === null || typeof message !== 'object' || Array.isArray(message) || message.jsonrpc !== '2.0') {
      return errorResponse(null, INVALID_REQUEST, 'Invalid Request');
    }
    const isNotification = message.id === undefined;
    if (isNotification) return null;

    const method = Object.hasOwn(methods, message.method) ? methods[message.method] : null;
    if (!method) return errorResponse(message.id, METHOD_NOT_FOUND, `Method not found: ${message.method}`);
    try {
      return { jsonrpc: '2.0', id: message.id, result: await method(message.params) };
    } catch (err) {
      return errorResponse(message.id, err.code ?? INVALID_PARAMS, err.message);
    }
  };
}

/**
 * Serve over stdin/stdout until stdin closes. stdout carries only protocol
 * messages, one JSON object per line.
 *
 * @param {Parameters<typeof createHandler>[0]} server
 */
export function serveStdio(server, input = process.stdin, output = process.stdout) {
  const handle = createHandler(server);
  const lines = createInterface({ input, crlfDelay: Infinity });

  lines.on('line', async (line) => {
    if (line.trim() === '') return;
    let message;
    try {
      message = JSON.parse(line);
    } catch {
      output.write(JSON.stringify(errorResponse(null, PARSE_ERROR, 'Parse error')) + '\n');
      return;
    }
    const response = await handle(message);
    if (response) output.write(JSON.stringify(response) + '\n');
  });
}

function rpcError(code, message) {
  return Object.assign(new Error(message), { code });
}

function errorResponse(id, code, message) {
  return { jsonrpc: '2.0', id, error: { code, message } };
}
