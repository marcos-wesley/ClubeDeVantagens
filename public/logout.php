<?php
session_start();
require_once '../includes/auth.php';

// Logout user
logoutUser();

// Redirect to homepage
header('Location: ../index.php');
exit;
?>