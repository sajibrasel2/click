<?php
$DB_HOST = 'localhost';
$DB_NAME = 'techandc_click';
$DB_USER = 'techandc_bot';
$DB_PASS = '12345Sajibs6@';

function dbConnect(bool $useDb = true)
{
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
    $dsn = "mysql:host=$DB_HOST;charset=utf8mb4";
    if ($useDb) {
        $dsn .= ";dbname=$DB_NAME";
    }

    return new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

session_start();

function is_logged_in()
{
    return !empty($_SESSION['user']);
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

function current_user()
{
    return $_SESSION['user'] ?? null;
}
