<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/middleware/hasAuth.php';

use Models\Pemohon;

$pemohonModel = new Pemohon($pdo);

$id = input_form($_GET['id'] ?? null);
$item = $pemohonModel->find($id);

if ($item === null) {
    $_SESSION['type'] = 'danger';
    $_SESSION['message'] = 'Data Tidak Ditemukan';

    header('location: pemohon.php');
    die();
}

use Models\Kriteria;

$kriteriaModel = new Kriteria($pdo);
$kriteriaItems = $kriteriaModel->getKriteriaAndSubKriteria();

$alternatifBobotItems = $pemohonModel->getAlternatifBobotByAlternatifId($id);

ob_start();

extract([
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

                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Tambah Bobot Pemohon</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
							<div class="card-body">
								<div class="mb-3">
									<label>Nama</label>
									<input type="text" name="nama" class="form-control" placeholder="Nama" value="<?php echo $item['nama'] ?>" disabled>
								</div>
								<div class="mb-3">
									<label>Alamat</label>
									<textarea name="alamat" id="" cols="4" class="form-control" placeholder="Alamat" disabled><?php echo $item['alamat'] ?></textarea>
								</div>
							</div>

							<div class="card-header">
                                <h3 class="card-title">Tambah Bobot</h3>
                            </div>


                            <form action="tambah_pemohon_bobot_proses.php" method="POST" enctype="multipart/form-data">
								<input type="hidden" name="pemohon_id" value="<?php echo $id ?>">

								<div class="card-body">
									<div class="mb-3">
                                        <label class="form-label">Bulan</label>
                                        <select name="bulan" id="" class="form-control" required>
                                            <option value="">Pilih Bulan</option>
											<?php foreach (get_bulan() as $bulanId => $bulan) { ?>
												<option value="<?php echo $bulanId ?>"><?php echo $bulan ?></option>
											<?php } ?>
                                        </select>
                                    </div>

									<?php foreach ($kriteriaItems as $kriteriaItem) { ?>
										<div class="mb-3">
											<label for="">Kriteria: <?php echo $kriteriaItem['nama'] ?></label>
											<?php if ($kriteriaItem['status_sub'] == 1) { ?>
												<select name="kriteria[<?php echo $kriteriaItem['id'] ?>][bobot]" id="" class="form-control" required>
													<option value=""><?php echo $kriteriaItem['nama'] ?></option>
													<?php foreach ($kriteriaItem['sub_kriteria'] as $sub_kriteria) { ?>
														<option value="<?php echo $sub_kriteria['id'] ?>"><?php echo $sub_kriteria['nama'] ?></option>
													<?php } ?>
												</select>
											<?php } else { ?>
												<input type="number" name="kriteria[<?php echo $kriteriaItem['id'] ?>][bobot]" class="form-control" placeholder="<?php echo $kriteriaItem['nama'] ?>" required>
											<?php } ?>
										</div>
										<input type="hidden" name="kriteria[<?php echo $kriteriaItem['id'] ?>][status_sub]" value="<?php echo $kriteriaItem['status_sub'] ?>">
									<?php } ?>
								</div>

								<!-- /.card-body -->
								<div class="card-footer">
									<button type="submit" class="btn btn-primary">Submit</button>
								</div>
							 
                            </form>

							<div class="card-header">
                                <h3 class="card-title">Data Bobot</h3>
                            </div>

							<!-- /.card-header -->
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Bulan</th>
                                            <th>Kriteria</th>
											<th>Sub Kriteria</th>
											<th>Bobot</th>
                                            <th>Option</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($alternatifBobotItems as $index => $alternatifBobotItem) { ?>
                                            <tr>
                                                <td><?php echo $index + 1 ?></td>
                                                <td><?php echo get_bulan()[$alternatifBobotItem['bulan']] ?? '-' ?></td>
                                                <td><?php echo $alternatifBobotItem['kriteria_nama'] ?></td>
                                                <td><?php echo $alternatifBobotItem['sub_kriteria_nama'] ?></td>
                                                <td><?php echo $alternatifBobotItem['bobot'] ?></td>
                                                <td>
                                                    <a href="hapus_pemohon_bobot_proses.php?id=<?php echo $alternatifBobotItem['id'] ?>" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i> Hapus
                                                    </a>
                                                </td>
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

		<script src="assets/js/app.js"></script>

	</body>
</html>

<?php

$view = ob_get_clean();

reset_session_flash();

echo $view;

?>