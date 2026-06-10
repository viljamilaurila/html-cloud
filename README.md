# html.cloud

Share an HTML file. Keep the key.

Zero-knowledge, end-to-end encrypted HTML file sharing. Your browser encrypts the file with AES-256-GCM before anything is uploaded. The decryption key lives after the `#` in the share URL — browsers never send that part to the server. We store only ciphertext.

## How it works

1. Drop an HTML file on the homepage
2. Your browser generates two random keys: a **view key** (AES-256-GCM) and an **edit key**
3. The file is encrypted in-browser and uploaded as ciphertext
4. You get two links:
   - **Share link** `/v/{id}#viewKey` — give this to anyone you want to read the file
   - **Edit link** `/e/{id}#editKey` — keep this private; it lets you replace the file later without changing the share link
5. Files self-destruct after the chosen expiry (7 days, 30 days, or never)

The server stores:
- The encrypted blob
- The view key encrypted with the edit key (so the edit page can re-encrypt without exposing the view key)
- `SHA-256(editKey)` for authorization — never the plaintext keys

## Security properties

- **Zero-knowledge uploads** — server cannot read any uploaded content
- **Key-in-fragment** — browsers strip the URL fragment before sending HTTP requests; the key never reaches the server
- **Edit authorization** — `SHA-256(editKey)` stored; raw editKey verified only on update requests
- **Expiry enforcement** — daily cron deletes blobs and DB rows past their expiry date
- **10 MB upload limit** enforced server-side
- The API is stateless: every state-mutating request is authorized by the `edit_auth`
  secret, and uploads are rate-limited

## CLI

Share a file from the terminal — encrypted locally, same crypto module as the browser:

```bash
npx html-cloud ./file.html
```

The package lives in [`cli/`](cli/) and on [npm](https://www.npmjs.com/package/html-cloud).
See [html.cloud/cli](https://html.cloud/cli) for usage and options.

## Stack

- **Laravel 12** (PHP 8.5+)
- **SQLite** (swap to MySQL/Postgres via `.env` for production)
- **Vite** (CSS build pipeline)
- **Web Crypto API** (all crypto is browser-native; no external crypto libraries)

## Setup

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve
```

For production, add a cron entry to run the scheduler:

```
* * * * * cd /path/to/html.cloud && php artisan schedule:run >> /dev/null 2>&1
```

## Self-hosting

Clone, configure `.env` (`APP_URL`, `DB_*` if using MySQL/Postgres), run behind nginx or Caddy. The encrypted blobs are stored in `storage/app/documents/` — back that directory up alongside the database.

## License

MIT
