<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/tasks/index.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    die('Token CSRF tidak valid. Silakan muat ulang halaman.');
}

$id  = (int)($_POST['id'] ?? 0);
$pdo = getConnection();

// DELETE: prepared statement + WHERE user_id -> user cuma bisa hapus tugas miliknya sendiri
$stmt = $pdo->prepare('DELETE FROM tasks WHERE id = :id AND user_id = :uid');
$stmt->execute(['id' => $id, 'uid' => $_SESSION['user_id']]);

redirect('/tasks/index.php?success=deleted');
