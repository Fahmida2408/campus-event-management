<?php
// config.php
session_start();

$host = "localhost";
$dbname = "campus_events_db";
$user = "root";      // change if your MySQL user is different
$pass = "";          // change if your MySQL password is not empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

header("Content-Type: application/json");

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function requireLogin() {
    if (!isset($_SESSION['user'])) {
        jsonResponse(["error" => "Not authenticated"], 401);
    }
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}
?>
