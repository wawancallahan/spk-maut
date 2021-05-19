<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Hasil;

$kelurahan_id = input_form($_GET['id'] ?? null);

if ($kelurahan_id !== "") {
    $hasilModel = new Hasil($pdo);
    $hasilModel->delete($kelurahan_id);
}

$_SESSION['type'] = 'success';
$_SESSION['message'] = 'Data Berhasil Dihapus';

header('location: hasil_perhitungan.php?id=' . $kelurahan_id);
die();