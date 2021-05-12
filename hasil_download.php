<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;
use Models\Hasil;
use Models\Kelurahan;
use Models\Kriteria;
use Models\Pemohon;

// instantiate and use the dompdf class
$kelurahan_id = input_form($_GET['id'] ?? null);

if ($kelurahan_id !== "") {
    $dompdf = new Dompdf();
    $kelurahanModel = new Kelurahan($pdo);
    $hasilModel = new Hasil($pdo);
    $pemohonModel = new Pemohon($pdo);
    $kriteriaModel = new Kriteria($pdo);

    $item = $kelurahanModel->find($kelurahan_id);
    $hasilItems = $hasilModel->index($kelurahan_id);
    $bobotAlternatifItems = $pemohonModel->getBobotIn(array_column($hasilItems, 'alternatif_id'));

    $hasilItems = array_map(function ($item) use ($bobotAlternatifItems) {
        $item['bobot'] = array_filter($bobotAlternatifItems, function ($bobot) use ($item) {
            return $item['alternatif_id'] == $bobot['alternatif_id'];
        });

        return $item;
    }, $hasilItems);

    $kriteriaItems = $kriteriaModel->index();

    ob_start();

    extract([
        'hasilItems' => $hasilItems,
        'item' => $item,
        'kriteriaItems' => $kriteriaItems
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