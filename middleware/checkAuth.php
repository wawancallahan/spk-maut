<?php

$is_login = $_SESSION['is_login'] ?? 0;

if ($is_login == 1) {
    header('location: dashboard.php');
    die();
}
