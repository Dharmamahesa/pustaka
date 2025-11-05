<?php

namespace App\Controllers;

// Import ModelUser dan BaseController CI4
use App\Models\ModelUser;

class User extends BaseController
{
    protected $modelUser;
    protected $helpers = ['form', 'url', 'session']; // Memuat helper untuk CI4

    /**
     * Konstruktor
     * Diadaptasi dari modul (hlm 86)
     */
    public function __construct()
    {
        // Buat instance dari ModelUser
        $this->modelUser = new ModelUser();
        
        // Panggil helper cek_login (pastikan 'pustaka' helper ada di Autoload.php)
        cek_login();
    }

    /**
     * Method index() (My Profile)
     * Diadaptasi dari modul (hlm 87)
     */
    public function index()
    {
        $data['judul'] = 'Profil Saya';
        $email = session()->get('email');
        $data['user'] = $this->modelUser->cekData(['email' => $email]);

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('user/index', $data); // Ini memanggil view 'app/Views/user/index.php'
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
        
        // Mengambil semua 'admin' (role_id 1)
        $data['anggota'] = $this->modelUser->where('role_id', 1)->findAll();

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('user/anggota', $data); // Ini memanggil view 'app/Views/user/anggota.php'
        echo view('templates/footer');
    }

    /**
     * Method ubahProfil() (GET dan POST)
     * Diadaptasi dari modul (hlm 87-91)
     * NAMA METHOD HARUS SAMA DENGAN ROUTE (ubahprofil)
     */
    public function ubahprofil() // Nama method diubah menjadi lowercase
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

        if (!$this->validate($rules)) {
            // Kirim helper validasi ke view
            $data['validation'] = $this->validator;

            // Tampilkan halaman form ubah profile
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('user/ubah-profile', $data); // Ini memanggil view 'app/Views/user/ubah-profile.php'
            echo view('templates/footer');
        } else {
            // Jika validasi sukses (method POST)
            $nama = $this->request->getPost('nama');
            $email_post = $this->request->getPost('email'); // Email ini (hidden) digunakan sebagai 'where'
            
            $dataToUpdate = [
                'nama' => $nama,
            ];

            $upload_image = $this->request->getFile('image');

            if ($upload_image && $upload_image->isValid() && !$upload_image->hasMoved()) {
                
                $gambar_lama = $data['user']['image'];
                $nama_gambar_baru = 'pro' . time() . '.' . $upload_image->getExtension();
                $upload_image->move(FCPATH . 'assets/img/profile/', $nama_gambar_baru);
                $dataToUpdate['image'] = $nama_gambar_baru;

                if ($gambar_lama != 'default.jpg') {
                    if (file_exists(FCPATH . 'assets/img/profile/' . $gambar_lama)) {
                         unlink(FCPATH . 'assets/img/profile/' . $gambar_lama);
                    }
                }
            }

            // Eksekusi Update
            $this->modelUser->where('email', $email_post)->set($dataToUpdate)->update();

            session()->setFlashdata('pesan', '<div
            class="alert alert-success alert-message" role="alert">Profil
            Berhasil diubah </div>');
            
            return redirect()->to('user');
        }
    }
}