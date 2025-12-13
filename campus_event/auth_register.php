<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(["error" => "Invalid method"], 405);
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");

if ($name === "" || $email === "" || $password === "") {
    jsonResponse(["error" => "All fields are required"], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(["error" => "Invalid email format"], 400);
}

try {
    global $pdo;

    // Check duplicate email
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        jsonResponse(["error" => "Email already registered"], 400);
    }

    // Insert user
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
    $stmt->execute([$name, $email, $hash]);

    $id = $pdo->lastInsertId();

    $_SESSION["user"] = [
        "id" => $id,
        "name" => $name,
        "email" => $email,
        "role" => "student"
    ];

    jsonResponse(["message" => "Signup successful", "user" => $_SESSION["user"]]);

} catch (Exception $e) {
    jsonResponse(["error" => "Server error"], 500);
}
