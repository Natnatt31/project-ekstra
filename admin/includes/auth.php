<?php
// ==========================================
// Auth Middleware — Cek session admin
// ==========================================
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php#login');
    exit;
}
