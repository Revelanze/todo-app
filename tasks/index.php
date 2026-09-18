<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoload -> untuk library Carbon

use Carbon\Carbon;

require_login();

$pdo = getConnection();

// READ: ambil hanya task milik user yang login (prepared statement -> aman dari SQLi)
$stmt = $pdo->prepare('SELECT * FROM tasks WHERE user_id = :uid ORDER BY deadline IS NULL, deadline ASC, created_at DESC');
$stmt->execute(['uid' => $_SESSION['user_id']]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Tugas</title>
    <link rel="stylesheet" href="<?= asset('/assets/style.css') ?>">
</head>
<body>
<div class="card wide">
    <div class="topbar">
        <h1>Halo, <?= e($_SESSION['user_name']) ?> 👋</h1>
        <a href="<?= asset('/auth/logout.php') ?>" class="btn-secondary">Logout</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <p class="success">
            <?php
            $msg = ['created' => 'Tugas berhasil ditambahkan.', 'updated' => 'Tugas berhasil diperbarui.', 'deleted' => 'Tugas berhasil dihapus.'];
            echo e($msg[$_GET['success']] ?? 'Berhasil.');
            ?>
        </p>
    <?php endif; ?>

    <a href="create.php" class="btn">+ Tambah Tugas</a>

    <table>
        <thead>
        <tr>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($tasks)): ?>
            <tr><td colspan="5">Belum ada tugas. Yuk tambah tugas pertamamu!</td></tr>
        <?php endif; ?>

        <?php foreach ($tasks as $task): ?>
            <tr>
                <!-- Semua output di-escape pakai e() -> mitigasi XSS saat menampilkan data -->
                <td><?= e($task['title']) ?></td>
                <td><?= e($task['description'] ?? '') ?></td>
                <td>
                    <?php if ($task['deadline']): ?>
                        <?php
                            // Pemakaian library Carbon (nesbot/carbon) untuk menghitung sisa waktu
                            $deadline = Carbon::parse($task['deadline']);
                            $today = Carbon::today();
                            $diff = $today->diffInDays($deadline, false);
                        ?>
                        <?= e($deadline->translatedFormat('d M Y')) ?>
                        <?php if ($task['status'] !== 'selesai'): ?>
                            <?php if ($diff < 0): ?>
                                <span class="badge badge-danger">Terlambat <?= abs($diff) ?> hari</span>
                            <?php elseif ($diff === 0): ?>
                                <span class="badge badge-warning">Hari ini</span>
                            <?php else: ?>
                                <span class="badge badge-info"><?= $diff ?> hari lagi</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><span class="status status-<?= e($task['status']) ?>"><?= e($task['status']) ?></span></td>
                <td>
                    <a href="edit.php?id=<?= (int)$task['id'] ?>">Edit</a>
                    <form method="POST" action="delete.php" class="inline-form" onsubmit="return confirm('Yakin hapus tugas ini?');">
                        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">
                        <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
                        <button type="submit" class="btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
