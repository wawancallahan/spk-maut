<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;
use Models\Hasil;
use Models\Kelurahan;

// instantiate and use the dompdf class
$kelurahan_id = input_form($_GET['id'] ?? null);

if ($kelurahan_id !== "") {
    $dompdf = new Dompdf();
    $kelurahanModel = new Kelurahan($pdo);
    $hasilModel = new Hasil($pdo);

    $item = $kelurahanModel->find($kelurahan_id);
    $hasilItems = $hasilModel->index($kelurahan_id);

    ob_start();

    extract([
        'hasilItems' => $hasilItems,
        'item' => $item
    ]);

    include 'hasil_download_template.php';

    $view = ob_get_clean();

    $dompdf->loadHtml($view);

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream();
} else {
    echo 'Terjadi Kesalahan Export PDF';
    die();
}