<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(TT_SESSION_NAME);
    session_start();
}

function tt_admin_logged_in(): bool
{
    return !empty($_SESSION['tt_admin']);
}

function tt_require_admin(): void
{
    if (!tt_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function tt_admin_login(string $user, string $pass): bool
{
    if (!tt_verify_login($user, $pass)) {
        return false;
    }
    $_SESSION['tt_admin'] = true;
    $_SESSION['tt_admin_user'] = $user;
    $_SESSION['tt_admin_time'] = time();
    return true;
}

function tt_admin_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
