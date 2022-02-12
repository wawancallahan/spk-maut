<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Hasil;

$hasilModel = new Hasil($pdo);
$hasilModel->delete();

$_SESSION['type'] = 'success';
$_SESSION['message'] = 'Data Berhasil Dihapus';

header('location: hasil_perhitungan.php');
die();