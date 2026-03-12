<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");

// Pobierz dane ze zmiennych środowiskowych (ustawionych w Render)
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB_ERROR");
}

$key = isset($_GET['key']) ? $_GET['key'] : '';
$hwid = isset($_GET['hwid']) ? $_GET['hwid'] : '';

if (empty($key)) {
    die("NO_KEY");
}

// Sprawdź czy klucz istnieje
$stmt = $pdo->prepare("SELECT * FROM licenses WHERE license_key = ?");
$stmt->execute([$key]);
$license = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$license) {
    die("INVALID");
}

if ($license['used'] == 0) {
    // Klucz nieużywany - aktywuj
    $stmt = $pdo->prepare("UPDATE licenses SET used = 1, machine_id = ?, activated_at = NOW() WHERE license_key = ?");
    $stmt->execute([$hwid, $key]);
    die("ACTIVATED");
} else {
    // Klucz już używany - sprawdź czy ten sam komputer
    if ($license['machine_id'] == $hwid) {
        die("VALID");
    } else {
        die("USED");
    }
}

?>
