<?php

echo '<pre>';

$kriteria = [
    0.45, // Min
    0.25, // Max
    0.15, // Max
    0.10, // Min
    0.05  // Max    
];

$alternatif = [
    [70, 80, 90, 70, 90],
    [90, 90, 80, 70, 70],
    [90, 80, 70, 80, 90],
    [80, 70, 80, 70, 70],
    [70, 80, 70, 70, 80]
];

foreach ($kriteria as $kriteriaKey => $kriteriaItem) {
    $totalBobot = sqrt(array_reduce($alternatif, function ($output, $item) use ($kriteriaKey) {
        $output += pow($item[$kriteriaKey], 2);

        return $output;
    }, 0));

    $alternatif = array_map(function ($item) use ($totalBobot, $kriteriaKey) {
        $item[$kriteriaKey] = number_format($item[$kriteriaKey] / $totalBobot, 4);
        
        return $item;
    }, $alternatif);
}

foreach ($alternatif as $alternatifKey => $alternatifItem) {
    $newAlternatifItem = [];

    foreach ($alternatifItem as $alternatifItemKey => $alternatifItemValue) {
        $newAlternatifItem[$alternatifItemKey] = number_format($alternatifItemValue * $kriteria[$alternatifItemKey], 4);
    }

    $alternatif[$alternatifKey] = $newAlternatifItem;
}


$rankingAlternatif = [];

foreach ($alternatif as $alternatifKey => $alternatifItem) {
    $max = $alternatifItem[1] + $alternatifItem[2] + $alternatifItem[4];
    $min = $alternatifItem[0] + $alternatifItem[3];

    $rankingAlternatif[$alternatifKey] = number_format($max - $min, 4);
}

arsort($rankingAlternatif);

echo print_r($rankingAlternatif);