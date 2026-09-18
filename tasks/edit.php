<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$pdo = getConnection();
$id  = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Ambil task, pastikan task ini milik user yang sedang login (ownership check)
$stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id AND user_id = :uid');
$stmt->execute(['id' => $id, 'uid' => $_SESSION['user_id']]);
$task = $stmt->fetch();

if (!$task) {
    die('Tugas tidak ditemukan atau bukan milik Anda.');
}

$errors = [];
$old = [
    'title'       => $task['title'],
    'description' => $task['description'],
    'deadline'    => $task['deadline'],
    'status'      => $task['status'],
];

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
        // UPDATE: prepared statement + WHERE user_id memastikan user hanya bisa update tugas miliknya
        $stmt = $pdo->prepare(
            'UPDATE tasks SET title = :title, description = :description, deadline = :deadline, status = :status
             WHERE id = :id AND user_id = :uid'
        );
        $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'],
            'deadline'    => $data['deadline'] ?: null,
            'status'      => $data['status'],
            'id'          => $id,
            'uid'         => $_SESSION['user_id'],
        ]);

        redirect('/tasks/index.php?success=updated');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Tugas</title>
    <link rel="stylesheet" href="<?= asset('/assets/style.css') ?>">
</head>
<body>
<div class="card">
    <h1>Edit Tugas</h1>

    <form method="POST" action="edit.php?id=<?= (int)$id ?>" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$id ?>">

        <label>Judul Tugas</label>
        <input type="text" name="title" value="<?= e($old['title']) ?>">
        <?php if (isset($errors['title'])): ?><p class="error"><?= e($errors['title']) ?></p><?php endif; ?>

        <label>Deskripsi</label>
        <textarea name="description" rows="4"><?= e($old['description'] ?? '') ?></textarea>

        <label>Deadline</label>
        <input type="date" name="deadline" value="<?= e($old['deadline'] ?? '') ?>">
        <?php if (isset($errors['deadline'])): ?><p class="error"><?= e($errors['deadline']) ?></p><?php endif; ?>

        <label>Status</label>
        <select name="status">
            <option value="belum" <?= $old['status'] === 'belum' ? 'selected' : '' ?>>Belum Dikerjakan</option>
            <option value="proses" <?= $old['status'] === 'proses' ? 'selected' : '' ?>>Sedang Dikerjakan</option>
            <option value="selesai" <?= $old['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>

        <button type="submit">Update</button>
        <a href="index.php" class="btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
