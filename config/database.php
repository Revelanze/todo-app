<?php
/**
 * config/database.php
 * Koneksi ke database menggunakan PDO.
 * PDO + prepared statement dipakai di SELURUH query pada project ini
 * sebagai mitigasi SQL Injection (CPMK091).
 */

// Ganti sesuai konfigurasi MySQL/XAMPP di komputer kamu
define('DB_HOST', 'localhost');
define('DB_NAME', 'todo_kelas');
define('DB_USER', 'root');
define('DB_PASS', '');

function getConnection(): PDO
{
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // biar error kelihatan jelas
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // WAJIB false agar prepared statement asli (bukan emulasi) -> lebih aman dari SQLi
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Jangan tampilkan detail error DB ke user (bisa bocorkan info sensitif)
        die("Koneksi database gagal. Silakan cek konfigurasi di config/database.php");
    }
}
