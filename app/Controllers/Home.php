<?php

namespace App\Controllers;

// Import model yang kita perlukan
use App\Models\ModelBuku;
use App\Models\ModelUser;

class Home extends BaseController
{
    protected $ModelBuku;
    protected $ModelUser;

    public function __construct()
    {
        // Inisialisasi model
        $this->ModelBuku = new ModelBuku();
        $this->ModelUser = new ModelUser();
    }

    /**
     * Halaman Utama (Beranda)
     * Menampilkan daftar semua buku
     */
    public function index()
    {
        $data = [
            'judul' => "Katalog Buku",
            // Ambil data buku (seperti di CI3 Home.php)
            'buku' => $this->ModelBuku->getBuku()->getResultArray(), 
            'user' => [] // Default user kosong
        ];

        // Cek jika user sudah login (CI4 session)
        if (session()->get('email')) {
            $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        }

        // Tampilkan view
        echo view('templates/templates-user/header', $data);
        // Kita gunakan view buku/daftarbuku.php dari repo CI3
        echo view('buku/daftarbuku', $data); 
        echo view('templates/templates-user/footer', $data);
    }

    /**
     * Halaman Detail Buku
     * Adaptasi dari method detailBuku() di CI3 Home.php
     */
    public function detailBuku($id = null)
    {
        if ($id === null) {
            return redirect()->to('/');
        }

        $data = [
            'judul' => "Detail Buku",
            // Ambil data buku spesifik (CI4 getRowArray)
            'buku' => $this->ModelBuku->bukuWhere(['id' => $id])->getRowArray(), 
            'user' => [] // Default user kosong
        ];

        // Cek jika user sudah login (CI4 session)
        if (session()->get('email')) {
            $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        }

        // Tampilkan view
        echo view('templates/templates-user/header', $data);
        echo view('buku/detail-buku', $data);
        echo view('templates/templates-user/footer', $data);
    }
}