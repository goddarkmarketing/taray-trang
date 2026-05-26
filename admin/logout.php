<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
tt_admin_logout();
header('Location: login.php');
exit;
