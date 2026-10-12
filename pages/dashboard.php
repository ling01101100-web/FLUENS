<?php
$title = 'Dashboard';
$uid   = user_id();

$stats = query(
    "SELECT
       (SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status = 'open') AS open_tasks,
       (SELECT COUNT(*) FROM pomodoro_sessions
         WHERE user_id = ? AND type = 'focus' AND DATE(completed_at) = CURDATE()) AS today_pomodoros,
       (SELECT COALESCE(SUM(duration_minutes), 0) FROM pomodoro_sessions
         WHERE user_id = ? AND type = 'focus') AS total_minutes",
    [$uid, $uid, $uid]
)->fetch();

$upNext = query(
    "SELECT id, title FROM tasks WHERE user_id = ? AND status = 'open' ORDER BY created_at LIMIT 5",
    [$uid]
)->fetchAll();
?>
<h1>Hello, <?= e(current_user()['username']) ?></h1>

<div class="stats">
  <div><strong><?= (int) $stats['today_pomodoros'] ?></strong> pomodoros today</div>
  <div><strong><?= (int) $stats['open_tasks'] ?></strong> open tasks</div>
  <div><strong><?= (int) $stats['total_minutes'] ?></strong> focus minutes in total</div>
</div>

<section class="panel">
  <h2>Up next</h2>
  <?php if (!$upNext): ?>
    <p class="muted">Nothing planned. <a href="<?= url('tasks') ?>">Add your first task</a>.</p>
  <?php else: ?>
    <ul class="rows">
      <?php foreach ($upNext as $task): ?>
        <li><span><?= e($task['title']) ?></span>
            <a class="btn small" href="<?= url('timer', ['task' => $task['id']]) ?>">Start focus</a></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>

<?php if (has_role(['manager', 'admin'])): ?>
  <p><a href="<?= url('reports') ?>">See how the team is doing →</a></p>
<?php endif; ?>
