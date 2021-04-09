<?php

$is_login = $_SESSION['is_login'] ?? 0;

if ($is_login == 0) {
    header('location: index.php');
    die();
}
