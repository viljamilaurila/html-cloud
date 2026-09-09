# html-cloud-mcp

An [MCP](https://modelcontextprotocol.io) server that lets Claude and other AI
assistants share the HTML they generate as a **private, end-to-end encrypted
link** — no account, no public URL.

The assistant calls one tool, `share_html`, to share — and `update_html` to
change a page it already shared, at the same link. The HTML is encrypted locally
with AES-256-GCM before anything is uploaded; [html.cloud](https://html.cloud)
stores only ciphertext and cannot read it.

> "Make me a one-page summary of this and share it privately."
> → the assistant generates the HTML, calls `share_html`, and replies with a link.
>
> "Actually, add a section on next steps."
> → the assistant revises the HTML, calls `update_html` with the edit link, and
> the link you already sent shows the new version.

## Install

### Claude Desktop (one-click)

Download the packaged extension (a `.mcpb` file) from
[html.cloud/mcp](https://html.cloud/mcp) and double-click it. If Claude Desktop
doesn't offer to install it, open *Settings → Extensions*, click *Advanced
settings*, and use *Install Extension…* under *Extension Developer* to pick the
file — dragging the file into the window is unreliable and may just attach it
to the chat.

> **About the install warning:** Claude Desktop shows the same red "access to
> everything on your computer" box for every extension installed from a file
> rather than from its built-in directory. It is a statement about how
> extensions run, not about this one. The server is the ~250 lines in `bin/`
> and `lib/` of this directory, it talks to a single host (html.cloud), and it
> uploads only ciphertext. Extensions installed from Claude's directory don't
> show the warning; getting listed there is in progress.

> **Cowork:** the extension also works in Cowork sessions that are linked to
> the computer it is installed on — the desktop app proxies it into the
> session. A Cowork session running purely in the cloud, with no linked
> computer, will not see it.

### Other MCP clients

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

## The tools

### `share_html`

| Argument | Type | Description |
|---|---|---|
| `html` | string (required) | The full, self-contained HTML document to share. |
| `expires` | `7` \| `30` \| `never` | Days until the link expires. Default `30`. |

Returns a **share link** (give it to anyone you want to read the file) and a
private **edit link** (update the file, change the expiry, or delete it).

### `update_html`

| Argument | Type | Description |
|---|---|---|
| `edit_link` | string (required) | The private edit link `share_html` returned. |
| `html` | string (required) | The complete HTML document that replaces the current one. |

Replaces the content behind an existing share. The **share link stays the
same** and the expiry is untouched, so anyone who already has the link sees
the new version. The edit key unwraps the document's existing view key locally,
the new HTML is encrypted under that same key, and only ciphertext is sent —
the server never sees the content, before or after.

## Example prompts

- "Make a one-page summary of this report as HTML and share it privately."
- "Turn these notes into a slide-style page and give me a link that expires in 7 days."
- "Add a next-steps section to the page you shared earlier." (the same link shows the new version)

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
| `HTML_CLOUD_PREFER` | When Claude should pick html.cloud over its built-in artifact publishing. `sensitive`: for confidential content and anything going outside your organisation; built-in artifacts stay fine for casual pages. `always`: for every share. | `sensitive` |
| `HTML_CLOUD_URL` | Server base URL (for self-hosted instances) | `https://html.cloud` |

The Claude Desktop extension asks this as a switch, *Always share through
html.cloud*, when you install it. For `npx` setups, set the env var in the
client config:

```json
{
  "mcpServers": {
    "html-cloud": {
      "command": "npx",
      "args": ["-y", "html-cloud-mcp"],
      "env": { "HTML_CLOUD_PREFER": "always" }
    }
  }
}
```

For Claude Code: `claude mcp add html-cloud -e HTML_CLOUD_PREFER=always -- npx -y html-cloud-mcp`.

## Privacy Policy

Full policy: [html.cloud/mcp-privacy](https://html.cloud/mcp-privacy).

- **Collection.** The server handles only the HTML the assistant passes to
  `share_html` or `update_html`. It reads nothing else from your computer and
  nothing from the conversation beyond the tool arguments.
- **Use and storage.** The HTML is encrypted locally with AES-256-GCM. What is
  sent to html.cloud: the ciphertext, the chosen expiry, a copy of the page key
  encrypted with the edit key, and a hash of the edit key that authorises later
  updates (updates send the page id, the new ciphertext, and that proof). None
  of it lets the server read the page. Keys exist only in the links returned
  to you.
- **Sharing.** No third parties. The server talks to exactly one host,
  html.cloud, and only when you share or update a page. No analytics, no
  telemetry, no account.
- **Retention.** A page is unavailable from the moment its link expires (7 or
  30 days, or never if you chose that) and its ciphertext is permanently
  deleted by a daily clean-up; deleting via the edit link removes it at once.
- **Contact.** Email [viljami@liito.io](mailto:viljami@liito.io) or open an
  issue at [github.com/viljamilaurila/html-cloud](https://github.com/viljamilaurila/html-cloud/issues).

## Honest threat model

Anyone with the share link can read the file — the link is the credential.
The server can expire or delete ciphertext but can never read it. See
[html.cloud/security](https://html.cloud/security) for details and limitations.

Source: [github.com/viljamilaurila/html-cloud](https://github.com/viljamilaurila/html-cloud) · MIT
