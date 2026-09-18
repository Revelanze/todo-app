<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$old = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Cek CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        die('Token CSRF tidak valid. Silakan muat ulang halaman.');
    }

    // 2. Sanitasi input mentah
    $data = [
        'name'             => sanitize($_POST['name'] ?? ''),
        'email'            => sanitize($_POST['email'] ?? ''),
        'password'         => $_POST['password'] ?? '',       // password TIDAK di-strip_tags, langsung di-hash
        'confirm_password' => $_POST['confirm_password'] ?? '',
    ];
    $old = ['name' => $data['name'], 'email' => $data['email']];

    // 3. Validasi
    $errors = validate_register($data);

    // 4. Cek email sudah dipakai atau belum (prepared statement -> aman dari SQL Injection)
    if (empty($errors)) {
        $pdo  = getConnection();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $data['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Email sudah terdaftar.';
        }
    }

    // 5. Simpan user baru
    if (empty($errors)) {
        // Enkripsi/hash password dengan bcrypt (CPMK091 - enkripsi dasar)
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)'
        );
        $stmt->execute([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $hashedPassword,
        ]);

        redirect('/auth/login.php?registered=1');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun - Manajemen Tugas Kelas</title>
    <link rel="stylesheet" href="<?= asset('/assets/style.css') ?>">
</head>
<body>
<div class="card">
    <h1>Daftar Akun</h1>

    <form method="POST" action="register.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">

        <label>Nama Lengkap</label>
        <input type="text" name="name" value="<?= e($old['name']) ?>">
        <?php if (isset($errors['name'])): ?><p class="error"><?= e($errors['name']) ?></p><?php endif; ?>

        <label>Email</label>
        <input type="text" name="email" value="<?= e($old['email']) ?>">
        <?php if (isset($errors['email'])): ?><p class="error"><?= e($errors['email']) ?></p><?php endif; ?>

        <label>Password</label>
        <input type="password" name="password">
        <?php if (isset($errors['password'])): ?><p class="error"><?= e($errors['password']) ?></p><?php endif; ?>

        <label>Konfirmasi Password</label>
        <input type="password" name="confirm_password">
        <?php if (isset($errors['confirm_password'])): ?><p class="error"><?= e($errors['confirm_password']) ?></p><?php endif; ?>

        <button type="submit">Daftar</button>
    </form>

    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
</div>
</body>
</html>
