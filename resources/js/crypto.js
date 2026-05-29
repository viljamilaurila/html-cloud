/**
 * Zero-knowledge crypto helpers using Web Crypto API.
 *
 * Key model:
 *  viewKey  — AES-256-GCM CryptoKey used to encrypt/decrypt the HTML content.
 *  editKey  — 32 random bytes. Encrypts the viewKey (stored server-side).
 *             SHA-256(editKey) is stored as editAuth for authorization.
 *
 * URL model:
 *  /v/{id}#{base64url(viewKey raw bytes)}
 *  /e/{id}#{base64url(editKey)}
 *
 * The server sees only:
 *  - Encrypted content blob
 *  - viewKey encrypted with editKey (so edit page can re-derive viewKey)
 *  - SHA-256(editKey) for authorization
 */

const ENC = 'AES-GCM';
const KEY_LEN = 256;
const IV_LEN = 12; // bytes for AES-GCM nonce

export function b64url(bytes) {
  // Chunked to avoid "maximum call stack" when spreading large arrays into String.fromCharCode
  let str = '';
  const CHUNK = 8192;
  for (let i = 0; i < bytes.length; i += CHUNK) {
    str += String.fromCharCode(...bytes.subarray(i, i + CHUNK));
  }
  return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
}

export function b64urlDecode(str) {
  str = str.replace(/-/g, '+').replace(/_/g, '/');
  const pad = (4 - str.length % 4) % 4;
  return Uint8Array.from(atob(str + '='.repeat(pad)), c => c.charCodeAt(0));
}

export async function generateViewKey() {
  return crypto.subtle.generateKey({ name: ENC, length: KEY_LEN }, true, ['encrypt', 'decrypt']);
}

export async function generateEditKey() {
  return crypto.getRandomValues(new Uint8Array(32));
}

export async function exportViewKey(key) {
  const raw = await crypto.subtle.exportKey('raw', key);
  return new Uint8Array(raw);
}

export async function importViewKey(rawBytes) {
  return crypto.subtle.importKey('raw', rawBytes, { name: ENC }, false, ['encrypt', 'decrypt']);
}

/** Encrypt plaintext bytes → {iv, ciphertext} both as Uint8Array */
export async function encryptBytes(viewKey, plaintext) {
  const iv = crypto.getRandomValues(new Uint8Array(IV_LEN));
  const buf = await crypto.subtle.encrypt({ name: ENC, iv }, viewKey, plaintext);
  return { iv, ciphertext: new Uint8Array(buf) };
}

/** Decrypt → Uint8Array */
export async function decryptBytes(viewKey, iv, ciphertext) {
  const buf = await crypto.subtle.decrypt({ name: ENC, iv }, viewKey, ciphertext);
  return new Uint8Array(buf);
}

/**
 * Encrypt the viewKey raw bytes using the editKey (raw bytes) as a password-derived AES key.
 * Returns base64url string suitable for storing on the server.
 */
export async function encryptViewKeyWithEditKey(viewKeyRaw, editKeyRaw) {
  const wrapKey = await crypto.subtle.importKey(
    'raw', editKeyRaw, { name: ENC }, false, ['encrypt']
  );
  const iv = crypto.getRandomValues(new Uint8Array(IV_LEN));
  const enc = await crypto.subtle.encrypt({ name: ENC, iv }, wrapKey, viewKeyRaw);
  // Store as iv:ciphertext base64url
  const combined = new Uint8Array(IV_LEN + enc.byteLength);
  combined.set(iv);
  combined.set(new Uint8Array(enc), IV_LEN);
  return b64url(combined);
}

/**
 * Decrypt the viewKey using the editKey.
 * Returns raw viewKey bytes.
 */
export async function decryptViewKeyWithEditKey(encryptedViewKeyB64, editKeyRaw) {
  const combined = b64urlDecode(encryptedViewKeyB64);
  const iv = combined.slice(0, IV_LEN);
  const data = combined.slice(IV_LEN);
  const wrapKey = await crypto.subtle.importKey(
    'raw', editKeyRaw, { name: ENC }, false, ['decrypt']
  );
  const raw = await crypto.subtle.decrypt({ name: ENC, iv }, wrapKey, data);
  return new Uint8Array(raw);
}

/** SHA-256(editKey) → hex string for server-side authorization */
export async function computeEditAuth(editKeyRaw) {
  const hash = await crypto.subtle.digest('SHA-256', editKeyRaw);
  return Array.from(new Uint8Array(hash)).map(b => b.toString(16).padStart(2, '0')).join('');
}

/**
 * Pack iv+ciphertext into a single base64url blob for wire transfer.
 * Format: [12 bytes IV][N bytes ciphertext]
 */
export function packCiphertext(iv, ciphertext) {
  const out = new Uint8Array(IV_LEN + ciphertext.length);
  out.set(iv);
  out.set(ciphertext, IV_LEN);
  return b64url(out);
}

export function unpackCiphertext(packed) {
  const bytes = b64urlDecode(packed);
  return { iv: bytes.slice(0, IV_LEN), ciphertext: bytes.slice(IV_LEN) };
}
