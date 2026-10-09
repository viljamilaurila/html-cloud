import { test } from 'node:test';
import assert from 'node:assert/strict';
import { documentStem, findPreviousVersion } from '../../resources/js/uploads-store.js';

test('a new version reduces to the same stem as the file it replaces', () => {
  for (const name of ['report.html', 'Report.HTM', 'report (1).html', 'report-v2.html', 'report_v12.html',
    'report version 3.html', 'report final.html', 'report-final (2).html', 'report copy.html', '/tmp/report.html']) {
    assert.equal(documentStem(name), 'report', name);
  }
});

test('names that merely contain a number keep it', () => {
  assert.equal(documentStem('q3-sales.html'), 'q3-sales');
  assert.equal(documentStem('top10.html'), 'top10');
});

test('finds the most recent upload of the same document from this device', () => {
  const uploads = [
    { id: 'new', label: 'deck-v2.html', editKey: 'k2' },
    { id: 'old', label: 'deck.html', editKey: 'k1' },
    { id: 'other', label: 'invoice.html', editKey: 'k3' },
  ];
  assert.equal(findPreviousVersion('deck (1).html', uploads).id, 'new');
  assert.equal(findPreviousVersion('budget.html', uploads), null);
});

test('ignores entries it could not update', () => {
  assert.equal(findPreviousVersion('deck.html', [{ id: 'x', label: 'deck.html' }]), null);
  assert.equal(findPreviousVersion('.html', [{ id: 'y', label: '.html', editKey: 'k' }]), null);
});
