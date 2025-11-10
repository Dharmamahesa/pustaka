<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
// Set default controller ke Autentifikasi sesuai alur modul
$routes->setDefaultController('Autentifikasi');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// Nonaktifkan Auto Routing (Best Practice CI4)
$routes->setAutoRoute(false);


/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Rute Default Proyek Pustaka Booking (Pertemuan 9)
$routes->get('/', 'Autentifikasi::index');


// --- Rute Autentifikasi (Login/Registrasi) ---
// (Diadaptasi dari Pertemuan 9 & 10)
$routes->group('autentifikasi', static function ($routes) {
    // Method index (Login) - bisa GET (menampilkan) dan POST (submit)
    $routes->match(['get', 'post'], '/', 'Autentifikasi::index');
    
    // Method registrasi - bisa GET (menampilkan) dan POST (submit)
    $routes->match(['get', 'post'], 'registrasi', 'Autentifikasi::registrasi');
    
    // Method lain
    $routes->get('logout', 'Autentifikasi::logout');
    $routes->get('blok', 'Autentifikasi::blok');
    $routes->get('gagal', 'Autentifikasi::gagal');
    
    // Asumsi dari view (hlm. 60)
    $routes->get('lupaPassword', 'Autentifikasi::lupaPassword'); 
});


// --- Rute Admin (Dashboard) ---
// (Diadaptasi dari Pertemuan 9)
$routes->get('admin', 'Admin::index');


// --- Rute User (Profil) ---
// (Diadaptasi dari Pertemuan 10)
$routes->group('user', static function ($routes) {
    $routes->get('/', 'User::index');
    $routes->get('anggota', 'User::anggota');
    
    // Method ubahProfil - bisa GET (menampilkan) dan POST (submit)
    // Dibuat 'ubahprofil' (lowercase) untuk mengatasi error 404 sebelumnya
    $routes->match(['get', 'post'], 'ubahprofil', 'User::ubahprofil');
});


// --- Rute Buku & Kategori ---
// (Diadaptasi dari Pertemuan 11 & 12)
$routes->group('buku', static function ($routes) {
    // Method index (Data Buku) - bisa GET (menampilkan) dan POST (submit tambah)
    $routes->match(['get', 'post'], '/', 'Buku::index');
    
    // Method ubahBuku - perlu ID (:num adalah placeholder angka)
    $routes->match(['get', 'post'], 'ubahBuku/(:num)', 'Buku::ubahBuku/$1');
    
    // Method hapusBuku - perlu ID
    $routes->get('hapusBuku/(:num)', 'Buku::hapusBuku/$1');

    // Method Kategori - bisa GET (menampilkan) dan POST (submit tambah)
    $routes->match(['get', 'post'], 'kategori', 'Buku::kategori');
    
    // Method hapusKategori - perlu ID
    $routes->get('hapusKategori/(:num)', 'Buku::hapusKategori/$1');
});


// --- Rute Latihan Awal (Opsional) ---
// (Diadaptasi dari Pertemuan 3, 4, 5)
$routes->get('latihan1', 'Latihan1::index');
$routes->get('latihan1/penjumlahan/(:num)/(:num)', 'Latihan1::penjumlahan/$1/$2');

$routes->get('matakuliah', 'Matakuliah::index');
$routes->post('matakuliah/cetak', 'Matakuliah::cetak');

$routes->get('web', 'Web::index');
$routes->get('web/about', 'Web::about');
$routes->group('booking', static function ($routes) {
    // Memerlukan login
    $routes->get('/', 'Booking::index');
    $routes->get('tambahBooking/(:num)', 'Booking::tambahBooking/$1');
    $routes->get('hapusbooking/(:num)', 'Booking::hapusbooking/$1');
    $routes->get('bookingSelesai', 'Booking::bookingSelesai');
    $routes->get('exportToPdf/(:any)', 'Booking::exportToPdf/$1');
});
$routes->group('pinjam', ['filter' => 'auth'], static function ($routes) {
    // Memerlukan login admin
    $routes->get('/', 'Pinjam::index');
    $routes->get('daftarBooking', 'Pinjam::daftarBooking');
    $routes->get('pinjamAct/(:any)', 'Pinjam::pinjamAct/$1');
    $routes->get('ubahStatus/(:any)', 'Pinjam::ubahStatus/$1');
});
// --- Rute Laporan (Admin) ---
$routes->group('laporan', ['filter' => 'auth'], static function ($routes) {
    // Laporan Buku
    $routes->get('laporan_buku', 'Laporan::laporan_buku');
    $routes->get('cetak_laporan_buku', 'Laporan::cetak_laporan_buku');
    $routes->get('laporan_buku_pdf', 'Laporan::laporan_buku_pdf');
    $routes->get('export_excel_buku', 'Laporan::export_excel_buku');

    // Laporan Anggota
    $routes->get('laporan_anggota', 'Laporan::laporan_anggota');
    $routes->get('cetak_laporan_anggota', 'Laporan::cetak_laporan_anggota');
    $routes->get('laporan_anggota_pdf', 'Laporan::laporan_anggota_pdf');
    $routes->get('export_excel_anggota', 'Laporan::export_excel_anggota');

    // Laporan Pinjam (Bisa GET untuk tampil, POST untuk submit filter)
    $routes->match(['get', 'post'], 'laporan_pinjam', 'Laporan::laporan_pinjam');
    // Rute cetak pinjam (ini dipanggil internal oleh 'laporan_pinjam' saat POST,
    // tapi kita buat juga rute GET jika diperlukan)
    $routes->get('laporan_pinjam_print', 'Laporan::laporan_pinjam_print');
    $routes->get('laporan_pinjam_pdf', 'Laporan::laporan_pinjam_pdf');
    $routes->get('export_excel_pinjam', 'Laporan::export_excel_pinjam');
});
$routes->group('user', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'User::index');
    $routes->get('anggota', 'User::anggota');
    $routes->match(['get', 'post'], 'ubahprofil', 'User::ubahprofil');
    
    // !! TAMBAHKAN RUTE INI !!
    $routes->get('riwayatPeminjaman', 'User::riwayatPeminjaman');
});
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}