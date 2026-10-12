<?php
$title = 'Log in';
$error = null;

if (is_post()) {
    csrf_check();
    if (attempt_login(trim($_POST['username'] ?? ''), $_POST['password'] ?? '')) {
        flash('success', 'Welcome back!');
        redirect('dashboard');
    }
    $error = 'Wrong username or password.';
}
?>
<section class="panel narrow">
  <h1>Log in</h1>
  <?php if ($error): ?><p class="flash flash-error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" class="stack">
    <?= csrf_field() ?>
    <label>Username <input name="username" required autofocus autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>"></label>
    <label>Password <input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn">Log in</button>
  </form>
  <p class="muted">No account yet? <a href="<?= url('register') ?>">Create one</a>.</p>
</section>
