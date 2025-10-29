<?php

namespace App\Controllers;

use App\Models\ModelLatihan1;

// Gunakan BaseController
class Latihan1 extends BaseController 
{
    protected $ModelLatihan1;

    public function __construct()
    {
        // [FIX KRITIS]: Wajib dipanggil sebelum instansiasi Model
        parent::__construct(); 

        // Instansiasi Model setelah parent::__construct()
        $this->ModelLatihan1 = new ModelLatihan1();
    }

    public function index()
    {
        echo "Selamat Datang.. selamat belajar Web Programming";
    }

    public function penjumlahan($n1, $n2)
    {
        // ... method ini sekarang aman dieksekusi
        $data['nilai1'] = $n1;
        $data['nilai2'] = $n2;
        $data['hasil'] = $this->ModelLatihan1->jumlah($n1, $n2);

        return view('view-latihan1', $data);
    }
}