<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Simple Pomodoro</title>
<style>
  :root {
    --bg: #111;
    --card: #1c1c1e;
    --text: #f5f5f7;
    --accent: #ff453a;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    min-height: 100vh;
    display: grid;
    place-items: center;
    background: var(--bg);
    color: var(--text);
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
  }
  .app {
    width: min(92vw, 360px);
    padding: 32px 24px;
    background: var(--card);
    border-radius: 24px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,.4);
  }
  h1 {
    margin: 0 0 8px;
    font-size: .9rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    opacity: .7;
  }
  #mode {
    margin-bottom: 16px;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--accent);
  }
  #time {
    margin: 8px 0 24px;
    font-size: 5rem;
    font-weight: 700;
    line-height: 1;
    letter-spacing: -.04em;
    font-variant-numeric: tabular-nums;
  }
  .buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
  }
  button {
    padding: 12px 20px;
    border: 0;
    border-radius: 999px;
    background: #2c2c2e;
    color: var(--text);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s, transform .1s;
  }
  button:hover { background: #3a3a3c; }
  button:active { transform: scale(.97); }
  button.primary { background: var(--accent); color: #fff; }
  button.primary:hover { filter: brightness(1.1); }
  #sessions {
    margin-top: 18px;
    font-size: .85rem;
    opacity: .6;
  }
</style>
</head>
<body>
  <main class="app">
    <h1>Pomodoro</h1>
    <div id="mode">Focus</div>
    <div id="time">25:00</div>
    <div class="buttons">
      <button id="start" class="primary">Start</button>
      <button id="reset">Reset</button>
      <button id="skip">Skip</button>
    </div>
    <div id="sessions">Completed: 0</div>
  </main>

<script>
  const WORK = 25 * 60;
  const BREAK = 5 * 60;
  const LONG_BREAK = 15 * 60;
  const CYCLES = 4;

  let mode = 'work';
  let remaining = WORK;
  let timer = null;
  let endAt = 0;
  let completed = 0;

  const $ = id => document.getElementById(id);
  const timeEl = $('time');
  const modeEl = $('mode');
  const startBtn = $('start');
  const sessionsEl = $('sessions');

  function format(sec) {
    const m = Math.floor(sec / 60).toString().padStart(2, '0');
    const s = Math.floor(sec % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
  }

  function update() {
    timeEl.textContent = format(remaining);
    modeEl.textContent =
      mode === 'work' ? 'Focus' :
      mode === 'long' ? 'Long Break' : 'Break';

    document.documentElement.style.setProperty(
      '--accent',
      mode === 'work' ? '#ff453a' : '#30d158'
    );

    sessionsEl.textContent = `Completed: ${completed}`;
    document.title = `${format(remaining)} · ${modeEl.textContent}`;
  }

  function nextMode(count) {
    if (mode === 'work') {
      if (count) completed++;
      mode = count && completed % CYCLES === 0 ? 'long' : 'break';
    } else {
      mode = 'work';
    }

    remaining =
      mode === 'work' ? WORK :
      mode === 'long' ? LONG_BREAK : BREAK;

    update();
  }

  function tick() {
    remaining = Math.max(0, Math.round((endAt - Date.now()) / 1000));
    update();

    if (remaining <= 0) {
      clearInterval(timer);
      timer = null;
      startBtn.textContent = 'Start';
      beep();
      nextMode(true);
    }
  }

  function start() {
    if (timer) {
      clearInterval(timer);
      timer = null;
      remaining = Math.max(0, Math.round((endAt - Date.now()) / 1000));
      startBtn.textContent = 'Start';
      update();
      return;
    }

    endAt = Date.now() + remaining * 1000;
    timer = setInterval(tick, 250);
    startBtn.textContent = 'Pause';
  }

  function reset() {
    clearInterval(timer);
    timer = null;
    mode = 'work';
    remaining = WORK;
    completed = 0;
    startBtn.textContent = 'Start';
    update();
  }

  function skip() {
    clearInterval(timer);
    timer = null;
    startBtn.textContent = 'Start';
    nextMode(false);
  }

  function beep() {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();

      osc.connect(gain);
      gain.connect(ctx.destination);

      osc.frequency.value = 880;
      gain.gain.setValueAtTime(0.001, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime + 0.01);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);

      osc.start();
      osc.stop(ctx.currentTime + 0.3);
    } catch (e) {}
  }

  startBtn.addEventListener('click', start);
  $('reset').addEventListener('click', reset);
  $('skip').addEventListener('click', skip);

  update();
</script>
</body>
</html>