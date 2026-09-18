<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$errors = [];
$old = ['title' => '', 'description' => '', 'deadline' => '', 'status' => 'belum'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        die('Token CSRF tidak valid. Silakan muat ulang halaman.');
    }

    $data = [
        'title'       => sanitize($_POST['title'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'deadline'    => sanitize($_POST['deadline'] ?? ''),
        'status'      => sanitize($_POST['status'] ?? 'belum'),
    ];
    $old = $data;

    $errors = validate_task($data);

    if (empty($errors)) {
        $pdo  = getConnection();
        // CREATE: prepared statement, tidak ada string concatenation ke SQL -> aman dari SQL Injection
        $stmt = $pdo->prepare(
            'INSERT INTO tasks (user_id, title, description, deadline, status)
             VALUES (:user_id, :title, :description, :deadline, :status)'
        );
        $stmt->execute([
            'user_id'     => $_SESSION['user_id'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'deadline'    => $data['deadline'] ?: null,
            'status'      => $data['status'],
        ]);

        redirect('/tasks/index.php?success=created');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Tugas</title>
    <link rel="stylesheet" href="<?= asset('/assets/style.css') ?>">
</head>
<body>
<div class="card">
    <h1>Tambah Tugas</h1>

    <form method="POST" action="create.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">

        <label>Judul Tugas</label>
        <input type="text" name="title" value="<?= e($old['title']) ?>">
        <?php if (isset($errors['title'])): ?><p class="error"><?= e($errors['title']) ?></p><?php endif; ?>

        <label>Deskripsi</label>
        <textarea name="description" rows="4"><?= e($old['description']) ?></textarea>

        <label>Deadline</label>
        <input type="date" name="deadline" value="<?= e($old['deadline']) ?>">
        <?php if (isset($errors['deadline'])): ?><p class="error"><?= e($errors['deadline']) ?></p><?php endif; ?>

        <label>Status</label>
        <select name="status">
            <option value="belum" <?= $old['status'] === 'belum' ? 'selected' : '' ?>>Belum Dikerjakan</option>
            <option value="proses" <?= $old['status'] === 'proses' ? 'selected' : '' ?>>Sedang Dikerjakan</option>
            <option value="selesai" <?= $old['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>

        <button type="submit">Simpan</button>
        <a href="index.php" class="btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
