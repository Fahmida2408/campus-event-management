<?php
require_once "config.php";

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    jsonResponse(["error" => "Forbidden"], 403);
}

$usersStmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 50");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

$eventsStmt = $pdo->query("SELECT id, title, event_date, category FROM events ORDER BY event_date DESC LIMIT 50");
$events = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);

jsonResponse([
    "users"  => $users,
    "events" => $events
]);
