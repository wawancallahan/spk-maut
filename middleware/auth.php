<?php

function authCheck() {
    $is_login = $_SESSION['is_login'] ?? 0;
    $role = $_SESSION['role'] ?? null;

    return $is_login === 1 || $role !== null;
}

function authRole($role) {
    $is_login = $_SESSION['is_login'] ?? 0;
    $role = $_SESSION['role'] ?? null;

    return $is_login === 1 && $role == $role;
}