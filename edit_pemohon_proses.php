<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Pemohon;

$id = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = input_form($_POST['id'] ?? null);
    $nama = input_form($_POST['nama'] ?? null);
    $kelurahan_id = input_form($_POST['kelurahan_id'] ?? null);
    $alamat = input_form($_POST['alamat'] ?? null);
    $pekerjaan = input_form($_POST['pekerjaan'] ?? null);

    $pemohonModel = new Pemohon($pdo);
    $item = $pemohonModel->update([
        'nama' => $nama,
        'kelurahan_id' => $kelurahan_id,
        'alamat' => $alamat,
        'pekerjaan' => $pekerjaan,
        'id' => $id
    ]);

    switch ($item) {
        case 'success':
            $_SESSION['type'] = 'success';
            $_SESSION['message'] = 'Data Berhasil Diedit';

            header('location: pemohon.php');
            die();
            break;
        case 'fail':
            $_SESSION['type'] = 'danger';
            $_SESSION['message'] = 'Data Gagal Diedit';
            break;
        case 'validation':
            $_SESSION['type'] = 'danger';
            $_SESSION['message'] = 'Semua bidang isian wajib diisi';
            break;
    }

    header('location: edit_pemohon.php?id=' . $id);
    die();
}

$_SESSION['type'] = 'danger';
$_SESSION['message'] = 'Terjadi Kesalahan Proses Data';

header('location: edit_pemohon.php?id=' . $id);
die();