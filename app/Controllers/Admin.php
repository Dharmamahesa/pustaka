<?php

namespace App\Controllers;

use App\Models\ModelUser;
use App\Models\ModelBuku;
use App\Models\ModelBooking;
use App\Models\ModelPinjam;

class Admin extends BaseController
{
    protected $ModelUser;
    protected $ModelBuku;
    protected $ModelBooking;
    protected $ModelPinjam;
    
    protected $helpers = ['form', 'url', 'session'];

    public function __construct()
    {
        // Inisialisasi semua model
        $this->ModelUser = new ModelUser();
        $this->ModelBuku = new ModelBuku();
        $this->ModelBooking = new ModelBooking();
        $this->ModelPinjam = new ModelPinjam();
        
        // Pastikan hanya admin yang bisa akses
        cek_login();
    }

    public function index()
    {
        $data['judul'] = 'Dashboard';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // ============================================================
        // LOGIKA WIDGET DASHBOARD (KOTAK WARNA-WARNI)
        // ============================================================
        
        // 1. Widget Anggota (Warna Biru)
        // Mengambil jumlah user dengan role member
        $data['total_anggota'] = $this->ModelUser->where('role_id', 2)->countAllResults();
        
        // 2. Widget Stok Buku (Warna Kuning)
        // Mengambil jumlah total judul buku
        $data['total_stok_buku'] = $this->ModelBuku->countAllResults();
        
        // 3. Widget Peminjaman (Warna Merah - "Buku yang dipinjam")
        // Mengambil jumlah data di tabel pinjam dengan status 'Pinjam'
        $data['total_dipinjam'] = $this->ModelPinjam->where('status', 'Pinjam')->countAllResults();
        
        // 4. Widget Booking (Warna Hijau - "Buku yang dibooking")
        // Mengambil jumlah data di tabel booking.
        // PERBAIKAN: Variabel disesuaikan dengan View ($total_dibooking)
        $data['total_dibooking'] = $this->ModelBooking->countAllResults();

        // ============================================================
        // DATA UNTUK TABEL
        // ============================================================
        
        // Data anggota terbaru (Limit 10)
        $data['anggota'] = $this->ModelUser->where('role_id', 2)->orderBy('id', 'DESC')->findAll(10);
        
        // Data buku
        $data['buku'] = $this->ModelBuku->getBuku(); 

        // Data detail booking (Kosongkan agar tidak error jika view memanggilnya untuk tabel kosong)
        $data['detail'] = [];

        // Tampilkan View
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('admin/index', $data);
        echo view('templates/footer');
    }
}