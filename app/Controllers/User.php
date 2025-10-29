<?php

namespace App\Controllers;

use App\Models\ModelUser; // Pastikan ModelUser sudah dibuat

// Controller User harus memperluas BaseController
class User extends BaseController 
{
    protected $ModelUser;
    
    // Memuat helper yang dibutuhkan: url, form, session, dan file
    protected $helpers = ['url', 'form', 'session', 'filesystem'];

    public function __construct()
    {
        // Inisialisasi ModelUser
        parent::__construct();
        $this->ModelUser = new ModelUser();

        // Panggil helper cek_login() dari pustaka_helper.php (asumsi sudah dibuat di pertemuan 10)
        // helper('pustaka'); // Uncomment baris ini setelah Anda membuat pustaka_helper.php
        // cek_login(); // Uncomment baris ini setelah Anda membuat pustaka_helper.php
    }

    // ==================================================================================
    // 1. HALAMAN MY PROFILE (INDEX) - Halaman 89
    // ==================================================================================
    public function index()
    {
        $data['judul'] = 'Profil Saya'; // Halaman 89
        
        // Ambil data user dari session (Halaman 89)
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')])->getRowArray();
        
        // Memuat view templates/header.php, templates/sidebar.php, templates/topbar.php, user/index.php, templates/footer.php
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('user/index', $data); // View My Profile
        echo view('templates/footer');
    }

    // ==================================================================================
    // 2. DATA ANGGOTA (ANGGOTA) - Halaman 89
    // ==================================================================================
    public function anggota()
    {
        $data['judul'] = 'Data Anggota'; // Halaman 89
        
        // Ambil data user yang sedang login (Halaman 89)
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')])->getRowArray();
        
        // Ambil data anggota (role_id = 2) 
        $data['anggota'] = $this->db->table('user')->getWhere(['role_id' => 2])->getResultArray(); // Asumsi role_id 2 adalah member

        // Memuat view 
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('user/anggota', $data); // View Data Anggota
        echo view('templates/footer');
    }

    // ==================================================================================
    // 3. UBAH PROFIL (UBAHPROFIL) - Halaman 90
    // ==================================================================================
    public function ubahProfil()
    {
        $data['judul'] = 'Ubah Profil'; // Halaman 90
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')])->getRowArray(); // Halaman 90
        
        // 1. Mendefinisikan Aturan Validasi (Halaman 90)
        $rules = [
            'nama' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|trim',
                'errors' => ['required' => 'Nama tidak Boleh Kosong']
            ]
        ];

        // 2. Jalankan Validasi
        if (!$this->validate($rules)) {
            // Jika validasi GAGAL, tampilkan form ubah-profile (Halaman 90)
            $data['validation'] = $this->validator;
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('user/ubah-profile', $data); // View Ubah Profile
            echo view('templates/footer');
            return;
        } 

        // 3. Jika validasi BERHASIL, proses update
        $nama  = $this->request->getPost('nama', FILTER_SANITIZE_STRING); // Halaman 90
        $email = $this->request->getPost('email'); // Email tidak boleh diubah (readonly)

        // Logika Upload Gambar (Halaman 90)
        $upload_image = $this->request->getFile('image');
        
        if ($upload_image->isValid() && !$upload_image->hasMoved()) 
        {
            // Cek apakah ada gambar yang diupload (Halaman 90)
            if ($upload_image->getName() != '') {
                
                $gambar_lama = $data['user']['image'];
                
                // Jika gambar lama bukan default.jpg, hapus gambar lama (Halaman 90)
                if ($gambar_lama != 'default.jpg') {
                    unlink('assets/img/profile/' . $gambar_lama);
                }

                // Generate nama file baru
                $gambar_baru = $upload_image->getRandomName();
                
                // Pindahkan file ke folder tujuan
                $upload_image->move('assets/img/profile', $gambar_baru);
                
                // Update database dengan nama file baru
                $this->db->table('user')->set('image', $gambar_baru);
            }
        }
        
        // Update nama user (Halaman 90)
        $this->db->table('user')->set('nama', $nama);
        $this->db->table('user')->where('email', $email)->update(); // Kondisi update

        // Set pesan sukses dan redirect (Halaman 91)
        session()->setFlashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Profil Berhasil diubah </div>');
        return redirect()->to(base_url('user')); // Arahkan ke halaman My Profile
    }
}