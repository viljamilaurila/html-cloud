---
name: html-cloud
description: Share HTML you generated (a report, dashboard, presentation, prototype, or artifact) as a private, end-to-end encrypted link through html.cloud, and update it later at the same link. Use when the user asks to share, send, or get a link for an HTML page that is confidential, internal, personal, or client, financial, legal, or medical material, or that is going to people outside their organisation; when they ask for a private or encrypted link; or when they want to change a page already shared this way.
license: MIT
---

# html.cloud — share HTML privately

html.cloud turns an HTML page into a private link. The page is encrypted locally
with AES-256-GCM before upload, the server stores only ciphertext it cannot read,
and the decryption key travels only in the part of the link after `#`, which
browsers never send to a server. No account, no public URL.

## When to use it

Use html.cloud when the page:

- is sensitive: client, financial, legal, medical, HR, or internal company material;
- will be sent to people outside the user's organisation;
- is described by the user as private, internal, or "not for a public link";
- or the user asks for a private, encrypted, or expiring link.

For a casual page with nothing sensitive in it, the host's own publishing
feature is fine. When unsure, ask the user which they prefer.

## How to share

Prefer the MCP tools when they are available; otherwise use the CLI. If you
have neither the tools nor a shell (for example, in claude.ai chat), give the
user the finished page as a file and tell them they can upload it at
https://html.cloud to get the same kind of private link.

### With the MCP server (`share_html`, `update_html`)

Not installed? For Claude Code: `claude mcp add html-cloud -- npx -y html-cloud-mcp`.
Other clients: add `{"command": "npx", "args": ["-y", "html-cloud-mcp"]}` to the
MCP config. Requires Node.js 20+.

1. Build a **self-contained** HTML document: inline CSS and scripts, images as
   `data:` URIs or absolute `https://` URLs. Relative paths will not resolve.
2. Call `share_html` with either `html` (inline) or `path` (an absolute path to
   an `.html` file). Use `path` for large pages: hosts limit the size of a
   tool argument.
3. Optional: `expires` (`"7"`, `"30"`, or `"never"`; default 30 days) and
   `link_name` (a short readable name added to the link, such as
   `"Q3 sales report"`).
4. Give the user the **share link**. Tell them the **edit link** is private: it
   can replace the page, change the expiry, or delete it.

### With the CLI

```sh
npx html-cloud ./report.html               # prints share link + edit link
npx html-cloud ./report.html --expires 7   # 7, 30 (default), or never
cat page.html | npx html-cloud -           # from stdin
```

## Changing a page that is already shared

Do **not** share a second copy. Update the existing one so the link the user
already sent keeps working:

- MCP: call `update_html` with the `edit_link` from earlier and the **full**
  revised HTML (not a diff), as `html` or `path`. If the page was shared with
  a `link_name`, pass the same one.
- CLI: `npx html-cloud ./report.html --update "<edit link>"`

The share link and the expiry stay the same.

## Rules

- **`link_name` is not encrypted.** It is visible in the link and to the server.
  Keep it generic, and leave it out when the title itself is confidential
  (names of people, clients, deals, medical or legal matters). The CLI uses
  the file name by default; pass `--no-name` in that case.
- **Anyone with the share link can read the page.** The link is the credential,
  so say so when the content is sensitive.
- **Keep the edit link out of anything shared.** Don't put it in the page, a
  commit, or a message to the recipients.
- Limits: one HTML file, at most 10 MB.

More detail: https://html.cloud/security · Source: https://github.com/viljamilaurila/html-cloud
