<?php
session_start();

function checkAuthentication($redirectToIfAuthenticated = 'index.php', $redirectToIfNotAuthenticated = './auth/login.php') {
    if (isset($_SESSION['user_id'])) {
        header("Location: $redirectToIfAuthenticated");
        exit;
    } else {
        header("Location: $redirectToIfNotAuthenticated");
        exit;
    }
}