<?php
// connection.php
// --
$server   = "DESKTOP-R0UAL3E";   // یا "localhost\\SQLEXPRESS"
$database = "Library_dbms_U";

$username = ""; // 
$password = ""; //

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// PDO SQL Server DSN
try {
    $dsn = "sqlsrv:Server=$server;Database=$database";
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // در محیط توسعه پیام خطا را نمایش می‌دهیم
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection error: ' . $e->getMessage()]);
    exit;
}
