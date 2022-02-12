<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Pemohon;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = input_form($_POST['nama'] ?? null);
    $alamat = input_form($_POST['alamat'] ?? null);
    $pekerjaan = input_form($_POST['pekerjaan'] ?? null);
    $kriteria = $_POST['kriteria'] ?? null;

    $pemohonModel = new Pemohon($pdo);
    $item = $pemohonModel->create([
        'nama' => $nama,
        'alamat' => $alamat,
        'pekerjaan' => $pekerjaan,
        'kriteria' => $kriteria,
        'file' => null
    ]);

    switch ($item) {
        case 'success':
            $_SESSION['type'] = 'success';
            $_SESSION['message'] = 'Data Berhasil Ditambah';

            header('location: pemohon.php');
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

    header('location: tambah_pemohon.php');
    die();
}

$_SESSION['type'] = 'danger';
$_SESSION['message'] = 'Terjadi Kesalahan Proses Data';

header('location: tambah_pemohon.php');
die();