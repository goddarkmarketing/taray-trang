<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (tt_admin_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (tt_admin_login($user, $pass)) {
        header('Location: index.php');
        exit;
    }
    $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>เข้าสู่ระบบ — Talay Trang Admin</title>
  <link rel="stylesheet" href="assets/admin.css"/>
</head>
<body>
  <div class="login-wrap">
    <form class="login-card" method="post" action="">
      <h1>Talay Trang</h1>
      <p>ระบบจัดการเนื้อหาเว็บไซต์</p>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>
      <div class="field">
        <label for="username">ชื่อผู้ใช้</label>
        <input id="username" name="username" type="text" required autocomplete="username" value="admin"/>
      </div>
      <div class="field">
        <label for="password">รหัสผ่าน</label>
        <input id="password" name="password" type="password" required autocomplete="current-password"/>
      </div>
      <button class="btn btn-primary" type="submit" style="width:100%;margin-top:8px">เข้าสู่ระบบ</button>
    </form>
  </div>
</body>
</html>
