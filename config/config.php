<?php
/**
 * config/config.php
 *
 * PENTING: sesuaikan BASE_URL dengan lokasi project kamu.
 *
 * - Kalau project ditaruh LANGSUNG di htdocs (mis. C:\xampp\htdocs\index.php),
 *   akses via http://localhost/  -> isi BASE_URL dengan string kosong: ''
 *
 * - Kalau project ditaruh di SUBFOLDER (mis. C:\xampp\htdocs\todo-app\index.php),
 *   akses via http://localhost/todo-app/  -> isi BASE_URL dengan: '/todo-app'
 *   (nama subfolder harus SAMA PERSIS dengan nama folder di htdocs)
 */

define('BASE_URL', '/todo-app');
