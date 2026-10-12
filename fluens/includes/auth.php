<?php
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function user_id(): int
{
    return (int) ($_SESSION['user']['id'] ?? 0);
}

function has_role(array $roles): bool
{
    $user = current_user();
    return $user !== null && in_array($user['role'], $roles, true);
}

/** Check credentials; on success store the user in the session. */
function attempt_login(string $username, string $password): bool
{
    $user = query(
        'SELECT u.id, u.username, u.password_hash, r.name AS role
           FROM users u JOIN roles r ON r.id = u.role_id
          WHERE u.username = ?',
        [$username]
    )->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['user'] = ['id' => (int) $user['id'], 'username' => $user['username'], 'role' => $user['role']];
    return true;
}

function logout(): void
{
    $_SESSION = [];
    session_destroy();
}
