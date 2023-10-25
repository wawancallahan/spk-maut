<?php
require_once('koneksi.php');


// AMBIL DATA KRITERIA
$queryKriteriaItems = mysqli_query($db, "SELECT * FROM kriteria");
$kriteriaItems = mysqli_fetch_all($queryKriteriaItems, MYSQLI_ASSOC);
//

// AMBIL DATA ALTERNATIF
$queryPemohonItems = mysqli_query($db, "SELECT alternatif.id_alternatif AS id, alternatif.nalternatif AS nama, bobot.id_kriteria AS kriteria_id, subkriteria.nsubkriteria AS nilai " . 
                 "FROM alternatif LEFT JOIN bobot ON alternatif.id_alternatif = bobot.id_alternatif " .
                 "LEFT JOIN subkriteria ON bobot.id_subkriteria = subkriteria.id_subkriteria");

$resultPemohonItems = mysqli_fetch_all($queryPemohonItems, MYSQLI_ASSOC);
$pemohonItems = [];

foreach ($resultPemohonItems as $resultItem) {
    if ( ! isset($pemohonItems[$resultItem['id']])) {
        $pemohonItems[$resultItem['id']] = [
            'id' => $resultItem['id'],
            'nama' => $resultItem['nama'],
            'bobot' => []
        ];
    }

    $pemohonItems[$resultItem['id']]['bobot'][$resultItem['kriteria_id']] = [
        'kriteria_id' => $resultItem['kriteria_id'],
        'nilai' => $resultItem['nilai']
    ];
}
//


// Perhitungan MAUT

// Hitung Total Bobot Kriteria
$totalKriteriaBobot = array_reduce($kriteriaItems, function ($output, $carry) {
    $output += $carry['nilai'];

    return $output;
}, 0);
//

// Hitung Normalisasi Bobot Kriteria
$kriteriaItems = array_map(function ($kriteria) use ($totalKriteriaBobot) {
    $kriteria['nilai_normalisasi'] = number_format($kriteria['nilai'] / $totalKriteriaBobot, 3);

    return $kriteria;
}, $kriteriaItems);
//

// PERHITUNGAN NILAI MATRIK MIN MAX
$kriteriaItems = array_map(function ($kriteria) use ($pemohonItems) {
    $nilai_matrik_max = max(
        array_map(function ($pemohon) use ($kriteria) {
            return $pemohon['bobot'][$kriteria['id_kriteria']]['nilai'] ?? 0;
        }, $pemohonItems)
    );

    $nilai_matrik_min = min(
        array_map(function ($pemohon) use ($kriteria) {
            return $pemohon['bobot'][$kriteria['id_kriteria']]['nilai'] ?? 0;
        }, $pemohonItems)
    );

    $kriteria['nilai_matrik_min'] = $nilai_matrik_min;
    $kriteria['nilai_matrik_max'] = $nilai_matrik_max;

    return $kriteria;
}, $kriteriaItems);


// Normalisasi Id Kriteria
$kriteriaIdItems = [];

foreach ($kriteriaItems as $kriteriaItem) {
    $kriteriaIdItems[$kriteriaItem['id_kriteria']] = $kriteriaItem;
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
//

// NORMALISASI ID ALTERNAITF
$pemohonHasilItems = [];
foreach ($pemohonItems as $pemohonItem) {
    $pemohonHasilItems[$pemohonItem['id']] = $pemohonItem['total'];
}	
//


// PROSES PENGURUTKAN NILAI HASIL TERBESAR KE TERKECIL
arsort($pemohonHasilItems);
//

mysqli_query($db, "DELETE FROM hasil");

// PROSES MENYIMPAN HASIL PENGURUTAN KE DATABASE
$no = 1;
foreach ($pemohonHasilItems as $pemohonId => $pemohonHasilItem) {
    $nilai = $pemohonHasilItem;

    mysqli_query($db, "INSERT INTO hasil VALUES (NULL, '$pemohonId', '$no', '$nilai')");

    $no++;
}
//


// PROSES MENAMPILKAN HASIL PENGURUTAN KE TABLE TAMPILAN
$queryHasilItems = mysqli_query($db, "SELECT alternatif.nalternatif AS nama, hasil.nilai AS nilai, hasil.no AS no FROM hasil LEFT JOIN alternatif ON hasil.alternatif_id = alternatif.id_alternatif");

$hasilItems = mysqli_fetch_all($queryHasilItems, MYSQLI_ASSOC);

$resultDataView = '';

foreach ($hasilItems as $index => $hasilItem) {

    $resultDataView .= '<tr>' . 
                            '<td>' . ($index + 1) . '</td>' . 
                            '<td>' . $hasilItem['nama'] . '</td>' .
                            '<td>' . $hasilItem['nilai'] . '</td>' . 
                            '<td style="text-align: left !important;" >' . $hasilItem['no'] . '</td>' .
                        '</tr>';
}
//

$status = true;

echo json_encode([
    'status' => $status,
    'result_data_view' => $resultDataView,
]);