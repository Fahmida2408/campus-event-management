<?php
// rsvp.php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Invalid method"], 405);
}

requireLogin();
$user = currentUser();

$event_id = (int) ($_POST['event_id'] ?? 0);
$status = $_POST['status'] ?? 'going';

if ($event_id <= 0 || !in_array($status, ['going','cancelled'])) {
    jsonResponse(["error" => "Invalid input"], 400);
}

try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    if (!$stmt->fetch()) {
        jsonResponse(["error" => "Event not found"], 404);
    }

    $stmt = $pdo->prepare("INSERT INTO rsvps (user_id, event_id, status)
                           VALUES (?, ?, ?)
                           ON DUPLICATE KEY UPDATE status = VALUES(status)");
    $stmt->execute([$user['id'], $event_id, $status]);

    jsonResponse(["message" => "RSVP updated"]);
} catch (Exception $e) {
    jsonResponse(["error" => "Server error"], 500);
}
?>
