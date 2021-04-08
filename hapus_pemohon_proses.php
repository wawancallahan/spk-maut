<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Pemohon;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = input_form($_GET['id'] ?? null);

    $pemohonModel = new Pemohon($pdo);
    $item = $pemohonModel->deleteBobot($id);
    if ($item) {
        $item = $pemohonModel->delete($id);
    }

    switch ($item) {
        case true:
            $_SESSION['type'] = 'success';
            $_SESSION['message'] = 'Data Berhasil Dihapus';

            header('location: pemohon.php');
            die();
            break;
        case false:
            $_SESSION['type'] = 'danger';
            $_SESSION['message'] = 'Data Gagal Dihapus';
            break;
    }

    header('location: pemohon.php');
    die();
}

$_SESSION['type'] = 'danger';
$_SESSION['message'] = 'Terjadi Kesalahan Proses Data';

header('location: pemohon.php');
die();