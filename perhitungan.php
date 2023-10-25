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

try {
    // Perhitungan Maut

    // Hitung Total Bobot Kriteria
    $totalKriteriaBobot = array_reduce($kriteriaItems, function ($output, $carry) {
        $output += $carry['bobot'];

        return $output;
    }, 0);
    //

    // Hitung Normalisasi Bobot Kriteria
    $kriteriaItems = array_map(function ($kriteria) use ($totalKriteriaBobot) {
        $kriteria['nilai_normalisasi'] = number_format($kriteria['bobot'] / $totalKriteriaBobot, 3);

        return $kriteria;
    }, $kriteriaItems);
    //

    // PERHITUNGAN NILAI MATRIK MIN MAX
    $kriteriaItems = array_map(function ($kriteria) use ($pemohonItems) {
        $nilai_matrik_max = max(
            array_map(function ($pemohon) use ($kriteria) {
                return $pemohon['bobot'][$kriteria['id']]['nilai'] ?? 0;
            }, $pemohonItems)
        );

        $nilai_matrik_min = min(
            array_map(function ($pemohon) use ($kriteria) {
                return $pemohon['bobot'][$kriteria['id']]['nilai'] ?? 0;
            }, $pemohonItems)
        );

        $kriteria['nilai_matrik_min'] = $nilai_matrik_min;
        $kriteria['nilai_matrik_max'] = $nilai_matrik_max;

        return $kriteria;
    }, $kriteriaItems);

    // Normalisasi Id Kriteria
    $kriteriaIdItems = [];

    foreach ($kriteriaItems as $kriteriaItem) {
        $kriteriaIdItems[$kriteriaItem['id']] = $kriteriaItem;
    }

    $kriteriaItems = $kriteriaIdItems;
    //

    // PERHITUNGAN NILAI BOBOT MATRIK
    $pemohonItems = array_map(function ($pemohon) use ($kriteriaItems) {
        $bobotItems = array_map(function ($bobot) use ($kriteriaItems) {
            // PERHITUNGAN NILAI UTILITY
            $atas = $bobot['nilai'] - $kriteriaItems[$bobot['kriteria_id']]['nilai_matrik_min'];
            $bawah = $kriteriaItems[$bobot['kriteria_id']]['nilai_matrik_max'] - $kriteriaItems[$bobot['kriteria_id']]['nilai_matrik_min'];
            
            $nilai_normalisasi = number_format($atas / $bawah, 3);

            $bobot['nilai_normalisasi'] = $nilai_normalisasi;   

            $bobot['nilai_hasil'] = number_format($nilai_normalisasi * $kriteriaItems[$bobot['kriteria_id']]['nilai_normalisasi'], 3);
            //

            return $bobot;
        }, $pemohon['bobot']);

        // PERHITUNGAN TOTAL NILAI BOBOT HASIL UTILITY
        $total = array_reduce($bobotItems, function ($output, $carry) {
            $output += $carry['nilai_hasil'];

            return $output;
        }, 0);
        //

        $pemohon['bobot'] = $bobotItems;
        $pemohon['total'] = $total;

        return $pemohon;
    }, $pemohonItems);

    // NORMALISASI ID ALTERNAITF
    $pemohonHasilItems = [];
    foreach ($pemohonItems as $pemohonItem) {
        $pemohonHasilItems[$pemohonItem['id']] = $pemohonItem['total'];
    }	
    //	

    arsort($pemohonHasilItems);

    $hasilModel->delete();

    $no = 1;
    foreach ($pemohonHasilItems as $pemohonId => $pemohonHasilItem) {
        $hasilModel->create([
            'alternatif_id' => input_form($pemohonId),
            'no' => input_form($no),
            'nilai' => input_form($pemohonHasilItem)
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
                                '<td>' . $hasilItem['nilai'] . '</td>' . 
                                '<td>' . $hasilItem['no'] . '</td>' .
                            '</tr>';
    }

    echo json_encode([
        'status' => true,
        'result_data_view' => $resultDataView,
    ]);
} catch (\Exception $e) {
    echo json_encode([
        'status' => false,
        'result_data_view' => ''
    ]);
}