<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Web extends Controller
{
    // Konstruktor (Magic Method) akan dijalankan pertama kali saat kelas diinisiasi
    public function __construct()
    {
        // Memuat helper 'url' agar fungsi base_url() dapat digunakan di Controller dan View
        helper('url');
    }

    // Method default yang akan dijalankan ketika mengakses /web
    public function index()
    {
        $data['judul'] = "Halaman Depan";
        
        // Memuat bagian template
        echo view('v_header', $data);
        echo view('v_index', $data);
        echo view('v_footer', $data);
    }
    
    // Method untuk menampilkan halaman About
    public function about()
    {
        $data['judul'] = "Halaman About";
        
        // Memuat bagian template
        echo view('v_header', $data);
        echo view('v_about', $data);
        echo view('v_footer', $data);
    }
}