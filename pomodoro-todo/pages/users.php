<?php
$title = 'Users';
$me    = user_id();

if (is_post()) {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id === $me) {
        flash('error', 'You cannot change or delete your own account here.');
    } elseif (($_POST['action'] ?? '') === 'role') {
        query('UPDATE users SET role_id = ? WHERE id = ?', [(int) ($_POST['role_id'] ?? 0), $id]);
        flash('success', 'Role updated.');
    } elseif (($_POST['action'] ?? '') === 'delete') {
        query('DELETE FROM users WHERE id = ?', [$id]);
        flash('success', 'User deleted along with their tasks.');
    }
    redirect('users');
}

$roles = query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
$users = query('SELECT u.id, u.username, u.email, u.role_id FROM users u ORDER BY u.id')->fetchAll();
?>
<h1>Users</h1>
<div class="table-wrap">
  <table>
    <thead><tr><th>Username</th><th>Email</th><th>Role</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= e($u['username']) ?></td>
          <td><?= e($u['email']) ?></td>
          <td>
            <form method="post" class="inline-form tight">
              <?= csrf_field() ?><input type="hidden" name="action" value="role"><input type="hidden" name="id" value="<?= $u['id'] ?>">
              <select name="role_id" <?= $u['id'] === $me ? 'disabled' : '' ?> aria-label="Role for <?= e($u['username']) ?>">
                <?php foreach ($roles as $r): ?>
                  <option value="<?= $r['id'] ?>" <?= $r['id'] == $u['role_id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn small" <?= $u['id'] === $me ? 'disabled' : '' ?>>Save role</button>
            </form>
          </td>
          <td>
            <?php if ($u['id'] !== $me): ?>
              <form method="post" onsubmit="return confirm('Delete this user and all their data?')">
                <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button class="btn small danger">Delete</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
