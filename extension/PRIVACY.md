# Privacy Policy — html.cloud browser extension

_Last updated: 2026-06-18_

The html.cloud extension is built so that we **cannot** see the files you share.
This policy explains exactly what happens to your data.

## What the extension does with your files

When you click **Share to html.cloud** on a local HTML file:

1. The file is read in your browser.
2. It is encrypted in your browser with AES-256-GCM **before anything is sent**.
3. Only the encrypted bytes (ciphertext) are uploaded to html.cloud.
4. The decryption key is placed in the share link, after the `#`. Browsers never
   transmit the part of a URL after `#`, so **the key never reaches our server**.

Because of this design, html.cloud stores only ciphertext. We cannot read your
file, and we cannot recover the key. Anyone you give the full link to can decrypt
the file in their own browser; anyone with only our stored data cannot.

## What is stored on your device

The extension keeps a local record of files you’ve shared — the document id and
its keys — in your browser’s storage. This lets those files appear under
**Your uploads** on html.cloud. This record:

- stays on your device,
- is never transmitted to any server,
- can be cleared at any time by removing the extension or clearing site data.

## What we collect

- **Personal information:** none.
- **Analytics or tracking:** none.
- **Third-party services:** none. The extension communicates only with
  html.cloud, and only to upload the ciphertext you chose to share.

We do not sell or share data with third parties, and we do not use your data for
any purpose unrelated to sharing the file you asked us to share.

## Permissions

- **File access** — so the Share button can appear on local `.html` files. The
  file is read only when you click Share.
- **html.cloud host access** — to upload the encrypted file and open your link.
- **Storage** — to remember your own shares locally.
- **Clipboard** — to copy the share link after a successful share.

## Contact

Questions: open an issue at https://github.com/viljamilaurila/html-cloud or email
the address listed on https://html.cloud.
