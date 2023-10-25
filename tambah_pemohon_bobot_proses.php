<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Pemohon;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pemohon_id = input_form($_POST['pemohon_id'] ?? null);
    $bulan = input_form($_POST['bulan'] ?? null);
    $kriteria = $_POST['kriteria'] ?? null;

    $pemohonModel = new Pemohon($pdo);
    $item = $pemohonModel->createAlternatifBobot([
        'pemohon_id' => $pemohon_id,
        'bulan' => $bulan,
        'kriteria' => $kriteria,
    ]);

    switch ($item) {
        case 'success':
            $_SESSION['type'] = 'success';
            $_SESSION['message'] = 'Data Berhasil Ditambah';

            header('location: tambah_pemohon_bobot.php?id=' . $pemohon_id);
            die();
            break;
        case 'fail':
            $_SESSION['type'] = 'danger';
            $_SESSION['message'] = 'Data Gagal Ditambah';
            break;
        case 'validation':
            $_SESSION['type'] = 'danger';
            $_SESSION['message'] = 'Semua bidang isian wajib diisi';
            break;
    }

    header('location: tambah_pemohon_bobot.php?id=' . $pemohon_id);
    die();
}

$_SESSION['type'] = 'danger';
$_SESSION['message'] = 'Terjadi Kesalahan Proses Data';

header('location: pemohon.php');
die();