<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        die('Token CSRF tidak valid. Silakan muat ulang halaman.');
    }

    $data = [
        'email'    => sanitize($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
    ];
    $old['email'] = $data['email'];

    $errors = validate_login($data);

    if (empty($errors)) {
        $pdo  = getConnection();
        // Prepared statement -> mencegah SQL Injection walau $data['email'] dari user
        $stmt = $pdo->prepare('SELECT id, name, password FROM users WHERE email = :email');
        $stmt->execute(['email' => $data['email']]);
        $user = $stmt->fetch();

        // password_verify membandingkan password input dengan hash di DB
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $errors['login'] = 'Email atau password salah.';
        } else {
            // Regenerasi session id setelah login -> mencegah session fixation
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            redirect('/tasks/index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Manajemen Tugas Kelas</title>
    <link rel="stylesheet" href="<?= asset('/assets/style.css') ?>">
</head>
<body>
<div class="card">
    <h1>Login</h1>

    <?php if (isset($_GET['registered'])): ?>
        <p class="success">Pendaftaran berhasil, silakan login.</p>
    <?php endif; ?>
    <?php if (isset($errors['login'])): ?>
        <p class="error"><?= e($errors['login']) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">

        <label>Email</label>
        <input type="text" name="email" value="<?= e($old['email']) ?>">
        <?php if (isset($errors['email'])): ?><p class="error"><?= e($errors['email']) ?></p><?php endif; ?>

        <label>Password</label>
        <input type="password" name="password">
        <?php if (isset($errors['password'])): ?><p class="error"><?= e($errors['password']) ?></p><?php endif; ?>

        <button type="submit">Login</button>
    </form>

    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</div>
</body>
</html>
