/**
 * Homepage "Real situations" switcher (partials/who-uses.blade.php). Turns the
 * stacked situations into tabs, one open at a time, with arrow-key support.
 */
const section = document.getElementById('who-uses');

if (section) {
  const tabs = [...section.querySelectorAll('[role="tab"]')];
  const panels = tabs.map((tab) => document.getElementById(tab.getAttribute('aria-controls')));
  let current = 0;

  const select = (index, { focus = false } = {}) => {
    current = (index + tabs.length) % tabs.length;
    tabs.forEach((tab, i) => {
      const on = i === current;
      tab.setAttribute('aria-selected', on ? 'true' : 'false');
      tab.tabIndex = on ? 0 : -1;
      panels[i].classList.toggle('is-active', on);
    });
    if (focus) tabs[current].focus();
  };

  section.classList.add('is-enhanced');
  select(0);

  tabs.forEach((tab, i) => {
    tab.addEventListener('click', () => select(i));
    tab.addEventListener('keydown', (e) => {
      const step = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 }[e.key];
      if (step === undefined && e.key !== 'Home' && e.key !== 'End') return;
      e.preventDefault();
      select(e.key === 'Home' ? 0 : e.key === 'End' ? tabs.length - 1 : current + step, { focus: true });
    });
  });
}
