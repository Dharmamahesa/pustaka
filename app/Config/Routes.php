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
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

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
    $routes->match(['get', 'post'], 'ubahProfil', 'User::ubahProfil');
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


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}