<?php
/**
 * Central authorization gate for the legacy administration area.
 * Include this file before any HTML or output in every protected admin page.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

if (!isset($_SESSION['adm_id']) || !ctype_digit((string) $_SESSION['adm_id']) || (int) $_SESSION['adm_id'] < 1) {
    http_response_code(401);
    header('Location: index.php');
    exit;
}
