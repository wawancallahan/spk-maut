<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Kelurahan;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = input_form($_GET['id'] ?? null);

    $kelurahanModel = new Kelurahan($pdo);
    $item = $kelurahanModel->delete($id);

    switch ($item) {
        case true:
            $_SESSION['type'] = 'success';
            $_SESSION['message'] = 'Data Berhasil Dihapus';

            header('location: kelurahan.php');
            die();
            break;
        case false:
            $_SESSION['type'] = 'danger';
            $_SESSION['message'] = 'Data Gagal Dihapus';
            break;
    }

    header('location: kelurahan.php');
    die();
}

$_SESSION['type'] = 'danger';
$_SESSION['message'] = 'Terjadi Kesalahan Proses Data';

header('location: kelurahan.php');
die();