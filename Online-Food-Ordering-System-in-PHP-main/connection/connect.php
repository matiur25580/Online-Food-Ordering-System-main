<?php
/*
 * Shared MySQL connection for the customer and administration areas.
 *
 * Local XAMPP defaults are retained for this legacy academic project.
 * Set DB_HOST, DB_USER, DB_PASSWORD and DB_NAME in the PHP process environment
 * when deploying; never use the local root/blank-password defaults in production.
 */

$servername = getenv('DB_HOST') ?: '127.0.0.1';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD');
$password = $password === false ? '' : $password;
$dbname = getenv('DB_NAME') ?: 'code_camp_bd_fos';

// Keep database diagnostics in server logs, not in the browser response.
mysqli_report(MYSQLI_REPORT_OFF);
$db = @mysqli_connect($servername, $username, $password, $dbname);

if (!$db) {
    error_log('Food ordering database connection failed: ' . mysqli_connect_error());
    http_response_code(500);
    exit('The application is temporarily unable to connect to its database.');
}

if (!mysqli_set_charset($db, 'utf8mb4')) {
    error_log('Food ordering database charset setup failed: ' . mysqli_error($db));
    mysqli_close($db);
    http_response_code(500);
    exit('The application could not initialize its database connection.');
}
