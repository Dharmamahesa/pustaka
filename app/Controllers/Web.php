<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Web extends Controller
{
    public function __construct()
    {
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