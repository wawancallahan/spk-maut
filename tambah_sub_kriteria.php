<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/middleware/hasAuth.php';

use Models\Kriteria;

$kriteriaModels = new Kriteria($pdo);
$kriteriaModels->setFilter([
    'status_sub' => 1 
]);
$kriteriaItems = $kriteriaModels->index();

ob_start();

extract([
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

                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Tambah Sub Kriteria</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form action="tambah_sub_kriteria_proses.php" method="POST">
                                <div class="card-body">

                                    <div class="mb-3">
                                        <label>Kriteria</label>
                                        <select name="kriteria_id" id="" class="form-control" required>
                                            <option value="">Pilih Kriteria</option>
                                            <?php foreach ($kriteriaItems as $kriteriaItem) { ?>
                                                <option value="<?php echo $kriteriaItem['id'] ?>"><?php echo $kriteriaItem['nama'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label>Nama</label>
                                        <input type="text" name="nama" class="form-control" placeholder="Nama" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Bobot</label>
                                        <input type="number" name="bobot" class="form-control" placeholder="Bobot" min="0" step="0.01" value="0" required>
                                    </div>

                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
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