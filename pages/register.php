<?php
$title  = 'Create account';
$errors = [];

if (is_post()) {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        $errors[] = 'Username must be 3–30 letters, numbers or underscores.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== ($_POST['confirm'] ?? '')) {
        $errors[] = 'Passwords do not match.';
    }
    if (!$errors && query('SELECT 1 FROM users WHERE username = ? OR email = ?', [$username, $email])->fetch()) {
        $errors[] = 'That username or email is already taken.';
    }

    if (!$errors) {
        // New sign-ups always get the least-privileged role: "user".
        query(
            "INSERT INTO users (role_id, username, email, password_hash)
             VALUES ((SELECT id FROM roles WHERE name = 'user'), ?, ?, ?)",
            [$username, $email, password_hash($password, PASSWORD_DEFAULT)]
        );
        flash('success', 'Account created. You can log in now.');
        redirect('login');
    }
}
?>
<section class="panel narrow">
  <h1>Create account</h1>
  <?php foreach ($errors as $err): ?><p class="flash flash-error"><?= e($err) ?></p><?php endforeach; ?>
  <form method="post" class="stack">
    <?= csrf_field() ?>
    <label>Username <input name="username" required value="<?= e($_POST['username'] ?? '') ?>"></label>
    <label>Email <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
    <label>Password <input type="password" name="password" required minlength="8"></label>
    <label>Confirm password <input type="password" name="confirm" required minlength="8"></label>
    <button class="btn">Create account</button>
  </form>
  <p class="muted">Already registered? <a href="<?= url('login') ?>">Log in</a>.</p>
</section>
