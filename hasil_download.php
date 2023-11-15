<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;
use Models\Hasil;
use Models\Kriteria;
use Models\Pemohon;

$dompdf = new Dompdf();
$hasilModel = new Hasil($pdo);
$pemohonModel = new Pemohon($pdo);
$kriteriaModel = new Kriteria($pdo);

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : null;

if ($bulan === null) {
    echo "Data tidak ditemukan";

    exit;
}

$hasilItems = $hasilModel->index($bulan);
$bobotAlternatifItems = $pemohonModel->getBobotIn(array_column($hasilItems, 'alternatif_id'), $bulan);

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
    'kriteriaItems' => $kriteriaItems
]);

include 'hasil_download_template.php';

$view = ob_get_clean();

$dompdf->loadHtml($view);

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();