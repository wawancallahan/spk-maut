<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil</title>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
        }

        .table th,
        .table td {
            border: 1px solid #000000;
        }
    </style>
</head>
<body>
    <h1>Hasil Alternatif</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <?php foreach ($kriteriaItems as $kriteriaItem) { ?>
                    <th><?php echo $kriteriaItem['nama'] ?></th>
                <?php } ?>
                <th>Nilai</th>
                <th>Rangking</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hasilItems as $index => $hasilItem) { ?>
                <?php
                    $bobot = array_values($hasilItem['bobot']);
                ?>
                <tr>
                    <td><?php echo $index + 1 ?></td>
                    <td><?php echo $hasilItem['nama'] ?></td>
                    <?php foreach ($kriteriaItems as $kriteriaItem) { ?>
                        <?php $bobotKey = array_search($kriteriaItem['id'], array_column($bobot, 'kriteria_id')); ?>
                        <td><?php echo $bobotKey !== false ? $bobot[$bobotKey]['bobot'] : null ?></td>
                    <?php } ?>
                    <td><?php echo $hasilItem['nilai'] ?></td>
                    <td><?php echo $hasilItem['no'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>