// Pomodoro timer. Counts down using a fixed end time so it stays accurate in background tabs.
(() => {
  const root = document.getElementById('timer');
  if (!root) return;

  const MODES = {
    focus:       { label: 'Focus',       minutes: +root.dataset.focus },
    short_break: { label: 'Short break', minutes: +root.dataset.short },
    long_break:  { label: 'Long break',  minutes: +root.dataset.long },
  };
  const clock  = document.getElementById('clock');
  const start  = document.getElementById('start');
  const reset  = document.getElementById('reset');
  const task   = document.getElementById('task');
  const count  = document.getElementById('count');
  const status = document.getElementById('status');

  let mode = 'focus';
  let remaining = MODES[mode].minutes * 60; // seconds
  let endAt = null;
  let ticker = null;
  let finishedToday = +root.dataset.today;

  const format = s => `${String(Math.floor(s / 60)).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;

  function render() {
    clock.textContent = format(remaining);
    document.title = `${format(remaining)} · ${MODES[mode].label}`;
  }

  function setMode(next) {
    stop();
    mode = next;
    remaining = MODES[mode].minutes * 60;
    root.className = `panel timer mode-${mode.replace('_', '-')}`;
    root.querySelectorAll('[data-mode]').forEach(b => b.setAttribute('aria-pressed', b.dataset.mode === mode));
    task.disabled = mode !== 'focus';
    render();
  }

  function stop() {
    clearInterval(ticker);
    ticker = null;
    start.textContent = 'Start';
  }

  function tick() {
    remaining = Math.max(0, Math.round((endAt - Date.now()) / 1000));
    render();
    if (remaining === 0) finish();
  }

  function beep() {
    try {
      const ctx = new AudioContext();
      const osc = ctx.createOscillator();
      osc.connect(ctx.destination);
      osc.frequency.value = 880;
      osc.start();
      osc.stop(ctx.currentTime + 0.4);
    } catch (_) { /* sound is optional */ }
  }

  async function finish() {
    stop();
    beep();
    const finished = mode;
    try {
      const body = new URLSearchParams({ type: finished, task_id: task.value, csrf: root.dataset.csrf });
      const res = await fetch(root.dataset.logUrl, { method: 'POST', body });
      const data = await res.json();
      if (data.ok) {
        finishedToday = data.today;
        count.textContent = finishedToday;
        status.firstChild.textContent = `${MODES[finished].label} saved. Pomodoros finished today: `;
      }
    } catch (_) {
      status.textContent = 'Could not save this session. Check your connection.';
    }
    // After focus: long break every 4th pomodoro, otherwise short. After a break: back to focus.
    setMode(finished === 'focus' ? (finishedToday % 4 === 0 ? 'long_break' : 'short_break') : 'focus');
  }

  start.addEventListener('click', () => {
    if (ticker) { // pause
      stop();
      return;
    }
    endAt = Date.now() + remaining * 1000;
    ticker = setInterval(tick, 250);
    start.textContent = 'Pause';
  });

  reset.addEventListener('click', () => setMode(mode));
  root.querySelectorAll('[data-mode]').forEach(b => b.addEventListener('click', () => setMode(b.dataset.mode)));

  render();
})();
