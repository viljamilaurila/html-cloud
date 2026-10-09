import { test } from 'node:test';
import assert from 'node:assert/strict';

import { shareHtml, updateHtml } from '../lib/share.js';

/** Share against a stubbed server; returns the links and every request sent. */
async function shareWith(opts) {
  const sent = [];
  const realFetch = globalThis.fetch;
  globalThis.fetch = async (url, init) => {
    sent.push({ url, body: init.body });
    return new Response(JSON.stringify({ id: 'AbC123xy' }), { status: 201 });
  };
  try {
    return { ...(await shareHtml('<h1>Hi</h1>', { baseUrl: 'https://html.test', ...opts })), sent };
  } finally {
    globalThis.fetch = realFetch;
  }
}

test('a link name becomes a readable slug in the share link', async () => {
  const { shareUrl, editUrl } = await shareWith({ linkName: 'Q3 Sales Report — Düsseldorf' });
  assert.match(shareUrl, /^https:\/\/html\.test\/v\/AbC123xy\/q3-sales-report-dusseldorf#[\w-]+$/);
  assert.match(editUrl, /^https:\/\/html\.test\/e\/AbC123xy#[\w-]+$/, 'the edit link never carries the name');
});

test('without a link name the share link is just the id', async () => {
  const { shareUrl } = await shareWith({});
  assert.match(shareUrl, /^https:\/\/html\.test\/v\/AbC123xy#[\w-]+$/);
});

test('the name is never sent to the server with the upload', async () => {
  const { sent } = await shareWith({ linkName: 'Board memo' });
  assert.equal(sent.length, 1);
  assert.doesNotMatch(sent[0].body, /board/i);
});

test('an update returns the same named link the page was shared with', async () => {
  const stored = {};
  const realFetch = globalThis.fetch;
  globalThis.fetch = async (url, init = {}) => {
    if (init.method === 'POST' || init.method === 'PUT') {
      Object.assign(stored, JSON.parse(init.body));
      return new Response(JSON.stringify({ id: 'AbC123xy' }), { status: 200 });
    }
    return new Response(JSON.stringify({ encrypted_view_key: stored.encrypted_view_key }), { status: 200 });
  };
  try {
    const shared = await shareHtml('<h1>v1</h1>', { baseUrl: 'https://html.test', linkName: 'Team update' });
    const updated = await updateHtml(shared.editUrl, '<h1>v2</h1>', { linkName: 'Team update' });
    assert.equal(updated.shareUrl, shared.shareUrl);
  } finally {
    globalThis.fetch = realFetch;
  }
});
