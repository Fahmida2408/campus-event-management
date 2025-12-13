<?php
// auth_me.php
require_once "config.php";

if (isset($_SESSION['user'])) {
    jsonResponse(["user" => $_SESSION['user']]);
} else {
    jsonResponse(["user" => null]);
}
?>
