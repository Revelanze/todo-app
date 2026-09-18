-- =========================================================
-- SCHEMA DATABASE: todo_kelas
-- Memenuhi CPMK091: skema relasional minimal 2 tabel
-- =========================================================

CREATE DATABASE IF NOT EXISTS todo_kelas;
USE todo_kelas;

-- Tabel 1: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- disimpan sebagai hash (bcrypt), bukan plaintext
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel 2: tasks (relasi many-to-one ke users)
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    deadline DATE DEFAULT NULL,
    status ENUM('belum','proses','selesai') NOT NULL DEFAULT 'belum',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

-- Index tambahan untuk mempercepat query task per user
CREATE INDEX idx_tasks_user_id ON tasks(user_id);
