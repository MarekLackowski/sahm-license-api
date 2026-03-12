<?php
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');

echo "Próba połączenia z bazą...\n";
echo "Host: $db_host\n";
echo "DB: $db_name\n";
echo "User: $db_user\n";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "SUKCES! Połączono z bazą.";
} catch (PDOException $e) {
    echo "BŁĄD: " . $e->getMessage();
}
?>