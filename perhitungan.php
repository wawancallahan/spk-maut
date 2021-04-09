<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';

use Models\Pemohon;
use Models\Kriteria;
use Models\Hasil;

$kelurahan_id = input_form($_POST['kelurahan_id'] ?? null);

$status = false;
$resultDataView = '';

if ($kelurahan_id !== "") {
    $hasilModel = new Hasil($pdo);
    $kriteriaModel = new Kriteria($pdo);
    $pemohonModel = new Pemohon($pdo);

    $kriteriaItems = $kriteriaModel->index();
    $pemohonItems = $pemohonModel->getAlternatifAndBobot($kelurahan_id);

    // Perhitungan SAW

    // Hitung Total Bobot Kriteria
    $totalKriteriaBobot = array_reduce($kriteriaItems, function ($output, $carry) {
        $output += $carry['bobot'];

        return $output;
    }, 0);

    // Hitung Normalisasi Bobot Kriteria
    $kriteriaItems = array_map(function ($kriteria) use ($totalKriteriaBobot) {
        $kriteria['nilai_normalisasi'] = number_format($kriteria['bobot'] / $totalKriteriaBobot, 2);

        return $kriteria;
    }, $kriteriaItems);

    // Matrik Keputusan
    $kriteriaItems = array_map(function ($kriteria) use ($pemohonItems) {
        $nilai_matrik = 0;

        if ($kriteria['status'] == 'benefit') {
            $nilai_matrik = max(
                array_map(function ($pemohon) use ($kriteria) {
                    return $pemohon['bobot'][$kriteria['id']]['nilai'] ?? 0;
                }, $pemohonItems)
            );
        } else if ($kriteria['status'] == 'cost') {
            $nilai_matrik = min(
                array_map(function ($pemohon) use ($kriteria) {
                    return $pemohon['bobot'][$kriteria['id']]['nilai'] ?? 0;
                }, $pemohonItems)
            );
        }

        $kriteria['nilai_matrik'] = $nilai_matrik;

        return $kriteria;
    }, $kriteriaItems);

    // Normalisasi Id Kriteria
    $kriteriaIdItems = [];

    foreach ($kriteriaItems as $kriteriaItem) {
        $kriteriaIdItems[$kriteriaItem['id']] = $kriteriaItem;
    }

    $kriteriaItems = $kriteriaIdItems;

    // Normalisasi Nilai Bobot dari Matrik Keputusan
    $pemohonItems = array_map(function ($pemohon) use ($kriteriaItems) {
        $bobotItems = array_map(function ($bobot) use ($kriteriaItems) {
            $bobot['nilai_normalisasi'] = $nilai_normalisasi = number_format($bobot['nilai'] / $kriteriaItems[$bobot['kriteria_id']]['nilai_matrik'], 2);

            $bobot['nilai_hasil'] = number_format($nilai_normalisasi * $kriteriaItems[$bobot['kriteria_id']]['nilai_normalisasi'], 2);

            return $bobot;
        }, $pemohon['bobot']);

        $total = array_reduce($bobotItems, function ($output, $carry) {
            $output += $carry['nilai_hasil'];

            return $output;
        }, 0);

        $pemohon['bobot'] = $bobotItems;
        $pemohon['total'] = $total;

        return $pemohon;
    }, $pemohonItems);

    $pemohonHasilItems = [];
    foreach ($pemohonItems as $pemohonItem) {
        $pemohonHasilItems[$pemohonItem['id']] = $pemohonItem['total'];
    }	

    arsort($pemohonHasilItems);

    $hasilModel->delete($kelurahan_id);

    $no = 1;
    foreach ($pemohonHasilItems as $pemohonId => $pemohonHasilItem) {
        $hasilModel->create([
            'alternatif_id' => input_form($pemohonId),
            'no' => input_form($no),
            'nilai' => input_form($pemohonHasilItem),
            'kelurahan_id' => $kelurahan_id
        ]);

        $no++;
    }

    $hasilItems = $hasilModel->index($kelurahan_id);

    foreach ($hasilItems as $hasilItem) {
        $resultDataView .= '<tr>' . 
                                '<td>' . $hasilItem['no'] . '</td>' . 
                                '<td>' . $hasilItem['nama'] . '</td>' . 
                                '<td>' . $hasilItem['nilai'] . '</td>' . 
                            '</tr>';
    }

    $status = true;
}

echo json_encode([
    'status' => $status,
    'result_data_view' => $resultDataView,
]);