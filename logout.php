<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/vendor/autoload.php';

session_destroy();

header('location: index.php');
die();