<?php

if (session_status() !== 2) {
    session_start();
}

function reset_session_flash() {
    unset(
        $_SESSION['type'],
        $_SESSION['message']
    );
}