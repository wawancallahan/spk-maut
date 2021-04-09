<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <a href="#" class="d-block"><?php echo $_SESSION['nama'] ?? '-' ?></a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
            with font-awesome or any other icon font library -->
            <li class="nav-item">
                <a href="index.php" class="nav-link active">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        Dashboard
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="pemohon.php" class="nav-link">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        Pemohon
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Master Data
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="kelurahan.php" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Kelurahan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="kriteria.php" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Kriteria</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="sub_kriteria.php" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Sub Kriteria</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="hasil.php" class="nav-link">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        Hasil
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        Logout
                    </p>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->