<?php
$title   = 'Timer';
$uid     = user_id();
$scripts = ['assets/timer.js'];

$tasks    = query("SELECT id, title FROM tasks WHERE user_id = ? AND status = 'open' ORDER BY created_at", [$uid])->fetchAll();
$selected = (int) ($_GET['task'] ?? 0);
$today    = (int) query(
    "SELECT COUNT(*) FROM pomodoro_sessions WHERE user_id = ? AND type = 'focus' AND DATE(completed_at) = CURDATE()",
    [$uid]
)->fetchColumn();
?>
<h1>Timer</h1>

<section id="timer" class="panel timer mode-focus"
         data-focus="<?= FOCUS_MINUTES ?>" data-short="<?= SHORT_BREAK_MINUTES ?>" data-long="<?= LONG_BREAK_MINUTES ?>"
         data-log-url="<?= e(url('log_session')) ?>" data-csrf="<?= csrf_token() ?>" data-today="<?= $today ?>">
  <div class="modes" role="group" aria-label="Timer mode">
    <button type="button" data-mode="focus" aria-pressed="true">Focus</button>
    <button type="button" data-mode="short_break" aria-pressed="false">Short break</button>
    <button type="button" data-mode="long_break" aria-pressed="false">Long break</button>
  </div>

  <div class="clock" id="clock" aria-live="off">25:00</div>

  <label class="task-pick">Working on
    <select id="task">
      <option value="">No specific task</option>
      <?php foreach ($tasks as $t): ?>
        <option value="<?= $t['id'] ?>" <?= $selected === (int) $t['id'] ? 'selected' : '' ?>><?= e($t['title']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>

  <div class="controls">
    <button type="button" class="btn" id="start">Start</button>
    <button type="button" class="btn ghost" id="reset">Reset</button>
  </div>

  <p class="muted" id="status">Pomodoros finished today: <strong id="count"><?= $today ?></strong></p>
</section>
