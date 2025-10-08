<?php

namespace App\Controllers;

use App\Models\ModelLatihan1;
use CodeIgniter\Controller;

class Latihan1 extends Controller
{
    public function __construct()
    {
        $this->ModelLatihan1 = new ModelLatihan1();
    }

    public function index()
    {
        echo "Selamat Datang.. selamat belajar Web Programming";
    }

    public function penjumlahan($n1, $n2)
{
    $data['nilai1'] = $n1;
    $data['nilai2'] = $n2;
    $data['hasil'] = $this->ModelLatihan1->jumlah($n1, $n2);

    return view('view-latihan1', $data);
}
}