<?php
require_once "config.php";

$id = $_GET["id"] ?? 0;

$stmt = $pdo->prepare("SELECT e.*, u.name AS organizer_name
                       FROM events e
                       LEFT JOIN users u ON e.created_by = u.id
                       WHERE e.id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    jsonResponse(["error" => "Event not found"], 404);
}

jsonResponse(["event" => $event]);
