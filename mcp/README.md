# html-cloud-mcp

An [MCP](https://modelcontextprotocol.io) server that lets Claude and other AI
assistants share the HTML they generate as a **private, end-to-end encrypted
link** — no account, no public URL.

The assistant calls one tool, `share_html`. The HTML is encrypted locally with
AES-256-GCM before anything is uploaded; [html.cloud](https://html.cloud) stores
only ciphertext and cannot read it.

> "Make me a one-page summary of this and share it privately."
> → the assistant generates the HTML, calls `share_html`, and replies with a link.

## Install

Add it to your MCP client. For Claude Desktop, in `claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "html-cloud": {
      "command": "npx",
      "args": ["-y", "html-cloud-mcp"]
    }
  }
}
```

For Claude Code:

```sh
claude mcp add html-cloud -- npx -y html-cloud-mcp
```

Requires Node.js 20+.

## The tool

### `share_html`

| Argument | Type | Description |
|---|---|---|
| `html` | string (required) | The full, self-contained HTML document to share. |
| `expires` | `7` \| `30` \| `never` | Days until the link expires. Default `30`. |

Returns a **share link** (give it to anyone you want to read the file) and a
private **edit link** (replace the file, change the expiry, or delete it).

## How the encryption works

- Keys are generated inside this server process and **never sent anywhere**
  except inside the links it returns.
- The HTML is encrypted with **AES-256-GCM** before any network request. Only
  ciphertext is uploaded.
- The decryption key sits after the `#` in the share link — that part of a URL
  is never transmitted to a server.

This is the same zero-knowledge model as the html.cloud website and CLI, using
the **same crypto module** (imported from the [`html-cloud`](https://www.npmjs.com/package/html-cloud)
package — never reimplemented). Full explainer: [html.cloud/security](https://html.cloud/security).

## Configuration

| Env var | Description | Default |
|---|---|---|
| `HTML_CLOUD_URL` | Server base URL (for self-hosted instances) | `https://html.cloud` |

## Honest threat model

Anyone with the share link can read the file — the link is the credential.
The server can expire or delete ciphertext but can never read it. See
[html.cloud/security](https://html.cloud/security) for details and limitations.

Source: [github.com/viljamilaurila/html-cloud](https://github.com/viljamilaurila/html-cloud) · MIT
