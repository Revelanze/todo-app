<?php
/**
 * includes/functions.php
 * Kumpulan fungsi bantuan: sanitasi input, validasi, CSRF token, session guard.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

/**
 * Sanitasi input dasar (CPMK091 - mitigasi XSS saat input & saat output).
 * - trim: buang spasi berlebih
 * - strip_tags: buang tag HTML/JS yang mungkin disisipkan
 */
function sanitize(string $data): string
{
    return trim(strip_tags($data));
}

/**
 * Escape output ke HTML (dipakai setiap kali data ditampilkan ke halaman)
 * supaya walau ada karakter <script> dsb, browser menampilkannya sebagai teks biasa.
 */
function e(string $data): string
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * ================= CSRF PROTECTION =================
 * Token acak disisipkan di setiap form, dicek ulang saat submit.
 * Mencegah request palsu dari situs lain (Cross Site Request Forgery).
 */
function generate_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && $token !== null && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * ================= VALIDASI INPUT (CPMK093) =================
 */
function validate_register(array $data): array
{
    $errors = [];

    if (empty($data['name']) || strlen($data['name']) < 3) {
        $errors['name'] = 'Nama minimal 3 karakter.';
    }

    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }

    if (empty($data['password']) || strlen($data['password']) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    if (empty($data['confirm_password']) || $data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = 'Konfirmasi password tidak sama.';
    }

    return $errors;
}

function validate_login(array $data): array
{
    $errors = [];

    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }

    if (empty($data['password'])) {
        $errors['password'] = 'Password wajib diisi.';
    }

    return $errors;
}

function validate_task(array $data): array
{
    $errors = [];

    if (empty($data['title']) || strlen($data['title']) < 3) {
        $errors['title'] = 'Judul tugas minimal 3 karakter.';
    }

    if (strlen($data['title'] ?? '') > 150) {
        $errors['title'] = 'Judul tugas maksimal 150 karakter.';
    }

    if (!empty($data['deadline'])) {
        $d = DateTime::createFromFormat('Y-m-d', $data['deadline']);
        if (!$d || $d->format('Y-m-d') !== $data['deadline']) {
            $errors['deadline'] = 'Format tanggal deadline tidak valid.';
        }
    }

    $allowedStatus = ['belum', 'proses', 'selesai'];
    if (!empty($data['status']) && !in_array($data['status'], $allowedStatus, true)) {
        $errors['status'] = 'Status tidak valid.';
    }

    return $errors;
}

/**
 * ================= AUTH GUARD =================
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
}

/**
 * Redirect ke path di dalam aplikasi (path diawali '/', mis. '/tasks/index.php').
 * BASE_URL otomatis ditambahkan di depan supaya tetap benar walau project
 * ditaruh di subfolder seperti htdocs/todo-app/.
 */
function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

/**
 * Untuk dipakai di atribut href/src, mis: <link href="<?= asset('/assets/style.css') ?>">
 */
function asset(string $path): string
{
    return BASE_URL . $path;
}
