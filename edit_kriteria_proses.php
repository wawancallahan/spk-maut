<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Kriteria;

$id = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = input_form($_POST['id'] ?? null);
    $nama = input_form($_POST['nama'] ?? null);
    $bobot = input_form($_POST['bobot'] ?? null);
    $sub = input_form($_POST['sub'] ?? null);

    $kriteriaModel = new Kriteria($pdo);
    $item = $kriteriaModel->update([
        'nama' => $nama,
        'bobot' => $bobot,
        'sub' => $sub,
        'id' => $id
    ]);

    switch ($item) {
        case 'success':
            $_SESSION['type'] = 'success';
            $_SESSION['message'] = 'Data Berhasil Diedit';

            header('location: kriteria.php');
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

    header('location: edit_kriteria.php?id=' . $id);
    die();
}

$_SESSION['type'] = 'danger';
$_SESSION['message'] = 'Terjadi Kesalahan Proses Data';

header('location: edit_kriteria.php?id=' . $id);
die();