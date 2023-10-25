<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/middleware/hasAuth.php';

use Models\Hasil;
use Models\Kriteria;
use Models\Pemohon;

$hasilModel = new Hasil($pdo);
$pemohonModel = new Pemohon($pdo);

$bulanSelected = isset($_GET['bulan']) ? $_GET['bulan'] : 1;

$hasilItems = $hasilModel->index($bulanSelected);
$bobotAlternatifItems = $pemohonModel->getBobotIn(array_column($hasilItems, 'alternatif_id'), $bulanSelected);

$hasilItems = array_map(function ($item) use ($bobotAlternatifItems) {
    $item['bobot'] = array_filter($bobotAlternatifItems, function ($bobot) use ($item) {
        return $item['alternatif_id'] == $bobot['alternatif_id'];
    });

    return $item;
}, $hasilItems);

$kriteriaModel = new Kriteria($pdo);
$kriteriaItems = $kriteriaModel->index();

ob_start();

extract([
    'hasilItems' => $hasilItems,
    'kriteriaItems' => $kriteriaItems
]);

?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
		<meta name="author" content="AdminKit">
		<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link rel="shortcut icon" href="assets/img/icons/icon-48x48.png" />

		<link rel="canonical" href="https://demo-basic.adminkit.io/" />

		<title>Admin</title>
		
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<link href="assets/css/app.css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
	</head>

	<body>
		<div class="wrapper">

			<?php require 'sidebar.php' ?>

			<div class="main">
				<nav class="navbar navbar-expand navbar-light navbar-bg">
					<a class="sidebar-toggle js-sidebar-toggle">
						<i class="hamburger align-self-center"></i>
					</a>

					<div class="navbar-collapse collapse">
						<ul class="navbar-nav navbar-align">
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
									<span class="text-dark"><?php echo $_SESSION['nama'] ?></span>
								</a>
							</li>
						</ul>
					</div>
				</nav>

				<main class="content">
					<div class="container-fluid p-0">
                        <?php require_once __DIR__ . '/components/flash.php' ?>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Daftar Pemohon</h3>
                            </div>

                            <form action="" method="GET" enctype="multipart/form-data">

                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Bulan</label>
                                        <select name="bulan" id="bulan" class="form-control" required>
                                            <option value="">Pilih Bulan</option>
                                            <?php foreach (get_bulan() as $bulanId => $bulan) { ?>
                                                <option value="<?php echo $bulanId ?>" <?php echo $bulanSelected == $bulanId ? 'selected' : null ?>><?php echo $bulan ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <button type="button" class="btn btn-primary" id="perhitungan">Mulai Perhitungan</button>
                                        <a href="hapus_perhitungan.php?bulan=<?php echo $bulanSelected ?>" class="btn btn-danger me-1">Hapus Perhitungan</button>
                                        <a href="hasil_download.php?bulan=<?php echo $bulanSelected ?>" class="btn btn-primary" target="_blank">Download PDF</a>
                                    </div>
                                </div>

                            </form>

                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
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
                                    <tbody id="hasil_perhitungan">
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
                            </div>
                            <!-- /.card-body -->
                        </div>
					</div>
				</main>

				<footer class="footer">
					<div class="container-fluid">
						<div class="row text-muted">
							<div class="col-6 text-start">
								<p class="mb-0">
									<a class="text-muted" href="https://adminkit.io/" target="_blank"><strong>AdminKit</strong></a> &copy;
								</p>
							</div>
							<div class="col-6 text-end">
								<ul class="list-inline">
									<li class="list-inline-item">
										<a class="text-muted" href="https://adminkit.io/" target="_blank">Support</a>
									</li>
									<li class="list-inline-item">
										<a class="text-muted" href="https://adminkit.io/" target="_blank">Help Center</a>
									</li>
									<li class="list-inline-item">
										<a class="text-muted" href="https://adminkit.io/" target="_blank">Privacy</a>
									</li>
									<li class="list-inline-item">
										<a class="text-muted" href="https://adminkit.io/" target="_blank">Terms</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</footer>
			</div>
		</div>
                                                    
        <!-- jQuery -->
        <script src="plugins/jquery/jquery.min.js"></script>
		<script src="assets/js/app.js"></script>
        <script>
            $('#perhitungan').on('click', function (e) {
                let el = $(this);

                el.attr('disabled', 'disabled');

                let id = el.data('id');

                $.ajax({
                    url: 'perhitungan.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        bulan: $('#bulan').val()
                    }
                }).then(function (response) {
                    if (response.status) {
                        $('#hasil_perhitungan').html(response.result_data_view);
                    } else {
                        alert(response.message);
                    }
                    el.removeAttr('disabled');
                }).catch(function () {
                    alert('Gagal');
                    el.removeAttr('disabled');
                });
            });
        </script>

	</body>
</html>

<?php

$view = ob_get_clean();

reset_session_flash();

echo $view;

?>