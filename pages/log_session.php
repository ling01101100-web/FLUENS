<?php
/** JSON endpoint: the timer calls this when a session finishes. */
header('Content-Type: application/json');

function reply(int $code, array $data): void
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if (!is_post()) {
    reply(405, ['ok' => false, 'error' => 'POST only']);
}
csrf_check();

$uid     = user_id();
$type    = $_POST['type'] ?? '';
$taskId  = (int) ($_POST['task_id'] ?? 0) ?: null;
$minutes = ['focus' => FOCUS_MINUTES, 'short_break' => SHORT_BREAK_MINUTES, 'long_break' => LONG_BREAK_MINUTES][$type] ?? null;

if ($minutes === null) {
    reply(400, ['ok' => false, 'error' => 'Unknown session type']);
}
if ($taskId && !query('SELECT 1 FROM tasks WHERE id = ? AND user_id = ?', [$taskId, $uid])->fetch()) {
    reply(403, ['ok' => false, 'error' => 'Not your task']);
}

query('INSERT INTO pomodoro_sessions (user_id, task_id, type, duration_minutes) VALUES (?, ?, ?, ?)',
      [$uid, $type === 'focus' ? $taskId : null, $type, $minutes]);

$today = (int) query(
    "SELECT COUNT(*) FROM pomodoro_sessions WHERE user_id = ? AND type = 'focus' AND DATE(completed_at) = CURDATE()",
    [$uid]
)->fetchColumn();

reply(200, ['ok' => true, 'today' => $today]);
