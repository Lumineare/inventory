<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Role checking function
function checkRole($allowedRoles) {
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        header('Location: ../dashboard.php');
        exit();
    }
}
?>