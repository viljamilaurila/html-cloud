#!/usr/bin/env node
/**
 * Generate the extension's PNG icons (16/48/128) from icon.svg.
 *
 * icon.svg is the single source of truth — the hand-drawn cloud + upload
 * arrow in white on the brand accent blue, same art as public/favicon.svg.
 *
 * Run:  npm i --no-save sharp && node make-icons.mjs
 */
import sharp from 'sharp';

for (const s of [16, 48, 128]) {
  await sharp('icon.svg', { density: Math.ceil(72 * s / 128) })
    .resize(s, s)
    .png()
    .toFile(`icons/icon${s}.png`);
  console.log(`wrote icons/icon${s}.png`);
}
