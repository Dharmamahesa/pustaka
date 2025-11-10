<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url(); ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-book"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Pustaka Booking</div>
    </a>

    <hr class="sidebar-divider">

    <?php if (session()->get('role_id') == 1) : // Jika Admin ?>
        
        <div class="sidebar-heading">
            Admin
        </div>
        
        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('admin'); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <hr class="sidebar-divider mt-3">

        <div class="sidebar-heading">
            Master Data
        </div>
        
        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('buku'); ?>">
                <i class="fa fa-fw fa-book"></i>
                <span>Data Buku</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('buku/kategori'); ?>">
                <i class="fa fa-fw fa-tag"></i>
                <span>Data Kategori</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('user/anggota'); ?>">
                <i class="fa fa-fw fa-users"></i>
                <span>Data Anggota</span></a>
        </li>

        <hr class="sidebar-divider mt-3">

        <div class="sidebar-heading">
            Transaksi
        </div>

        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('pinjam'); ?>">
                <i class="fa fa-fw fa-shopping-cart"></i>
                <span>Data Peminjaman</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('pinjam/daftarBooking'); ?>">
                <i class="fa fa-fw fa-list"></i>
                <span>Data Booking</span></a>
        </li>

        <hr class="sidebar-divider mt-3">

        <div class="sidebar-heading">
            Laporan
        </div>

        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('laporan/laporan_buku'); ?>">
                <i class="fas fa-fw fa-book"></i>
                <span>Laporan Data Buku</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('laporan/laporan_anggota'); ?>">
                <i class="fas fa-fw fa-users"></i>
                <span>Laporan Data Anggota</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('laporan/laporan_pinjam'); ?>">
                <i class="fas fa-fw fa-list-alt"></i>
                <span>Laporan Peminjaman</span></a>
        </li>

    <?php else : // Jika Member (role_id = 2) ?>

        <div class="sidebar-heading">
            Member
        </div>

        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('user'); ?>">
                <i class="fas fa-fw fa-user"></i>
                <span>Profil Saya</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link pb-0" href="<?= base_url('user/riwayatPeminjaman'); ?>">
                <i class="fas fa-fw fa-history"></i>
                <span>Riwayat Peminjaman</span></a>
        </li>

    <?php endif; ?>

    <hr class="sidebar-divider mt-3">
    
    <li class="nav-item">
        <a class="nav-link pb-0" href="<?= base_url('autentifikasi/logout'); ?>" data-toggle="modal" data-target="#logoutModal">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span></a>
    </li>
    
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>