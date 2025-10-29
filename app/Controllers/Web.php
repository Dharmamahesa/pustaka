<?php

namespace App\Controllers;

// Pastikan Anda memperluas BaseController
class Web extends BaseController 
{
    // [PERBAIKAN KRITIS]: Gunakan property $helpers untuk memuat helper secara otomatis
    protected $helpers = ['url']; 

    // Method __construct() kini dihapus total, menghilangkan Fatal Error
    // ...

    public function index()
    {
        $data['judul'] = "Halaman Depan";
        
        // Memuat bagian template
        echo view('v_header', $data);
        echo view('v_index', $data);
        echo view('v_footer');
    }
    
    public function about()
    {
        $data['judul'] = "Halaman About";
        
        echo view('v_header', $data);
        echo view('v_about', $data);
        echo view('v_footer');
    }
}