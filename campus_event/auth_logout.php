<?php
// auth_logout.php
require_once "config.php";

session_unset();
session_destroy();

jsonResponse(["message" => "Logged out"]);
?>
