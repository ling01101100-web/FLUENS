<?php
$title = 'Categories';
$uid   = user_id();

if (is_post()) {
    csrf_check();
    if (($_POST['action'] ?? '') === 'add') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || mb_strlen($name) > 40) {
            flash('error', 'Category name must be 1–40 characters.');
        } elseif (query('SELECT 1 FROM categories WHERE user_id = ? AND name = ?', [$uid, $name])->fetch()) {
            flash('error', 'You already have a category with that name.');
        } else {
            query('INSERT INTO categories (user_id, name) VALUES (?, ?)', [$uid, $name]);
            flash('success', 'Category added.');
        }
    } elseif (($_POST['action'] ?? '') === 'delete') {
        query('DELETE FROM categories WHERE id = ? AND user_id = ?', [(int) ($_POST['id'] ?? 0), $uid]);
        flash('success', 'Category deleted. Its tasks were kept.');
    }
    redirect('categories');
}

$categories = query(
    'SELECT c.id, c.name, COUNT(t.id) AS task_count
       FROM categories c LEFT JOIN tasks t ON t.category_id = c.id
      WHERE c.user_id = ? GROUP BY c.id, c.name ORDER BY c.name',
    [$uid]
)->fetchAll();
?>
<h1>Categories</h1>

<section class="panel">
  <form method="post" class="inline-form">
    <?= csrf_field() ?><input type="hidden" name="action" value="add">
    <label class="grow">New category <input name="name" required maxlength="40"></label>
    <button class="btn">Add category</button>
  </form>
</section>

<?php if (!$categories): ?>
  <p class="muted">No categories yet. Group your tasks, for example "Study" or "Work".</p>
<?php else: ?>
  <ul class="rows">
    <?php foreach ($categories as $c): ?>
      <li><span class="grow"><strong><?= e($c['name']) ?></strong> <span class="muted"><?= (int) $c['task_count'] ?> tasks</span></span>
        <form method="post" onsubmit="return confirm('Delete this category?')">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $c['id'] ?>">
          <button class="btn small danger">Delete</button>
        </form></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
