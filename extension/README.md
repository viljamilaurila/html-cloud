# html.cloud browser extension

Open a local HTML file, click one floating button, and get a zero-knowledge
encrypted share link. Same crypto as the website and the CLI — encryption happens
in your browser, the server only ever stores ciphertext.

## What it does

- **Floating Share button** on any local `file:///*.html` page. One click reads the
  file, encrypts it in-browser (AES-256-GCM), uploads only ciphertext, and copies
  the `/v/{id}#viewKey` share link.
- **Unified "Your uploads"** — shares created from the extension are merged into
  html.cloud's device-local uploads registry, so they appear alongside files you
  uploaded from the homepage. Keys never leave the device; nothing syncs to a server.
- **First-run setup** that walks you through Chrome's file-access permission.

## Architecture

| File | Context | Role |
|------|---------|------|
| `content-file.js` | file:///*.html | Injects the Shadow-DOM Share button, reads the file bytes |
| `background.js` | service worker | Holds html.cloud host access; runs `shareDocument` (encrypt + upload) |
| `content-merge.js` | html.cloud/* | Merges extension uploads into `localStorage['hc_uploads']` |
| `onboarding.js/html` | options page | Detects file access, deep-links the toggle, re-checks |
| `vendor/*.js` | — | Generated copies of `cli/crypto.js` + `cli/share-core.js` |

The cross-origin upload lives in the background worker on purpose: a content
script on a `file://` page would be blocked by CORS. `host_permissions` lets the
worker POST to html.cloud freely.

## Build & load

```bash
node extension/build.mjs        # vendors crypto.js + share-core.js into vendor/
```

Then in Chrome:

1. `chrome://extensions` → enable **Developer mode**
2. **Load unpacked** → select the `extension/` folder
3. On the extension's details page, turn on **“Allow access to file URLs”**
   (the first-run screen links you straight there)

Re-run `build.mjs` whenever `cli/crypto.js` or `cli/share-core.js` changes.

## Local testing

Point the extension at a dev server by editing `config.js` (`BASE_URL`) and the
`host_permissions` / content-script match in `manifest.json` to the same origin.
