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

if (empty($_SESSION['admin_csrf_token'])) {
    $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
}

/** Reject state-changing admin requests without a same-session CSRF token. */
function admin_require_csrf_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Method not allowed.');
    }
    $submitted = $_POST['csrf_token'] ?? '';
    $expected = $_SESSION['admin_csrf_token'] ?? '';
    if (!is_string($submitted) || !is_string($expected) || $expected === '' || !hash_equals($expected, $submitted)) {
        http_response_code(403);
        exit('Invalid security token. Please refresh and try again.');
    }
}

function admin_csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['admin_csrf_token'], ENT_QUOTES, 'UTF-8') . '">';
}
