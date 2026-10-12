<?php /** Shared page frame. Expects: $title, $content, $routes, $page, optional $scripts */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?><?= $title ? ' · ' : '' ?>FLUENS</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="<?= url('dashboard') ?>">FLUENS</a>
  <?php if ($user = current_user()): ?>
    <nav aria-label="Main">
      <?php foreach ($routes as $name => $route): ?>
        <?php if (isset($route['nav']) && can_access($route)): ?>
          <a href="<?= url($name) ?>" <?= $name === $page ? 'aria-current="page"' : '' ?>><?= e($route['nav']) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>
    <form class="who" method="post" action="<?= url('logout') ?>">
      <?= csrf_field() ?>
      <span class="role role-<?= e($user['role']) ?>"><?= e($user['username']) ?> · <?= e($user['role']) ?></span>
      <button class="link">Log out</button>
    </form>
  <?php endif; ?>
</header>

<main>
  <?php foreach (pull_flash() as $f): ?>
    <p class="flash flash-<?= e($f['type']) ?>" role="status"><?= e($f['message']) ?></p>
  <?php endforeach; ?>
  <?= $content ?>
</main>

<?php foreach ($scripts ?? [] as $src): ?>
  <script src="<?= e($src) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
