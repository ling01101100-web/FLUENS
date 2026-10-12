<?php
/** Front controller: every request enters here and is routed by ?page=name */
require __DIR__ . '/includes/bootstrap.php';
$routes = require __DIR__ . '/includes/routes.php';

/** Can the current visitor open this route? */
function can_access(array $route): bool
{
    return match (true) {
        $route['access'] === 'guest' => current_user() === null,
        $route['access'] === 'auth'  => current_user() !== null,
        default                      => has_role($route['access']),
    };
}

/** Render a full-page error inside the normal layout. */
function show_error(int $code, string $message): void
{
    global $routes, $page;
    http_response_code($code);
    $title   = (string) $code;
    $content = '<section class="panel narrow"><h1>' . $code . '</h1><p>' . e($message) . '</p>'
             . '<p><a class="btn" href="' . url('dashboard') . '">Back to dashboard</a></p></section>';
    require __DIR__ . '/includes/layout.php';
    exit;
}

$page  = $_GET['page'] ?? (current_user() ? 'dashboard' : 'login');
$route = $routes[$page] ?? null;

if ($route === null) {
    show_error(404, 'That page does not exist.');
}
if ($route['access'] === 'guest' && current_user()) {
    redirect('dashboard');
}
if ($route['access'] !== 'guest' && !current_user()) {
    flash('error', 'Please log in to continue.');
    redirect('login');
}
if (!can_access($route)) {
    show_error(403, 'Your role does not have access to this page.');
}

$pageFile = __DIR__ . '/pages/' . $route['file'] . '.php';

if (!empty($route['raw'])) {
    require $pageFile;
    exit;
}

$title = '';
$scripts = [];
ob_start();
require $pageFile;
$content = ob_get_clean();
require __DIR__ . '/includes/layout.php';
