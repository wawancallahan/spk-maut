<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Pemohon;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = input_form($_POST['nama'] ?? null);
    $kelurahan_id = input_form($_POST['kelurahan_id'] ?? null);
    $alamat = input_form($_POST['alamat'] ?? null);
    $pekerjaan = input_form($_POST['pekerjaan'] ?? null);
    $kriteria = $_POST['kriteria'] ?? null;

    $newFileName = null;

    if ($_FILES['foto']['size'] !== 0) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = $_FILES['foto']['size'];
        $fileType = $_FILES['foto']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if ( ! in_array($fileExtension, $allowedfileExtensions)) {
            $_SESSION['type'] = 'danger';
            $_SESSION['message'] = 'Ekstensi File Hanya Boleh .jpg, .jpeg, .gif, .png';
            
            header('location: tambah_pemohon.php');
            die();
        }
    
        // directory in which the uploaded file will be moved
        $uploadFileDir = './files/';
    
        if (!is_dir($uploadFileDir)) {
            # jika tidak maka folder harus dibuat terlebih dahulu
            mkdir($uploadFileDir, 0777, $rekursif = true);
        }
    
        $dest_path = $uploadFileDir . $newFileName;
    
        if( ! move_uploaded_file($fileTmpPath, $dest_path)) {
            $_SESSION['type'] = 'danger';
            $_SESSION['message'] = 'Gagal Upload File';
            
            header('location: tambah_pemohon.php');
            die();
        }
    }

    $pemohonModel = new Pemohon($pdo);
    $item = $pemohonModel->create([
        'nama' => $nama,
        'kelurahan_id' => $kelurahan_id,
        'alamat' => $alamat,
        'pekerjaan' => $pekerjaan,
        'kriteria' => $kriteria,
        'file' => $newFileName
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