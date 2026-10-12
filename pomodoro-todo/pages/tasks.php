<?php
$title = 'Tasks';
$uid   = user_id();

if (is_post()) {
    csrf_check();
    $action = $_POST['action'] ?? '';
    $id     = (int) ($_POST['id'] ?? 0);

    switch ($action) {
        case 'add':
        case 'update':
            $name = trim($_POST['title'] ?? '');
            $est  = max(1, min(12, (int) ($_POST['est_pomodoros'] ?? 1)));
            $cat  = (int) ($_POST['category_id'] ?? 0) ?: null;

            if ($name === '' || mb_strlen($name) > 120) {
                flash('error', 'Task title must be 1–120 characters.');
                break;
            }
            // Only allow categories that belong to this user.
            if ($cat && !query('SELECT 1 FROM categories WHERE id = ? AND user_id = ?', [$cat, $uid])->fetch()) {
                $cat = null;
            }
            if ($action === 'add') {
                query('INSERT INTO tasks (user_id, category_id, title, est_pomodoros) VALUES (?, ?, ?, ?)',
                      [$uid, $cat, $name, $est]);
                flash('success', 'Task added.');
            } else {
                query('UPDATE tasks SET title = ?, category_id = ?, est_pomodoros = ? WHERE id = ? AND user_id = ?',
                      [$name, $cat, $est, $id, $uid]);
                flash('success', 'Task updated.');
            }
            break;

        case 'toggle':
            query("UPDATE tasks SET status = IF(status = 'open', 'done', 'open') WHERE id = ? AND user_id = ?", [$id, $uid]);
            break;

        case 'delete':
            query('DELETE FROM tasks WHERE id = ? AND user_id = ?', [$id, $uid]);
            flash('success', 'Task deleted.');
            break;
    }
    redirect('tasks');
}

$filter = in_array($_GET['status'] ?? '', ['open', 'done'], true) ? $_GET['status'] : 'all';
$sql    = "SELECT t.*, c.name AS category,
                  (SELECT COUNT(*) FROM pomodoro_sessions s
                    WHERE s.task_id = t.id AND s.type = 'focus') AS done_pomodoros
             FROM tasks t LEFT JOIN categories c ON c.id = t.category_id
            WHERE t.user_id = ?";
$params = [$uid];
if ($filter !== 'all') {
    $sql    .= ' AND t.status = ?';
    $params[] = $filter;
}
$tasks      = query($sql . " ORDER BY t.status = 'done', t.created_at DESC", $params)->fetchAll();
$categories = query('SELECT id, name FROM categories WHERE user_id = ? ORDER BY name', [$uid])->fetchAll();
$editing    = isset($_GET['edit'])
    ? query('SELECT * FROM tasks WHERE id = ? AND user_id = ?', [(int) $_GET['edit'], $uid])->fetch()
    : false;
?>
<h1>Tasks</h1>

<section class="panel">
  <h2><?= $editing ? 'Edit task' : 'Add a task' ?></h2>
  <form method="post" class="inline-form">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="<?= $editing ? 'update' : 'add' ?>">
    <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
    <label class="grow">What will you work on?
      <input name="title" required maxlength="120" value="<?= e($editing['title'] ?? '') ?>"></label>
    <label>Category
      <select name="category_id">
        <option value="">None</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($editing['category_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select></label>
    <label>Pomodoros
      <input type="number" name="est_pomodoros" min="1" max="12" value="<?= (int) ($editing['est_pomodoros'] ?? 1) ?>"></label>
    <button class="btn"><?= $editing ? 'Save changes' : 'Add task' ?></button>
    <?php if ($editing): ?><a class="btn ghost" href="<?= url('tasks') ?>">Cancel</a><?php endif; ?>
  </form>
</section>

<div class="tabs">
  <?php foreach (['all' => 'All', 'open' => 'Open', 'done' => 'Done'] as $key => $label): ?>
    <a href="<?= url('tasks', $key === 'all' ? [] : ['status' => $key]) ?>" <?= $filter === $key ? 'aria-current="true"' : '' ?>><?= $label ?></a>
  <?php endforeach; ?>
</div>

<?php if (!$tasks): ?>
  <p class="muted">No tasks here yet.</p>
<?php else: ?>
  <ul class="rows task-list">
    <?php foreach ($tasks as $t): ?>
      <li class="<?= $t['status'] === 'done' ? 'is-done' : '' ?>">
        <form method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $t['id'] ?>">
          <button class="check" aria-label="<?= $t['status'] === 'done' ? 'Mark as open' : 'Mark as done' ?>"><?= $t['status'] === 'done' ? '✓' : '' ?></button>
        </form>
        <div class="grow">
          <strong><?= e($t['title']) ?></strong>
          <?php if ($t['category']): ?><span class="tag"><?= e($t['category']) ?></span><?php endif; ?>
          <span class="dots" title="<?= (int) $t['done_pomodoros'] ?> of <?= (int) $t['est_pomodoros'] ?> pomodoros">
            <?php for ($i = 0; $i < $t['est_pomodoros']; $i++): ?><i class="<?= $i < $t['done_pomodoros'] ? 'on' : '' ?>"></i><?php endfor; ?>
          </span>
        </div>
        <?php if ($t['status'] === 'open'): ?>
          <a class="btn small" href="<?= url('timer', ['task' => $t['id']]) ?>">Focus</a>
        <?php endif; ?>
        <a class="btn small ghost" href="<?= url('tasks', ['edit' => $t['id']]) ?>">Edit</a>
        <form method="post" onsubmit="return confirm('Delete this task?')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $t['id'] ?>">
          <button class="btn small danger">Delete</button>
        </form>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
