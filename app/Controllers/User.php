<?php

namespace App\Controllers;

// Import ModelUser yang sudah kita buat sebelumnya
use App\Models\ModelUser;

class User extends BaseController
{
    protected $modelUser;

    /**
     * Konstruktor
     * Diadaptasi dari modul (hlm 86)
     */
    public function __construct()
    {
        // Buat instance dari ModelUser
        $this->modelUser = new ModelUser();
        
        // Panggil helper cek_login
        cek_login();
    }

    /**
     * Method index() (My Profile)
     * Diadaptasi dari modul (hlm 87)
     */
    public function index()
    {
        $data['judul'] = 'Profil Saya';
        
        // Mengambil data user dari session CI4 (bukan $this->session->userdata)
        $email = session()->get('email');
        
        // Menggunakan ModelUser yang sudah kita buat
        $data['user'] = $this->modelUser->cekData(['email' => $email]);

        // Menampilkan view di CI4 (gunakan echo view() untuk templating)
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('user/index', $data);
        echo view('templates/footer');
    }

    /**
     * Method anggota()
     * Diadaptasi dari modul (hlm 87)
     */
    public function anggota()
    {
        $data['judul'] = 'Data Anggota';
        $email = session()->get('email');
        $data['user'] = $this->modelUser->cekData(['email' => $email]);
        
        // Adaptasi query CI3: $this->db->where('role_id', 1);
        // Catatan: role_id 1 adalah 'administrator', role_id 2 adalah 'member'.
        // Modul menggunakan role_id 1, jadi kita ikuti.
        $data['anggota'] = $this->modelUser->where('role_id', 1)->findAll();

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('user/anggota', $data);
        echo view('templates/footer');
    }

    /**
     * Method ubahProfil() (GET dan POST)
     * Diadaptasi dari modul (hlm 87-91)
     */
    public function ubahProfil()
    {
        $data['judul'] = 'Ubah Profil';
        $email = session()->get('email');
        $data['user'] = $this->modelUser->cekData(['email' => $email]);

        // Aturan validasi CI4
        $rules = [
            'nama' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak Boleh Kosong'
                ]
            ]
        ];

        // Jika validasi gagal (CI4 way)
        // Ini akan gagal saat request GET (halaman dibuka) atau saat POST tapi data salah
        if (!$this->validate($rules)) {
            // Tampilkan halaman form ubah profile
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('user/ubah-profile', $data);
            echo view('templates/footer');
        } else {
            // Jika validasi sukses (method POST)
            $nama = $this->request->getPost('nama');
            $email = $this->request->getPost('email'); // Email ini (hidden) digunakan sebagai 'where'
            
            // Siapkan data untuk update
            $dataToUpdate = [
                'nama' => $nama,
            ];

            // Cek jika ada file gambar diupload (CI4 way)
            $upload_image = $this->request->getFile('image');

            if ($upload_image->isValid() && !$upload_image->hasMoved()) {
                
                $gambar_lama = $data['user']['image'];
                
                // Buat nama file baru (sesuai modul: 'pro' + time)
                $nama_gambar_baru = 'pro' . time() . '.' . $upload_image->getExtension();
                
                // Pindahkan file ke folder (CI4 way)
                // Pastikan folder 'assets/img/profile/' ada dan writable
                $upload_image->move(FCPATH . 'assets/img/profile/', $nama_gambar_baru);
                
                // Tambahkan gambar baru ke data update
                $dataToUpdate['image'] = $nama_gambar_baru;

                // Hapus gambar lama (jika bukan default)
                if ($gambar_lama != 'default.jpg') {
                    // Hapus file (CI4 FCPATH lebih aman)
                    unlink(FCPATH . 'assets/img/profile/' . $gambar_lama);
                }
            }

            // Eksekusi Update (CI4 Model way)
            // Ini lebih efisien daripada 2x query seperti di modul
            $this->modelUser->where('email', $email)->set($dataToUpdate)->update();

            // Set flashdata (CI4 way)
            session()->setFlashdata('pesan', '<div
            class="alert alert-success alert-message" role="alert">Profil
            Berhasil diubah </div>');
            
            // Redirect (CI4 way)
            return redirect()->to('user');
        }
    }
}