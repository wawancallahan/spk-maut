<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/middleware/hasAuth.php';

use Models\Pemohon;
use Models\Kriteria;
use Models\Hasil;

$resultDataView = '';

$hasilModel = new Hasil($pdo);
$kriteriaModel = new Kriteria($pdo);
$pemohonModel = new Pemohon($pdo);

$kriteriaItems = $kriteriaModel->index();
$pemohonItems = $pemohonModel->getAlternatifAndBobot();

// Perhitungan Moora

$totalBobotPemohonKriteria = [];

foreach ($kriteriaItems as $kriteriaItem) {
    $totalBobotPemohonKriteria[$kriteriaItem['id']] = sqrt(array_reduce($pemohonItems, function ($output, $item) use ($kriteriaItem) {
        $output += pow($item['bobot'][$kriteriaItem['id']]['nilai'], 2);

        return $output;
    }, 0));
}

$kriteriaBobot = [];
$kriteriaStatus = [];

foreach ($kriteriaItems as $kriteriaItem) {
    $kriteriaBobot[$kriteriaItem['id']] = number_format($kriteriaItem['bobot'], 2);
    $kriteriaStatus[$kriteriaItem['id']] = $kriteriaItem['status'];
}

$pemohonItems = array_map(function ($pemohonItem) use ($kriteriaItems, $totalBobotPemohonKriteria) {
    $pemohonItem['bobot'] = array_map(function ($pemohonItemBobot) use ($totalBobotPemohonKriteria ) {
        $pemohonItemBobot['nilai_normalisasi'] = number_format($pemohonItemBobot['nilai'] / $totalBobotPemohonKriteria[$pemohonItemBobot['kriteria_id']], 4);

        return $pemohonItemBobot;
    }, $pemohonItem['bobot']);

    return $pemohonItem;
}, $pemohonItems);

$pemohonItems = array_map(function ($pemohonItem) use ($kriteriaBobot) {
    $pemohonItem['bobot'] = array_map(function ($pemohonItemBobot) use ($kriteriaBobot) {
        $pemohonItemBobot['nilai_normalisasi_kriteria'] = number_format(
            $pemohonItemBobot['nilai_normalisasi'] * $kriteriaBobot[$pemohonItemBobot['kriteria_id']]
        , 4);

        return $pemohonItemBobot;
    }, $pemohonItem['bobot']);

    return $pemohonItem;
}, $pemohonItems);

$pemohonItems = array_map(function ($pemohonItem) use ($kriteriaStatus) {
    $max = $min = 0;
    foreach ($pemohonItem['bobot'] as $pemohonBobot) {
        if ($kriteriaStatus[$pemohonBobot['kriteria_id']] == 'benefit') {
            $max += $pemohonBobot['nilai_normalisasi_kriteria'];
        } else {
            $min += $pemohonBobot['nilai_normalisasi_kriteria'];
        }
    }

    $pemohonItem['nilai_akhir'] = number_format($max - $min, 4);

    return $pemohonItem;
}, $pemohonItems);

$pemohonHasilItems = [];
foreach ($pemohonItems as $pemohonItem) {
    $pemohonHasilItems[$pemohonItem['id']] = $pemohonItem['nilai_akhir'];
}	

arsort($pemohonHasilItems);

$hasilModel->delete();

$no = 1;
foreach ($pemohonHasilItems as $pemohonId => $pemohonHasilItem) {

    $nilai = json_encode([
        'nilai_akhir' => input_form($pemohonHasilItem),
    ]);

    $hasilModel->create([
        'alternatif_id' => input_form($pemohonId),
        'no' => input_form($no),
        'nilai' => $nilai
    ]);

    $no++;
}

$hasilItems = $hasilModel->index();
$bobotAlternatifItems = $pemohonModel->getBobotIn(array_column($hasilItems, 'alternatif_id'));

$hasilItems = array_map(function ($item) use ($bobotAlternatifItems) {
    $item['bobot'] = array_filter($bobotAlternatifItems, function ($bobot) use ($item) {
        return $item['alternatif_id'] == $bobot['alternatif_id'];
    });

    return $item;
}, $hasilItems);

foreach ($hasilItems as $index => $hasilItem) {

    $nilai = json_decode($hasilItem['nilai'], true);
    $bobot = array_values($hasilItem['bobot']);

    $bobotKriteria = "";

    foreach ($kriteriaItems as $kriteriaItem) {
        $bobotKey = array_search($kriteriaItem['id'], array_column($bobot, 'kriteria_id'));
        $bobotKriteria .= '<td>' . ($bobotKey !== false ? $bobot[$bobotKey]['bobot'] : null) . '</td>';
    }

    $resultDataView .= '<tr>' . 
                            '<td>' . ($index + 1) . '</td>' . 
                            '<td>' . $hasilItem['nama'] . '</td>' . 
                            $bobotKriteria .
                            '<td>' . $nilai['nilai_akhir'] . '</td>' . 
                            '<td>' . $hasilItem['no'] . '</td>' .
                        '</tr>';
}

echo json_encode([
    'result_data_view' => $resultDataView,
]);