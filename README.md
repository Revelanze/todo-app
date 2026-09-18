# Manajemen Tugas Kelas (To-Do App)

**Tugas Remedial** — Mata Kuliah Pengembangan Web Sisi Server (TI253305)

|---|---|
| **Nama** | Sang Putu Adistya Deva Yastra |
| **NIM** | 240040020 |


Project back-end web application dengan tema bebas: **aplikasi manajemen tugas kelas (to-do list)**,
dibuat dengan **PHP native + MySQL (PDO)** tanpa framework.

---

## 1. Daftar Isi

1. [Pemetaan ke Soal (CPMK)](#2-pemetaan-ke-soal-cpmk)
2. [Struktur Folder](#3-struktur-folder)
3. [Tutorial Instalasi & Menjalankan Project](#4-tutorial-instalasi--menjalankan-project)
4. [Tutorial Push ke GitHub](#5-tutorial-push-ke-github)
5. [Troubleshooting Umum](#6-troubleshooting-umum)

---

## 2. Struktur Folder

```
todo-app/
├── auth/
│   ├── register.php      # Create user (daftar akun)
│   ├── login.php         # Read user + set session login
│   └── logout.php        # Hapus session
├── tasks/
│   ├── index.php         # Read (list) semua tugas milik user yang login
│   ├── create.php        # Create tugas baru
│   ├── edit.php          # Update tugas
│   └── delete.php        # Delete tugas
├── config/
│   ├── database.php      # Koneksi PDO ke MySQL
│   └── config.php        # Konstanta BASE_URL (WAJIB disesuaikan, lihat langkah 3 di bawah)
├── includes/
│   └── functions.php     # sanitasi, validasi, csrf, auth guard, helper redirect/asset
├── assets/
│   └── style.css         # Styling halaman
├── vendor/                # Dibuat otomatis oleh Composer (TIDAK di-commit ke git)
├── schema.sql             # Skema database (2 tabel: users & tasks)
├── composer.json          # Daftar dependency (library Carbon)
├── composer.lock          # Versi pasti dependency yang terinstall
├── index.php              # Entry point, redirect ke login/daftar tugas
└── .gitignore              # File/folder yang sengaja tidak di-commit (vendor/, .env)
```

---

## 3. Instalasi & Menjalankan Project

### Langkah 1 — Siapkan XAMPP

1. Install [XAMPP](https://www.apachefriends.org/) kalau belum ada.
2. Buka **XAMPP Control Panel**, klik **Start** pada **Apache** dan **MySQL**.

### Langkah 2 — Taruh Project di htdocs

1. Extract/clone project ini ke `C:\xampp\htdocs\todo-app` (nama folder harus persis `todo-app`, atau sesuaikan dengan langkah 3 di bawah kalau namanya beda).
2. Pastikan strukturnya `C:\xampp\htdocs\todo-app\index.php` langsung (bukan `C:\xampp\htdocs\todo-app\todo-app\index.php`).

### Langkah 3 — Atur BASE_URL

Buka `config/config.php`, sesuaikan nilainya:

```php
// Kalau project di subfolder htdocs/todo-app/ (kasus paling umum):
define('BASE_URL', '/todo-app');

// Kalau project ditaruh LANGSUNG di htdocs (akses lewat http://localhost/ saja):
define('BASE_URL', '');
```

> Ini penting — kalau BASE_URL tidak sesuai nama folder, redirect antar halaman (misalnya setelah login) akan menghasilkan error **404 Not Found**.

### Langkah 4 — Import Database

1. Buka `http://localhost/phpmyadmin`.
2. Klik tab **Import**, pilih file `schema.sql`, klik **Go**. Ini otomatis membuat database `todo_kelas` beserta tabel `users` dan `tasks`.
3. Kalau username/password MySQL kamu berbeda dari default XAMPP (`root` tanpa password), sesuaikan juga di `config/database.php`.

### Langkah 5 — Install Library Eksternal (Composer)

1. Kalau belum ada Composer, download dan install dari [getcomposer.org](https://getcomposer.org/download/).
2. Buka terminal (Git Bash / CMD), masuk ke folder project:
   ```bash
   cd C:\xampp\htdocs\todo-app
   ```
3. Jalankan:
   ```bash
   composer install
   ```
   Ini otomatis membuat folder `vendor/` berisi library **Carbon**.

### Langkah 6 — Jalankan Aplikasi

Buka browser, akses:
```
http://localhost/todo-app/
```
Daftar akun baru di halaman **Register**, login, lalu mulai kelola tugas kelas kamu (tambah, edit, ubah status, hapus).

---

## 4. Push ke GitHub

Riwayat commit yang rapi (bertahap per fitur) jadi bukti pengelolaan versi dengan Git, bukan asal `git add .` sekali lalu commit semuanya.

### Langkah 1 — Buat Repository Kosong di GitHub

Buka github.com → tombol **+** → **New repository** → beri nama (misal `todo-app`) → **jangan** centang "Add a README file" / ".gitignore" / "license" supaya repo benar-benar kosong → **Create repository**. Salin link HTTPS-nya.

### Langkah 2 — Buka Terminal di Komputer Sendiri

Gunakan **Git Bash** (bukan terminal dari GitHub Codespaces/Dev Container, karena itu berjalan di server terpisah dan tidak bisa mengakses folder di komputer lokal). Masuk ke folder project:

```bash
cd /c/xampp/htdocs/todo-app
```

### Langkah 3 — Inisialisasi Git & Set Identitas

```bash
git init
git config --global user.name "Sang Putu Adistya Deva Yastra"
git config --global user.email "email_github_kamu@gmail.com"
```

### Langkah 4 — Commit Bertahap per Fitur

```bash
git add schema.sql config/ includes/functions.php
git commit -m "feat: setup database schema dan koneksi PDO"

git add auth/
git commit -m "feat: implementasi register dan login user"

git add tasks/create.php tasks/index.php
git commit -m "feat: create dan read data tugas"

git add tasks/edit.php tasks/delete.php
git commit -m "feat: update dan delete data tugas"

git add composer.json composer.lock
git commit -m "feat: tambah library carbon untuk hitung sisa deadline"

git add assets/ README.md .gitignore index.php
git commit -m "docs: tambah styling dan dokumentasi"
```

Cek riwayatnya dengan:
```bash
git log --oneline
```

### Langkah 5 — Hubungkan ke GitHub & Push

```bash
git branch -M main
git remote add origin https://github.com/USERNAME/todo-app.git
git push -u origin main
```

Ganti `USERNAME` dengan username GitHub kamu, dan link-nya sesuai repo yang dibuat di Langkah 1. Kalau diminta login, ikuti instruksi autentikasi via browser yang muncul otomatis.

### Langkah 6 — Verifikasi

Refresh halaman repo di GitHub. Pastikan:
- Semua file/folder muncul (index.php, auth/, tasks/, dst)
- Folder `vendor/` **tidak ikut** ter-upload (karena sudah masuk `.gitignore`)
- Tab **Commits** menampilkan riwayat commit bertahap seperti Langkah 4

---

## 5. Troubleshooting Umum

| Gejala | Penyebab & Solusi |
|---|---|
| **404 Not Found** saat buka `localhost/todo-app/` | Cek folder ada persis di `htdocs/todo-app/` (bukan `htdocs/todo-app/todo-app/`), dan `BASE_URL` di `config/config.php` sudah sesuai nama folder. |
| **Fatal error: Failed opening required '.../vendor/autoload.php'** | Composer belum dijalankan. Masuk ke folder project di terminal, jalankan `composer install`. |
| **`cd`: No such file or directory** di Git Bash | Git Bash pakai format path Unix, bukan Windows. Gunakan `cd /c/xampp/htdocs/todo-app`, bukan `cd C:\xampp\htdocs\todo-app`. |
| Commit sudah dibuat tapi tidak muncul di GitHub | Commit (`git commit`) hanya tersimpan lokal di komputer. Harus dilanjutkan dengan `git remote add origin ...` dan `git push` supaya terkirim ke GitHub. |
| Koneksi database gagal | Cek MySQL sudah **Start** di XAMPP Control Panel, dan kredensial di `config/database.php` sesuai (default XAMPP: user `root`, password kosong). |

---
