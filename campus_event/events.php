<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    jsonResponse(["error" => "Invalid method"], 405);
}

$search   = trim($_GET["search"]   ?? "");
$category = trim($_GET["category"] ?? "");

$sql  = "SELECT e.*, u.name AS organizer_name
         FROM events e
         LEFT JOIN users u ON e.created_by = u.id
         WHERE 1 ";
$params = [];

if ($search !== "") {
    $sql .= " AND (e.title LIKE ? OR e.description LIKE ? OR e.location LIKE ?) ";
    $like = "%".$search."%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($category !== "" && $category !== "All") {
    $sql .= " AND e.category = ? ";
    $params[] = $category;
}

$sql .= " ORDER BY e.event_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

jsonResponse(["events" => $events]);
