<?php
require_once __DIR__ . '/../includes/functions.php';

// Hapus semua data session lalu hancurkan session-nya
$_SESSION = [];
session_destroy();

redirect('/auth/login.php');
