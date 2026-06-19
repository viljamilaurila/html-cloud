# Chrome Web Store listing

Copy-paste source for the Web Store (and Edge Add-ons) submission. Field names
match the Developer Dashboard. Character limits noted where they bite.

---

## Name  *(max 45 chars)*

```
html.cloud — Share local HTML
```

## Summary  *(max 132 chars)*

```
Open a local HTML file and share it as an end-to-end encrypted link in one click. Encrypted in your browser — server sees only ciphertext.
```

## Category

Productivity

## Language

English

---

## Detailed description

```
Share an HTML file. Keep the key.

html.cloud turns any HTML file on your computer into a private, shareable link —
without uploading anything readable. Open a local .html file, click the floating
Share button, and you get a link you can send to anyone.

The catch that makes it safe: your browser encrypts the file (AES-256-GCM)
*before* anything leaves your machine. The decryption key lives in the link
itself, after the “#” — and browsers never send that part to any server. So
html.cloud stores only ciphertext. We can’t read your file, and we can’t recover
the key. Neither can anyone who only has the server’s data.

HOW IT WORKS
• Open a local HTML file in Chrome
• Click the floating “Share to html.cloud” button
• The file is encrypted in your browser and only the ciphertext is uploaded
• Your share link opens automatically and is copied to your clipboard

WHY YOU MIGHT WANT IT
• Send a one-off report, invoice, or AI-generated page without email attachments
• Share a self-contained HTML file that just works in any browser — no account
• Keep it genuinely private: zero-knowledge, end-to-end encrypted by default

YOUR UPLOADS, IN ONE PLACE
Files you share from the extension also appear under “Your uploads” on
html.cloud, alongside anything you uploaded from the website — all kept locally
on your device, never synced to a server.

PRIVACY BY DESIGN
• The encryption key never reaches any server — it stays in the link fragment
• The server only ever stores encrypted bytes
• The extension contacts exactly one site: html.cloud
• No account, no tracking, no analytics

One-time setup: Chrome asks you to allow the extension to access local files so
the Share button can appear on .html pages. The extension walks you through it.

Open source: https://github.com/viljamilaurila/html-cloud
```

---

## Single purpose  *(dashboard field)*

```
Share a local HTML file as an end-to-end encrypted link via html.cloud. The file
is encrypted in the browser before upload; the decryption key stays in the link
and never reaches the server.
```

## Permission justifications  *(one per permission the dashboard flags)*

**`storage`**
```
Stores the user’s own share records (document id + decryption keys) locally in
the browser, so shared files can be listed under “Your uploads” on html.cloud.
This data stays on the device and is never transmitted.
```

**`clipboardWrite`**
```
Copies the generated share link to the clipboard after a successful share, so
the user can paste it immediately.
```

**Host permission — `https://html.cloud/*`**
```
The extension uploads the encrypted file to html.cloud and opens the resulting
share link. html.cloud is the only site the extension communicates with.
```

**File access — content script on `file:///*.html`**
```
The extension adds a “Share” button to local HTML files the user opens. It reads
the file’s contents only when the user clicks Share, and encrypts them in the
browser before any upload.
```

**Are you using remote code?**
```
No. All JavaScript is bundled in the extension package. Nothing is fetched and
executed at runtime.
```

---

## Data safety / privacy disclosures

- **Does this extension collect or use user data?** Only the file the user
  chooses to share — and it is encrypted in the browser before upload, so the
  server receives ciphertext it cannot read.
- **Personal/identifying data collected:** None.
- **Sold to third parties:** No.
- **Used for purposes unrelated to the core feature:** No.
- **Tracking / analytics:** None.
- **Privacy policy URL:** host `extension/PRIVACY.md` (e.g. https://html.cloud/extension-privacy)

---

## Assets still needed before submitting

- [ ] Icon you’re happy with (current one is a placeholder)
- [ ] At least one 1280×800 or 640×400 screenshot — show the floating Share
      button on a local HTML file, and the “Shared ✓” state
- [ ] A 440×280 small promo tile (optional but recommended)
- [ ] Hosted privacy policy URL (see PRIVACY.md)
