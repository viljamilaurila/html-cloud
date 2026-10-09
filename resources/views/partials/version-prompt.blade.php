{{-- Shown instead of the drop zone when a dropped file looks like a new version of
     something this browser already shared (matched by filename, device-local only).
     Filled in by upload.js; the person always chooses. --}}
<div class="version-prompt hidden" id="version-prompt" role="group" aria-labelledby="version-prompt-title">
  <p class="version-prompt-title" id="version-prompt-title">
    A new version of <strong id="version-prompt-name"></strong>?
  </p>
  <p class="version-prompt-sub">
    You shared it from this browser <span id="version-prompt-when"></span>. Update that link
    and everyone who already has it sees the new version — nothing to re-send.
  </p>
  <div class="version-prompt-actions">
    <button type="button" class="link-btn link-btn-primary" id="version-prompt-update">Update the existing link</button>
    <button type="button" class="link-btn link-btn-ghost" id="version-prompt-new">Share as a new link</button>
  </div>
  <button type="button" class="version-prompt-cancel" id="version-prompt-cancel">Cancel</button>
</div>
