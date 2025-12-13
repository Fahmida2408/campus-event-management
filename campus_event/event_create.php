<?php
session_start();
require "config.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  echo json_encode(["error" => "Invalid request"]);
  exit;
}

if (!isset($_SESSION["user"])) {
  echo json_encode(["error" => "Not logged in"]);
  exit;
}

$user = $_SESSION["user"];

if ($user["role"] !== "organizer" && $user["role"] !== "admin") {
  echo json_encode(["error" => "Unauthorized"]);
  exit;
}

$title = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$location = trim($_POST["location"] ?? "");
$event_date = $_POST["event_date"] ?? "";

if ($title === "" || $event_date === "") {
  echo json_encode(["error" => "Title and date are required"]);
  exit;
}

$stmt = $pdo->prepare("
  INSERT INTO events (title, description, location, event_date, created_by)
  VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
  $title,
  $description,
  $location,
  $event_date,
  $user["id"]
]);

echo json_encode(["success" => true]);
