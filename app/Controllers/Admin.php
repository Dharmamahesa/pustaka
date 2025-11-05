<?php

namespace App\Controllers;

// Import model yang akan digunakan
use App\Models\ModelUser;
use App\Models\ModelBuku;

class Admin extends BaseController
{
    // Properti untuk menyimpan instance model
    protected $ModelUser;
    protected $ModelBuku;

    public function __construct()
    {
        // Buat instance dari model
        $this->ModelUser = new ModelUser();
        $this->ModelBuku = new ModelBuku();
        
        // Panggil helper cek_login
        // (Pastikan 'pustaka' helper sudah di-load di app/Config/Autoload.php)
        cek_login();
    }

    public function index()
    {
        // Ambil data dari modul
        $data['judul'] = 'Dashboard';
        
        // Ambil data user dari session (CI4 way)
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // Ambil data anggota (menggunakan ModelUser CI4 yang sudah kita buat)
        $data['anggota'] = $this->ModelUser->getUserLimit();
        
        // Ambil data buku (menggunakan ModelBuku CI4)
        $data['buku'] = $this->ModelBuku->getBuku()->getResultArray();

        // Tampilkan view (CI4 way)
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('admin/index', $data);
        echo view('templates/footer');
    }
}