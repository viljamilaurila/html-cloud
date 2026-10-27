# html.cloud

Share the HTML that Claude makes for you (a report, dashboard, presentation,
prototype or invitation) as a private link, and update it later at the same
link. The page is encrypted on your computer before anything is uploaded, so
html.cloud stores only ciphertext it cannot read. No account, no public URL.

## What's inside

- **MCP server** (`html-cloud-mcp`, open source, in [`server/`](server/), copied
  from this repository's [`mcp/`](https://github.com/viljamilaurila/html-cloud/tree/main/mcp)
  folder by `mcp/scripts/sync-plugin.js`).
  It adds two tools:
  - `share_html` encrypts a page and returns a share link and a private edit link.
  - `update_html` replaces a shared page using its edit link. The share link stays the same.
- **Skill** (`html-cloud`). It tells Claude when a private link fits (client,
  financial, legal, medical or internal material, or pages going outside your
  organisation), how to build a self-contained page, and to update a page
  instead of sharing a second copy.

## Use it

Ask Claude to make a page and share it privately, for example: "Turn these
notes into a one-page report and give me a private link." Claude returns the
share link to send and an edit link to keep. Later, ask for a change ("add the
Q3 numbers") and Claude updates the same page.

## Where it works

The MCP server is a local program that runs with Node.js 20 or later,
so the tools work in **Claude Code** and in **Cowork** sessions on your
computer. Chat on claude.ai and in the desktop app loads only the skill. There
you can upload a file yourself at [html.cloud](https://html.cloud).

## What it runs, sends and stores

- **Runs:** `node server/bin/html-cloud-mcp.js` from this plugin. The whole
  server is in [`server/`](server/) as plain source, with no dependencies, so
  nothing is downloaded when it starts.
- **Reads:** only the HTML Claude passes to a tool, inline or as the one
  `.html` file whose path Claude passes. Nothing else from your computer or
  the conversation.
- **Sends:** to one host, `https://html.cloud`, only when you share or update
  a page: the AES-256-GCM ciphertext, the expiry you chose, an encrypted copy
  of the page key, a hash of the edit key, and, if Claude sets one, a short
  readable name for the link. That name is not encrypted. Each request also
  says it comes from the MCP server, so html.cloud can keep daily totals.
- **Stores:** nothing on your computer. The keys exist only in the links
  returned to you, after the `#`, which browsers never send to a server.

## Privacy Policy

Full policy: [html.cloud/mcp-privacy](https://html.cloud/mcp-privacy).

- **Collection:** the HTML passed to the tools, encrypted before it leaves your
  computer. No account, email, name or other personal data.
- **Use and storage:** html.cloud stores the ciphertext so the link works, and
  daily totals (for example, pages shared through the MCP server that day).
  Nothing per page or per person.
- **Third parties:** none. No analytics, no tracking, no crash reporting.
- **Retention:** a page stops working when its link expires (7 or 30 days, or
  never if you chose that), and its ciphertext is deleted by a daily clean-up.
  Deleting it with the edit link removes it at once.
- **Contact:** [viljami@liito.io](mailto:viljami@liito.io) or
  [GitHub issues](https://github.com/viljamilaurila/html-cloud/issues).

Anyone with the share link can read the page, so send it only to the people meant to read it.
Keep the edit link private. More on the design: [html.cloud/security](https://html.cloud/security).

## License

MIT
