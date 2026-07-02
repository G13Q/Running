<?php

$env = parse_ini_file(__DIR__ . "/../.env");
$hostname = $env["DB_HOST"] ?? "localhost";
$port = $env["DB_PORT"] ?? "3306";
$dbname = $env["DB_NAME"] ?? "runningdb";
$username = $env["DB_USER"] ?? "root";
$password = $env["DB_PASS"] ?? "";


try {
    $dsn = "mysql:host=$hostname;port=$port;dbname=$dbname";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    exit("erreur : " . $e->getMessage());
}
