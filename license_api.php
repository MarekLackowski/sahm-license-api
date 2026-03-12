<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");

// 🔴 TWOJE DANE
$gistId = "541cefcb68e9e3d5204ba9ba3a8ca350"; // ID z URL (to po gist.githubusercontent.com/.../)
$filename = "licenses.json";
$token = getenv('GITHUB_TOKEN');

$key = isset($_GET['key']) ? $_GET['key'] : '';
$hwid = isset($_GET['hwid']) ? $_GET['hwid'] : '';

if (empty($key)) {
    die("NO_KEY");
}

// Pobierz aktualną zawartość Gista przez API
$ch = curl_init("https://api.github.com/gists/$gistId");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "PHP-Script");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: token $token",
    "Accept: application/vnd.github.v3+json"
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    die("GIST_ERROR");
}

$gistData = json_decode($response, true);
$currentContent = $gistData['files'][$filename]['content'];
$licenses = json_decode($currentContent, true);

// Sprawdź klucz
if (!isset($licenses[$key])) {
    die("INVALID");
}

$license = $licenses[$key];

if (!$license['used']) {
    // Aktywuj – zmień dane
    $licenses[$key]['used'] = true;
    $licenses[$key]['machine_id'] = $hwid;
    
    // Zapisz z powrotem do Gista
    $newContent = json_encode($licenses, JSON_PRETTY_PRINT);
    
    $updateData = json_encode([
        "files" => [
            $filename => [
                "content" => $newContent
            ]
        ]
    ]);
    
    $ch = curl_init("https://api.github.com/gists/$gistId");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $updateData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "PHP-Script");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: token $token",
        "Accept: application/vnd.github.v3+json",
        "Content-Type: application/json"
    ]);
    curl_exec($ch);
    curl_close($ch);
    
    die("ACTIVATED");
} else {
    if ($license['machine_id'] == $hwid) {
        die("VALID");
    } else {
        die("USED");
    }
}
?>
