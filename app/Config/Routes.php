<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =========================================================================
// 1. KONTROLER UTAMA & HALAMAN DEFAULT
// =========================================================================

// Rute Default: Diarahkan ke Controller Autentifikasi (Halaman Login)
// Di modul, Autentifikasi dijadikan Controller default (Halaman 61)
$routes->get('/', 'Autentifikasi::index'); 


// =========================================================================
// 2. TUGAS PERTEMUAN 3 (CONTOH MVC)
// =========================================================================

// Contoh 1 (Hanya Controller)
$routes->get('contoh1', 'Contoh1::index');

// Contoh 2 & 3 (Controller, View, Model - Penjumlahan)
// URI: /latihan1/penjumlahan/n1/n2
$routes->get('latihan1/penjumlahan/(:num)/(:num)', 'Latihan1::penjumlahan/$1/$2');


// =========================================================================
// 3. TUGAS PERTEMUAN 4 (TEMPLATING)
// =========================================================================

// Controller Web (Home dan About)
$routes->get('web', 'Web::index');
$routes->get('web/about', 'Web::about');


// =========================================================================
// 4. TUGAS PERTEMUAN 5 (FORM VALIDASI)
// =========================================================================

// Controller Matakuliah (Form Validation)
$routes->get('matakuliah', 'Matakuliah::index');
$routes->post('matakuliah/cetak', 'Matakuliah::cetak');


// =========================================================================
// 5. TUGAS PERTEMUAN 9 & 10 (AUTENTIFIKASI & REGISTRASI)
// =========================================================================

// Controller Autentifikasi (Login & Logout)
$routes->get('autentifikasi', 'Autentifikasi::index');
$routes->post('autentifikasi', 'Autentifikasi::index'); // Menggunakan POST untuk proses login
$routes->get('autentifikasi/logout', 'Autentifikasi::logout');

// Registrasi
$routes->get('autentifikasi/registrasi', 'Autentifikasi::registrasi');
$routes->post('autentifikasi/registrasi', 'Autentifikasi::registrasi'); // Menggunakan POST untuk proses registrasi

// Halaman Error (Blok/Gagal)
$routes->get('autentifikasi/blok', 'Autentifikasi::blok');
$routes->get('autentifikasi/gagal', 'Autentifikasi::gagal');


// =========================================================================
// 6. TUGAS PERTEMUAN 9 & 10 (ADMIN & USER)
// =========================================================================

// Controller Admin (Dashboard)
$routes->get('admin', 'Admin::index');

// Controller User (Profile & Data Anggota)
$routes->get('user', 'User::index');
$routes->get('user/anggota', 'User::anggota');

// Ubah Profile
$routes->get('user/ubahprofil', 'User::ubahProfil');
$routes->post('user/ubahprofil', 'User::ubahProfil'); // Menggunakan POST untuk proses update


// =========================================================================
// 7. TUGAS PERTEMUAN 11 & 12 (MANAJEMEN BUKU & KATEGORI)
// =========================================================================

// Controller Buku (Data Buku)
$routes->get('buku', 'Buku::index');
$routes->post('buku', 'Buku::index'); // Menggunakan POST untuk proses tambah buku
$routes->get('buku/ubahbuku/(:num)', 'Buku::ubahBuku/$1');
$routes->post('buku/ubahbuku', 'Buku::ubahBuku'); // Menggunakan POST untuk proses update
$routes->get('buku/hapusbuku/(:num)', 'Buku::hapusBuku/$1');

// Controller Buku (Data Kategori)
$routes->get('buku/kategori', 'Buku::kategori');
$routes->post('buku/kategori', 'Buku::kategori'); // Menggunakan POST untuk proses tambah kategori
$routes->get('buku/hapuskategori/(:num)', 'Buku::hapusKategori/$1');

// =========================================================================//