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

        // !! LOGIKA TAMBAHAN UNTUK KOTAK DASHBOARD (hlm 66-68) !!
        // Kita panggil model di controller, bukan di view
        
        // 1. Jumlah Anggota (Admin)
        // Adaptasi: $this->ModelUser->getUserWhere (['role_id' => 1])->num_rows();
        // Kita gunakan countAllResults() di CI4
        $data['total_anggota'] = $this->ModelUser->where(['role_id' => 1])->countAllResults();

        // 2. Stok Buku Terdaftar
        // Adaptasi: $this->ModelBuku->total('stok', $where);
        // Kita gunakan selectSum() di CI4
        $data['total_stok_buku'] = $this->ModelBuku->selectSum('stok')->where(['stok !=' => 0])->get()->getRowArray()['stok'];
        
        // 3. Buku yang dipinjam
        // Adaptasi: $this->ModelBuku->total('dipinjam', $where);
        $data['total_dipinjam'] = $this->ModelBuku->selectSum('dipinjam')->where(['dipinjam !=' => 0])->get()->getRowArray()['dipinjam'];

        // 4. Buku yang dibooking
        // Adaptasi: $this->ModelBuku->total('dibooking', $where);
        $data['total_dibooking'] = $this->ModelBuku->selectSum('dibooking')->where(['dibooking !=' => 0])->get()->getRowArray()['dibooking'];


        // Tampilkan view (CI4 way)
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('admin/index', $data);
        echo view('templates/footer');
    }
}