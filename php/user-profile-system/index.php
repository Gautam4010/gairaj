<?php
session_start();

// Redirect to login if not logged in, otherwise to profile
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
} else {
    header('Location: login.php');
}
exit();