<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil <?php echo $item['nama'] ?></title>

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
    <h1>Hasil Pemohon <?php echo $item['nama'] ?></h1>
    
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hasilItems as $hasilItem) { ?>
                <tr>
                    <td><?php echo $hasilItem['no'] ?></td> 
                    <td><?php echo $hasilItem['nama'] ?></td> 
                    <td><?php echo $hasilItem['nilai'] ?></td>
                </tr> 
            <?php } ?>
        </tbody>
    </table>
</body>
</html>