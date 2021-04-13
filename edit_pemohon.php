<?php

require __DIR__ . '/config/connect.php';
require __DIR__ . '/config/session.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/form.php';
require __DIR__ . '/middleware/hasAuth.php';

use Models\Pemohon;
use Models\Kelurahan;
use Models\Kriteria;

$pemohonModel = new Pemohon($pdo);

$id = input_form($_GET['id'] ?? null);
$item = $pemohonModel->find($id);

$itemBobot = $pemohonModel->getBobot($id);
$itemBobot = array_reduce($itemBobot, function ($output, $carry) {
    $output[$carry['kriteria_id']] = $carry['sub_kriteria_id'];

    return $output;
}, []);

$kelurahanModel = new Kelurahan($pdo);
$kelurahanItems = $kelurahanModel->index();

$kriteriaModel = new Kriteria($pdo);
$kriteriaItems = $kriteriaModel->getKriteriaAndSubKriteria();

if ($item === null) {
    $_SESSION['type'] = 'danger';
    $_SESSION['message'] = 'Data Tidak Ditemukan';

    header('location: pemohon.php');
    die();
}

ob_start();

extract([
    'kelurahanItems' => $kelurahanItems
]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Dashboard 2</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index3.html" class="brand-link">
                <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">AdminLTE 3</span>
            </a>

            <?php require './sidebar.php' ?>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Pemohon</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Pemohon</li>
                        </ol>
                    </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <?php require_once __DIR__ . '/components/flash.php' ?>

                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Pemohon</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="edit_pemohon_proses.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $item['id'] ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="nama" class="form-control" placeholder="Nama" value="<?php echo $item['nama'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Kelurahan</label>
                                    <select name="kelurahan_id" id="" class="form-control" required>
                                        <option value="">Pilih Kelurahan</option>
                                        <?php foreach ($kelurahanItems as $kelurahanItem) { ?>
                                            <option value="<?php echo $kelurahanItem['id'] ?>" <?php echo $kelurahanItem['id'] == $item['kelurahan_id'] ? 'selected' : null ?>><?php echo $kelurahanItem['nama'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea name="alamat" id="" cols="4" class="form-control" placeholder="Alamat" required><?php echo $item['alamat'] ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Pekerjaan</label>
                                    <input type="text" name="pekerjaan" class="form-control" placeholder="Pekerjaan" value="<?php echo $item['pekerjaan'] ?>" required>
                                </div>

                                <hr>

                                <?php foreach ($kriteriaItems as $kriteriaItem) { ?>
                                    <div class="form-group">
                                        <label for=""><?php echo $kriteriaItem['nama'] ?></label>
                                        <?php if ($kriteriaItem['status_sub'] == 1) { ?>
                                            <select name="kriteria[<?php echo $kriteriaItem['id'] ?>]" id="" class="form-control" required>
                                                <option value=""><?php echo $kriteriaItem['nama'] ?></option>
                                                <?php foreach ($kriteriaItem['sub_kriteria'] as $sub_kriteria) { ?>
                                                    <option value="<?php echo $sub_kriteria['id'] ?>" <?php echo ($itemBobot[$kriteriaItem['id']] ?? 0) == $sub_kriteria['id'] ? 'selected' : null ?>><?php echo $sub_kriteria['nama'] ?></option>
                                                <?php } ?>
                                            </select>
                                        <?php } else { ?>
                                            <input type="number" name="kriteria[<?php echo $kriteriaItem['id'] ?>]" class="form-control" min="0" placeholder="<?php echo $kriteriaItem['nama'] ?>" value="<?php echo $itemBobot[$kriteriaItem['id']] ?? 0 ?>" required>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div><!--/. container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.1.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>

</body>
</html>

<?php

$view = ob_get_clean();

reset_session_flash();

echo $view;

?>