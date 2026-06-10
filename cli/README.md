# html-cloud

Share an HTML file privately from the command line — encrypted in your own
process before anything is uploaded. No account, no project setup, no public URL.

```sh
npx html-cloud ./report.html
```

```
Share link (anyone with this can view):
  https://html.cloud/v/kT4eN7xQ#b3FvXyJq…

Edit link (keep private — replace, change expiry, delete):
  https://html.cloud/e/kT4eN7xQ#9dKw2mPv…
```

Pipe straight from a generator:

```sh
my-report-tool | npx html-cloud -
```

## Why this exists

AI tools (Claude, ChatGPT, Gemini) produce self-contained HTML — presentations,
reports, dashboards, prototypes. Sending one to a client or colleague usually
means a public deploy or a clunky attachment. `html-cloud` gives you a private
link in one command.

## How the encryption works

- The file is encrypted with **AES-256-GCM** locally, in this process.
- The decryption key is placed after the `#` in the share link. URL fragments
  are never sent to servers — by the browser or by this tool.
- The server stores **only ciphertext**. It cannot read your file; nobody can
  without your link. This is the same zero-knowledge model as the
  [html.cloud](https://html.cloud) website, using the same
  [open-source crypto module](https://github.com/viljamilaurila/html-cloud).

Read the full explainer: [html.cloud/security](https://html.cloud/security)

## Options

| Option | Description | Default |
|---|---|---|
| `--expires <7\|30\|never>` | Days until the link expires | `30` |
| `--url <base>` | Server base URL (or `$HTML_CLOUD_URL`) | `https://html.cloud` |

Limits: one `.html`/`.htm` file (or stdin), max 10 MB. Expiry can be changed
later from the edit link.

## Honest threat model

Anyone who has the share link can read the file — link handling is on you.
The server can delete or expire ciphertext but can never read it. For details
and limitations, see [html.cloud/security](https://html.cloud/security).

Requires Node 20+. MIT licensed.
