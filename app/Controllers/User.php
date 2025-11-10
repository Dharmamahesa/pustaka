<?php

namespace App\Controllers;

// Import Model CI4
use App\Models\ModelUser;
use App\Models\ModelBooking; // Diperlukan untuk riwayat
use App\Models\ModelPinjam;  // Diperlukan untuk riwayat

class User extends BaseController
{
    protected $modelUser;
    protected $modelBooking;
    protected $modelPinjam;
    
    // Tentukan helper yang akan digunakan di controller ini
    protected $helpers = ['form', 'url', 'session'];

    /**
     * Konstruktor
     * Diadaptasi dari modul (hlm 86)
     */
    public function __construct()
    {
        // Buat instance dari Model
        $this->modelUser = new ModelUser();
        $this->modelBooking = new ModelBooking();
        $this->modelPinjam = new ModelPinjam();
        
        // Panggil helper cek_login (pastikan 'pustaka' helper ada di Autoload.php)
        cek_login();
    }

    /**
     * Method index() (Halaman "Profil Saya")
     * Diadaptasi dari modul (hlm 87)
     * Berfungsi untuk Admin dan Member
     */
    public function index()
    {
        $data['judul'] = 'Profil Saya';
        
        // Mengambil data user dari session CI4
        $email = session()->get('email');
        
        // Menggunakan ModelUser
        $data['user'] = $this->modelUser->cekData(['email' => $email]);

        // Menampilkan view di CI4
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('user/index', $data); // Memanggil app/Views/user/index.php
        echo view('templates/footer');
    }

    /**
     * Method anggota() (Halaman "Data Anggota" - Dilihat Admin)
     * Diadaptasi dari modul (hlm 87)
     */
    public function anggota()
    {
        $data['judul'] = 'Data Anggota';
        $email = session()->get('email');
        $data['user'] = $this->modelUser->cekData(['email' => $email]);
        
        // Mengambil semua data 'member' (role_id = 2)
        $data['anggota'] = $this->modelUser->where('role_id', 2)->findAll();

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('user/anggota', $data); // Memanggil app/Views/user/anggota.php
        echo view('templates/footer');
    }

    /**
     * Method ubahprofil() (Halaman "Ubah Profil")
     * Diadaptasi dari modul (hlm 87-91)
     * Berfungsi untuk GET (menampilkan form) dan POST (memproses data)
     */
    public function ubahprofil() 
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

        // Logika CI4: Jika method BUKAN post, atau JIKA validasi GAGAL
        if ($this->request->getMethod() !== 'post' || !$this->validate($rules)) {
            // Kirim helper validasi ke view (jika ada error)
            $data['validation'] = $this->validator;

            // Tampilkan halaman form ubah profile
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('user/ubah-profile', $data); // Memanggil app/Views/user/ubah-profile.php
            echo view('templates/footer');
        } else {
            // Jika validasi sukses (method POST)
            $nama = $this->request->getPost('nama');
            $email_post = $this->request->getPost('email'); // Email ini (hidden) digunakan sebagai 'where'
            
            $dataToUpdate = [
                'nama' => $nama,
            ];

            // Cek jika ada file gambar diupload
            $upload_image = $this->request->getFile('image');

            if ($upload_image && $upload_image->isValid() && !$upload_image->hasMoved()) {
                
                $gambar_lama = $data['user']['image'];
                
                // Buat nama file random (CI4 style)
                $nama_gambar_baru = $upload_image->getRandomName();
                
                // Pindahkan file ke folder public/assets/img/profile/
                $upload_image->move(FCPATH . 'assets/img/profile/', $nama_gambar_baru);
                
                // Tambahkan nama gambar baru ke data update
                $dataToUpdate['image'] = $nama_gambar_baru;

                // Hapus gambar lama (jika bukan default.jpg)
                if ($gambar_lama != 'default.jpg' && file_exists(FCPATH . 'assets/img/profile/' . $gambar_lama)) {
                     unlink(FCPATH . 'assets/img/profile/' . $gambar_lama);
                }
            }

            // Eksekusi Update ke database
            $this->modelUser->where('email', $email_post)->set($dataToUpdate)->update();

            session()->setFlashdata('pesan', '<div
            class="alert alert-success alert-message" role="alert">Profil
            Berhasil diubah </div>');
            
            return redirect()->to('user');
        }
    }

    /**
     * (FITUR BARU DARI LANGKAH SEBELUMNYA)
     * Halaman Riwayat Peminjaman Member
     */
    public function riwayatPeminjaman()
    {
        $data['judul'] = 'Riwayat Peminjaman Buku';
        $email = session()->get('email');
        $user = $this->modelUser->cekData(['email' => $email]);
        $id_user = $user['id'];
        
        $data['user'] = $user;
        
        // Mengambil data peminjaman yang sedang berlangsung (status='Pinjam')
        $data['pinjam'] = $this->modelPinjam->joinData()
                            ->where(['p.id_user' => $id_user, 'p.status' => 'Pinjam'])
                            ->get()->getResultArray();
                            
        // Mengambil data peminjaman yang sudah selesai (status='Kembali')
        $data['kembali'] = $this->modelPinjam->joinData()
                             ->where(['p.id_user' => $id_user, 'p.status' => 'Kembali'])
                             ->get()->getResultArray();

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('user/riwayat_peminjaman', $data); // Memanggil app/Views/user/riwayat_peminjaman.php
        echo view('templates/footer');
    }
}