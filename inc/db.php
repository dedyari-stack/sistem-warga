<?php
$dbConfig = [
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'sistem_warga',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];

$pdo = null;
$dbAvailable = false;
$dbError = null;

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['database'],
        $dbConfig['charset']
    );

    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $dbAvailable = true;
} catch (PDOException $exception) {
    $dbError = $exception->getMessage();
}
?>
