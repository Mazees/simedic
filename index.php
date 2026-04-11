<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require './models/user.php';
if ($_SESSION['is_logged_in'] === true) {
    header("Location: ./dashboard");
} else {
    header("Location: ./login");
}
?>