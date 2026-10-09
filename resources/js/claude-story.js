/**
 * Plays the homepage Claude storyboard (partials/claude-story.blade.php) as a
 * loop: each step reveals the elements whose data-at it has reached. Runs only
 * while the stage is on screen; with reduced motion the finished conversation
 * simply stays, which is also what renders without JS.
 */
const stage = document.getElementById('claude-story');
const section = stage?.closest('.cs');

// [step, how long to hold it before the next one], in ms.
const TIMELINE = [
  [1, 1100], [2, 2600], [3, 1500], [4, 2900], [5, 2800], [6, 1500], [7, 5200],
];
const PHASE_OF_STEP = { 1: 1, 2: 1, 3: 2, 4: 2, 5: 3, 6: 4, 7: 4 };

if (stage && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
  const parts = [...stage.querySelectorAll('[data-at]')];
  let index = -1;
  let timer = null;
  let visible = false;

  const show = (step) => {
    stage.dataset.step = String(step);
    section.dataset.phase = String(PHASE_OF_STEP[step] ?? 0);
    for (const el of parts) el.classList.toggle('is-on', Number(el.dataset.at) <= step);
  };

  const advance = () => {
    index += 1;
    if (index >= TIMELINE.length) {
      // Fade the finished story out, then start over from an empty chat.
      stage.classList.add('is-resetting');
      timer = setTimeout(() => {
        stage.classList.remove('is-resetting');
        index = -1;
        show(0);
        schedule(500);
      }, 500);
      return;
    }
    show(TIMELINE[index][0]);
    schedule(TIMELINE[index][1]);
  };

  const schedule = (ms) => {
    clearTimeout(timer);
    if (visible) timer = setTimeout(advance, ms);
  };

  stage.classList.remove('is-static');
  show(0);

  new IntersectionObserver(([entry]) => {
    visible = entry.isIntersecting;
    if (visible) schedule(index < 0 ? 400 : 1200);
    else clearTimeout(timer);
  }, { threshold: 0.35 }).observe(stage);
}
