<?php

namespace App\Controllers;

// Import model yang kita perlukan
use App\Models\ModelBuku;
use App\Models\ModelUser;
use App\Models\ModelBooking;

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
            // getBuku() di model menggunakan findAll(), jadi sudah array. Tidak perlu getResultArray()
            'buku' => $this->ModelBuku->getBuku(), 
            'user' => [], 
            'count_temp' => 0
        ];

        // Cek jika user sudah login
        if (session()->get('email')) {
            $user = $this->ModelUser->cekData(['email' => session()->get('email')]);
            $data['user'] = $user;
            
            // Hitung isi keranjang (tabel temp)
            $data['count_temp'] = $this->ModelBooking->getCountTemp(['id_user' => $user['id']]);
        }

        echo view('templates/templates-user/header', $data);
        echo view('buku/daftarbuku', $data);
        echo view('templates/templates-user/footer', $data);
    }

    /**
     * Halaman Detail Buku
     */
    public function detailBuku($id = null)
    {
        if ($id === null) {
            return redirect()->to('/');
        }

        $data = [
            'judul' => "Detail Buku",
            // PERBAIKAN DI SINI: Gunakan ->first() untuk mengambil satu data
            'buku' => $this->ModelBuku->bukuWhere(['id' => $id])->first(), 
            'user' => [], 
            'count_temp' => 0 
        ];

        // Cek jika user sudah login
        if (session()->get('email')) {
            $user = $this->ModelUser->cekData(['email' => session()->get('email')]);
            $data['user'] = $user;

            // Hitung isi keranjang
            $data['count_temp'] = $this->ModelBooking->getCountTemp(['id_user' => $user['id']]);
        }

        echo view('templates/templates-user/header', $data);
        echo view('buku/detail-buku', $data);
        echo view('templates/templates-user/footer', $data);
    }
}