<?php
/**
 * Route table: the single place that decides who can open what.
 *   access: 'guest' (logged-out only) | 'auth' (any logged-in role) | [roles allowed]
 *   nav:    label shown in the menu (omit to hide)
 *   raw:    true = no page layout (redirects / JSON endpoints)
 */
return [
    'login'       => ['file' => 'login',       'access' => 'guest'],
    'register'    => ['file' => 'register',    'access' => 'guest'],
    'logout'      => ['file' => 'logout',      'access' => 'auth', 'raw' => true],
    'dashboard'   => ['file' => 'dashboard',   'access' => 'auth', 'nav' => 'Dashboard'],
    'tasks'       => ['file' => 'tasks',       'access' => 'auth', 'nav' => 'Tasks'],
    'categories'  => ['file' => 'categories',  'access' => 'auth', 'nav' => 'Categories'],
    'timer'       => ['file' => 'timer',       'access' => 'auth', 'nav' => 'Timer'],
    'log_session' => ['file' => 'log_session', 'access' => 'auth', 'raw' => true],
    'reports'     => ['file' => 'reports',     'access' => ['manager', 'admin'], 'nav' => 'Team report'],
    'users'       => ['file' => 'users',       'access' => ['admin'],            'nav' => 'Users'],
];
