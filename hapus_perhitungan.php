<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Hasil;

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : null;

if ($bulan === null) {
   
    $_SESSION['type'] = 'success';
    $_SESSION['message'] = 'Data Gagal Dihapus';

    header('location: hasil_perhitungan.php');

    exit;
}

$hasilModel = new Hasil($pdo);
$hasilModel->delete($bulan);

$_SESSION['type'] = 'success';
$_SESSION['message'] = 'Data Berhasil Dihapus';

header('location: hasil_perhitungan.php');
die();