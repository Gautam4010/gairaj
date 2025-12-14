<?php
// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Redirect to login if not authenticated
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

// Redirect to profile if already logged in
function redirect_if_logged_in() {
    if (is_logged_in()) {
        header('Location: profile.php');
        exit();
    }
}
?>