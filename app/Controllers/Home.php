<?php

namespace App\Controllers;

// Import model yang kita perlukan
use App\Models\ModelBuku;
use App\Models\ModelUser;
use App\Models\ModelBooking; // Perlu untuk hitung keranjang

class Home extends BaseController
{
    protected $ModelBuku;
    protected $ModelUser;
    protected $ModelBooking;

    public function __construct()
    {
        // Inisialisasi model
        $this->ModelBuku = new ModelBuku();
        $this->ModelUser = new ModelUser();
        $this->ModelBooking = new ModelBooking();
    }

    /**
     * Halaman Utama (Beranda)
     * Menampilkan daftar semua buku
     */
    public function index()
    {
        $data = [
            'judul' => "Katalog Buku",
            'buku' => $this->ModelBuku->getBuku()->getResultArray(), 
            'user' => [], // Default user kosong
            'count_temp' => 0 // Default keranjang 0
        ];

        // Cek jika user sudah login (CI4 session)
        if (session()->get('email')) {
            $user = $this->ModelUser->cekData(['email' => session()->get('email')]);
            $data['user'] = $user;
            
            // Hitung isi keranjang (tabel temp)
            $data['count_temp'] = $this->ModelBooking->getCountTemp(['id_user' => $user['id']]);
        }

        // Tampilkan view
        echo view('templates/templates-user/header', $data);
        echo view('buku/daftarbuku', $data); // View ini menampilkan buku-buku
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
            'buku' => $this->ModelBuku->bukuWhere(['id' => $id])->getRowArray(), 
            'user' => [], // Default user kosong
            'count_temp' => 0 // Default keranjang 0
        ];

        // Cek jika user sudah login (CI4 session)
        if (session()->get('email')) {
            $user = $this->ModelUser->cekData(['email' => session()->get('email')]);
            $data['user'] = $user;

            // Hitung isi keranjang (tabel temp)
            $data['count_temp'] = $this->ModelBooking->getCountTemp(['id_user' => $user['id']]);
        }

        // Tampilkan view
        echo view('templates/templates-user/header', $data);
        echo view('buku/detail-buku', $data);
        echo view('templates/templates-user/footer', $data);
    }
}