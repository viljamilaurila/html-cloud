/**
 * Where the extension talks to. Override for local testing against a dev server
 * (e.g. 'http://localhost:8000') and update host_permissions in manifest.json to
 * match — host_permissions is what lets the background worker POST without CORS.
 */
export const BASE_URL = 'https://html.cloud';
