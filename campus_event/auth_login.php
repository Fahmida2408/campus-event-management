<?php
ini_set('display_errors',1);
error_reporting(E_ALL);


// auth_login.php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Invalid method"], 405);
}

$input = $_POST;
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
    jsonResponse(["error" => "Email and password required"], 400);
}

try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(["error" => "Invalid credentials"], 401);
    }

    unset($user['password']);
    $_SESSION['user'] = $user;

    jsonResponse(["message" => "Login successful", "user" => $user]);
} catch (Exception $e) {
    jsonResponse(["error" => "Server error"], 500);
}
?>
