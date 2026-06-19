/**
 * Re-export of the canonical encrypt-then-upload module.
 *
 * The real implementation lives in cli/share-core.js so the npm package and the
 * Chrome extension can ship it. Browser (Vite), CLI and extension must always
 * share this one implementation — never fork it, or the zero-knowledge wire
 * contract can silently diverge. Same pattern as crypto.js.
 */
export * from '../../cli/share-core.js';
