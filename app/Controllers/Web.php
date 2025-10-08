<?php

namespace App\Controllers;

// Ganti "use CodeIgniter\Controller;"
// Gunakan BaseController yang disediakan CodeIgniter
class Web extends BaseController 
{
    public function __construct()
    {
        // [Wajib Ditambahkan] Memanggil konstruktor parent untuk inisialisasi CodeIgniter
        parent::__construct();
        
        // Memuat helper 'url' 
        helper('url');
    }

    public function index()
    {
        $data['judul'] = "Halaman Depan";
        
        echo view('v_header', $data);
        echo view('v_index', $data);
        echo view('v_footer', $data);
    }
    
    public function about()
    {
        $data['judul'] = "Halaman About";
        
        echo view('v_header', $data);
        echo view('v_about', $data);
        echo view('v_footer', $data);
    }
}