<?php

/**
 * Fungsi untuk mengecek apakah user sudah login.
 * Diadaptasi dari Modul Pertemuan 10 (hlm 75-78).
 */
function cek_login()
{
    // Mengambil service session di CI4
    $session = \Config\Services::session();

    // Jika 'email' tidak ada di session
    if (!$session->get('email')) {
        
        // Atur pesan flashdata
        $session->setFlashdata('pesan', '<div class="alert
        alert-danger" role="alert">Akses ditolak. Anda belum login!!
        </div>');
        
        // Redirect ke controller autentifikasi
        // (Gunakan return redirect()->to() di CI4)
        return redirect()->to('autentifikasi');
    }
    
    // (Catatan: Logika role_id di modul (hlm 78) berada di luar scope 'else'
    // yang sepertinya typo. Untuk saat ini, kita hanya cek login saja
    // sesuai implementasinya di controller User.php (hlm 86)).
}