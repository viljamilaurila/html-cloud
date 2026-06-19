#!/usr/bin/env node
/**
 * Vendors the shared zero-knowledge modules into the extension.
 *
 * crypto.js and share-core.js are the single source of truth (in cli/). A Chrome
 * extension can only load files inside its own root, so we COPY them into
 * extension/vendor/ — generated artifacts, never hand-edited, never forked. Run
 * this before loading the unpacked extension (and after changing either module).
 */

import { copyFile, mkdir } from 'node:fs/promises';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const cli = join(here, '..', 'cli');
const vendor = join(here, 'vendor');

await mkdir(vendor, { recursive: true });
for (const file of ['crypto.js', 'share-core.js']) {
  await copyFile(join(cli, file), join(vendor, file));
  console.log(`vendored ${file}`);
}
console.log('done — load extension/ as an unpacked extension in Chrome.');
