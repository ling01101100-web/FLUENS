<?php
$title = 'Team report';

// Read-only overview of every user's progress (managers and admins only).
$rows = query(
    "SELECT u.username, r.name AS role,
            (SELECT COUNT(*) FROM tasks t WHERE t.user_id = u.id) AS total_tasks,
            (SELECT COUNT(*) FROM tasks t WHERE t.user_id = u.id AND t.status = 'done') AS done_tasks,
            (SELECT COUNT(*) FROM pomodoro_sessions s WHERE s.user_id = u.id AND s.type = 'focus') AS pomodoros,
            (SELECT COALESCE(SUM(s.duration_minutes), 0) FROM pomodoro_sessions s
              WHERE s.user_id = u.id AND s.type = 'focus') AS focus_minutes
       FROM users u JOIN roles r ON r.id = u.role_id
      ORDER BY pomodoros DESC, u.username"
)->fetchAll();
?>
<h1>Team report</h1>
<div class="table-wrap">
  <table>
    <thead><tr><th>User</th><th>Role</th><th>Tasks done</th><th>Pomodoros</th><th>Focus minutes</th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= e($r['username']) ?></td>
          <td><?= e($r['role']) ?></td>
          <td><?= (int) $r['done_tasks'] ?> / <?= (int) $r['total_tasks'] ?></td>
          <td><?= (int) $r['pomodoros'] ?></td>
          <td><?= (int) $r['focus_minutes'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
