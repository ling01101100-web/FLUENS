const WORK = 25 * 60;
const BREAK = 5 * 60;
const LONG_BREAK = 15 * 60;
const CYCLES = 4;

let mode = "work";
let remaining = WORK;
let timer = null;
let endAt = 0;
let completed = 0;

const $ = (id) => document.getElementById(id);
const modeEl = $("mode");
const timeEl = $("time");
const startBtn = $("start");
const resetBtn = $("reset");
const skipBtn = $("skip");
const sessionEl = $("sessions");

function format(sec) {
  const m = Math.floor(sec / 60)
    .toString()
    .padStart(2, "0");
  const s = Math.floor(sec % 60)
    .toString()
    .padStart(2, "0");
  return `${m}:${s}`;
}

function update() {
  timeEl.textContent = format(remaining);
  if (mode === "work") modeEl.textContent = "Focus";
  if (mode === "long") modeEl.textContent = "Long Break";
  if (mode === "break") modeEl.textContent = "Break";

  document.documentElement.style.setProperty(
    "--accent",
    mode === "work" ? "#ff453a" : "#30d158",
  );

  sessionEl.textContent = `Completed: ${completed}`;
  document.title = `${format(remaining)} · ${modeEl.textContent}`;
}

// -------------------------------------------------------------
function nextMode(count) {
  if (mode === "work") {
    if (count) completed++;
    mode = count && completed % CYCLES === 0 ? "long" : "break";
  } else {
    mode = "work";
  }
}
