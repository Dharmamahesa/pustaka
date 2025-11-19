<?php

namespace Config;

$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
// Default controller sekarang adalah Home (Frontend)
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false); // Nonaktifkan AutoRoute

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Rute Default (Beranda Frontend)
$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');
$routes->get('home/detailBuku/(:num)', 'Home::detailBuku/$1');

// --- Rute Autentifikasi (Login/Registrasi) ---
$routes->group('autentifikasi', static function ($routes) {
    $routes->match(['get', 'post'], '/', 'Autentifikasi::index');
    $routes->match(['get', 'post'], 'registrasi', 'Autentifikasi::registrasi');
    $routes->get('logout', 'Autentifikasi::logout');
    $routes->get('blok', 'Autentifikasi::blok');
    $routes->get('gagal', 'Autentifikasi::gagal');
    $routes->get('lupaPassword', 'Autentifikasi::lupaPassword');
});

// --- Rute Admin (Dashboard) ---
$routes->get('admin', 'Admin::index', ['filter' => 'auth']);

// --- Rute User (Profil) ---
$routes->group('user', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'User::index');
    $routes->get('anggota', 'User::anggota');
    $routes->match(['get', 'post'], 'ubahprofil', 'User::ubahprofil');
    $routes->get('riwayatPeminjaman', 'User::riwayatPeminjaman');
});

// --- Rute Buku & Kategori (Admin) ---
$routes->group('buku', ['filter' => 'auth'], static function ($routes) {
    $routes->match(['get', 'post'], '/', 'Buku::index');
    $routes->match(['get', 'post'], 'ubahBuku/(:num)', 'Buku::ubahBuku/$1');
    $routes->get('hapusBuku/(:num)', 'Buku::hapusBuku/$1');
    $routes->match(['get', 'post'], 'kategori', 'Buku::kategori');
    $routes->get('hapusKategori/(:num)', 'Buku::hapusKategori/$1');
});

// --- Rute Booking (Member) ---
$routes->group('booking', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Booking::index');
    $routes->get('tambahBooking/(:num)', 'Booking::tambahBooking/$1');
    $routes->get('hapusbooking/(:num)', 'Booking::hapusbooking/$1');
    $routes->get('bookingSelesai', 'Booking::bookingSelesai');
    $routes->get('exportToPdf/(:any)', 'Booking::exportToPdf/$1');
});

// --- Rute Pinjam (Admin) ---
$routes->group('pinjam', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Pinjam::index');
    $routes->get('daftarBooking', 'Pinjam::daftarBooking');
    $routes->get('pinjamAct/(:any)', 'Pinjam::pinjamAct/$1');
    $routes->get('ubahStatus/(:any)', 'Pinjam::ubahStatus/$1');
});

// --- Rute Laporan (Admin) ---
$routes->group('laporan', ['filter' => 'auth'], static function ($routes) {
    $routes->get('laporan_buku', 'Laporan::laporan_buku');
    $routes->get('cetak_laporan_buku', 'Laporan::cetak_laporan_buku');
    $routes->get('laporan_buku_pdf', 'Laporan::laporan_buku_pdf');
    $routes->get('export_excel_buku', 'Laporan::export_excel_buku');
    $routes->get('laporan_anggota', 'Laporan::laporan_anggota');
    $routes->get('cetak_laporan_anggota', 'Laporan::cetak_laporan_anggota');
    $routes->get('laporan_anggota_pdf', 'Laporan::laporan_anggota_pdf');
    $routes->get('export_excel_anggota', 'Laporan::export_excel_anggota');
    $routes->match(['get', 'post'], 'laporan_pinjam', 'Laporan::laporan_pinjam');
});

// --- Rute Latihan Awal (Opsional) ---
$routes->get('latihan1', 'Latihan1::index');
$routes->get('latihan1/penjumlahan/(:num)/(:num)', 'Latihan1::penjumlahan/$1/$2');
$routes->get('matakuliah', 'Matakuliah::index');
$routes->post('matakuliah/cetak', 'Matakuliah::cetak');
$routes->get('web', 'Web::index');
$routes->get('web/about', 'Web::about');


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}