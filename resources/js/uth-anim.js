/**
 * Homepage "Under the hood" animation (partials/under-the-hood.blade.php): a
 * page is shredded into ciphertext in your browser, only the scrambled bytes go
 * to the server, the key travels separately in the link, and the recipient's
 * browser reassembles the page. Loops while on screen; with reduced motion (or
 * without JS) the finished state is shown instead.
 */
const stage = document.getElementById('uth-anim');
const steps = document.getElementById('uth-steps');

const PAGE = [
  '<h1>Q3 report</h1>',
  '<p>Revenue +18%</p>',
  '<p>Next: hire 2</p>',
];
const HEX = '0123456789abcdef';

/** Same shape as the page, but bytes: what the server actually holds. */
const shredded = (line) => [...line].map((c) => (c === ' ' ? ' ' : HEX[Math.floor(Math.random() * 16)])).join('');
const CIPHER = PAGE.map(shredded);

const docs = stage && {
  you: stage.querySelector('[data-doc="you"]'),
  server: stage.querySelector('[data-doc="server"]'),
  them: stage.querySelector('[data-doc="them"]'),
};

const show = (el, lines) => { el.textContent = lines.join('\n'); };

if (stage) {
  // End state, for no-JS and reduced motion alike.
  show(docs.you, PAGE);
  show(docs.server, CIPHER);
  show(docs.them, PAGE);
}

if (stage && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
  const packet = stage.querySelector('.uth-packet');
  const chip = stage.querySelector('.uth-keychip');
  const track = stage.querySelector('.uth-track');
  let timers = [];
  let visible = false;
  // Bumped on every stop, so a loop that was paused mid-step bails out
  // instead of carrying on alongside the next one.
  let generation = 0;
  const STOPPED = Symbol('stopped');

  const at = (ms, fn) => timers.push(setTimeout(fn, ms));
  const wait = (ms) => new Promise((resolve) => at(ms, resolve));
  const setStep = (n) => {
    for (const li of steps.children) li.classList.toggle('is-on', Number(li.dataset.step) === n);
  };

  /** Morph `el` from one set of lines to another, character by character, top to bottom. */
  const morph = (el, from, to, ms) => new Promise((resolve) => {
    const total = to.join('').length;
    const start = performance.now();
    const tick = (now) => {
      const done = Math.min(1, (now - start) / ms);
      let budget = Math.floor(done * total);
      el.textContent = to.map((line, i) => [...line].map((c, j) => {
        if (budget-- > 0) return c;
        const src = from[i]?.[j] ?? ' ';
        return src === ' ' ? ' ' : HEX[Math.floor(Math.random() * 16)];
      }).join('')).join('\n');
      if (done < 1) requestAnimationFrame(tick);
      else resolve();
    };
    requestAnimationFrame(tick);
  });

  /** Fly `el` from the box of `fromEl` to the box of `toEl`, relative to the stage. */
  const fly = (el, fromEl, toEl, ms) => {
    const base = stage.getBoundingClientRect();
    const a = fromEl.getBoundingClientRect();
    const b = toEl.getBoundingClientRect();
    el.style.transition = 'none';
    el.style.width = `${a.width}px`;
    el.style.transform = `translate(${a.left - base.left}px, ${a.top - base.top}px)`;
    el.classList.add('is-on');
    void el.offsetWidth;
    el.style.transition = `transform ${ms}ms cubic-bezier(.6,0,.3,1)`;
    el.style.transform = `translate(${b.left - base.left}px, ${b.top - base.top}px)`;
    return wait(ms);
  };

  /** The key chip runs along the link track, from its start to its end. */
  const sendKey = (ms) => {
    const base = stage.getBoundingClientRect();
    const t = track.getBoundingClientRect();
    const vertical = t.height > t.width;
    const from = vertical ? [t.left - base.left - 8, t.top - base.top] : [t.left - base.left, t.top - base.top - 12];
    const to = vertical ? [t.left - base.left - 8, t.bottom - base.top - 24] : [t.right - base.left - chip.offsetWidth, t.top - base.top - 12];
    chip.style.transition = 'none';
    chip.style.transform = `translate(${from[0]}px, ${from[1]}px)`;
    chip.classList.add('is-on');
    void chip.offsetWidth;
    chip.style.transition = `transform ${ms}ms cubic-bezier(.6,0,.3,1)`;
    chip.style.transform = `translate(${to[0]}px, ${to[1]}px)`;
    return wait(ms);
  };

  const reset = () => {
    show(docs.you, PAGE);
    docs.server.textContent = '';
    docs.them.textContent = '';
    stage.classList.remove('is-server-full', 'is-them-open', 'is-you-shred');
    packet.classList.remove('is-on');
    chip.classList.remove('is-on');
    setStep(0);
  };

  const play = async () => {
    const mine = ++generation;
    const go = async (promise) => {
      await promise;
      if (mine !== generation) throw STOPPED;
    };

    try {
      for (;;) {
        reset();
        await go(wait(900));

        setStep(1);
        stage.classList.add('is-you-shred');
        await go(morph(docs.you, PAGE, CIPHER, 1100));
        await go(wait(500));

        setStep(2);
        show(packet, CIPHER);
        const flight = fly(packet, docs.you, docs.server, 1000);
        // You keep your own copy: the readable page comes back once the bytes leave.
        at(250, () => { stage.classList.remove('is-you-shred'); show(docs.you, PAGE); });
        await go(flight);
        packet.classList.remove('is-on');
        show(docs.server, CIPHER);
        stage.classList.add('is-server-full');
        await go(wait(900));

        setStep(3);
        await go(sendKey(1500));
        await go(wait(500));

        setStep(4);
        await go(fly(packet, docs.server, docs.them, 1000));
        packet.classList.remove('is-on');
        show(docs.them, CIPHER);
        await go(wait(250));
        await go(morph(docs.them, CIPHER, PAGE, 1200));
        stage.classList.add('is-them-open');
        await go(wait(3600));
      }
    } catch (err) {
      if (err !== STOPPED) throw err;
    }
  };

  const stop = () => {
    generation += 1;
    timers.forEach(clearTimeout);
    timers = [];
    reset();
  };

  stage.classList.remove('is-static');
  steps.classList.add('is-playing');
  reset();

  new IntersectionObserver(([entry]) => {
    if (entry.isIntersecting && !visible) {
      visible = true;
      play();
    } else if (!entry.isIntersecting && visible) {
      visible = false;
      stop();
    }
  }, { threshold: 0.4 }).observe(stage);
}
