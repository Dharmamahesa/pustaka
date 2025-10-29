<?php

namespace App\Controllers;

// Matakuliah harus memperluas BaseController (praktik terbaik CI4)
class Matakuliah extends BaseController 
{
    // Memuat helper yang diperlukan: 'url' untuk base_url() dan 'form' untuk validasi.
    protected $helpers = ['url', 'form']; 

    // Method untuk menampilkan form input (index)
    public function index()
    {
        // Langsung memuat view form input
        echo view('view-form-matakuliah');
    }

    // Method untuk memproses dan memvalidasi data form (cetak)
    public function cetak()
    {
        // 1. Mendefinisikan Aturan Validasi (Rules)
        $rules = [
            'kode' => [
                'label' => 'Kode Matakuliah',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    // Pesan error kustom dari modul
                    'required' => 'Kode Matakuliah Harus diisi',
                    'min_length' => 'Kode terlalu pendek'
                ]
            ],
            'nama' => [
                'label' => 'Nama Matakuliah',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    // Pesan error kustom dari modul
                    'required' => 'Nama Matakuliah Harus diisi',
                    'min_length' => 'Nama terlalu pendek'
                ]
            ]
        ];

        // 2. Jalankan Validasi
        // $this->validate() menggunakan service validation CI4
        if (!$this->validate($rules)) {
            // Jika validasi GAGAL, kembalikan ke form dengan pesan error
            $data['validation'] = $this->validator;
            return view('view-form-matakuliah', $data);
        }

        // 3. Jika validasi BERHASIL (Data OK)
        $data = [
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'sks' => $this->request->getPost('sks')
        ];
        
        // Memuat view hasil dengan data yang sudah diproses
        return view('view-data-matakuliah', $data);
    }
}