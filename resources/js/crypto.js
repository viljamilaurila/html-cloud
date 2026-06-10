/**
 * Re-export of the canonical zero-knowledge crypto module.
 *
 * The real implementation lives in cli/crypto.js so the npm package
 * (`npx html-cloud`) can ship it; npm cannot include files outside the
 * package root. Browser (Vite) and CLI must always share this one
 * implementation — never fork it, or the zero-knowledge model can
 * silently diverge.
 */
export * from '../../cli/crypto.js';
