<?php

namespace App\Controllers;

use App\Models\ModelUser; // Import ModelUser

class Autentifikasi extends BaseController
{
    protected $ModelUser;
    
    // Muat helper yang dibutuhkan untuk controller ini
    protected $helpers = ['form', 'url', 'session'];

    public function __construct()
    {
        // Inisialisasi ModelUser
        $this->ModelUser = new ModelUser();
    }

    /**
     * Halaman Login (Method index)
     * Adaptasi dari Pertemuan 9 (hlm. 59-60)
     */
    public function index()
    {
        // Jika statusnya sudah login (CI4 session)
        if (session()->get('email')) {
            return redirect()->to('user'); // CI4 redirect
        }

        // Aturan validasi (CI4 style)
        $rules = [
            'email' => [
                'label' => 'Alamat Email',
                'rules' => 'required|trim|valid_email',
                'errors' => [
                    'required' => 'Email Harus diisi!!',
                    'valid_email' => 'Email Tidak Benar!!'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Password Harus diisi'
                ]
            ]
        ];

        // Cek validasi
        if (!$this->validate($rules)) {
            // Jika validasi gagal, tampilkan form login
            $data['judul'] = 'Login';
            
            // Tampilkan view (CI4 way)
            echo view('autentifikasi/aute_header', $data);
            echo view('autentifikasi/login', $data);
            echo view('autentifikasi/aute_footer');
        } else {
            // Jika validasi sukses, jalankan method _login
            // Kita return karena _login akan mengembalikan redirect
            return $this->_login();
        }
    }

    /**
     * Logika internal untuk memproses login
     * Adaptasi dari Pertemuan 9 (hlm. 60-62)
     */
    private function _login()
    {
        // Ambil input (CI4 request)
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // *** INI ADALAH PERBAIKANNYA ***
        // Panggil ModelUser. Fungsi cekData() sudah mengembalikan array.
        $user = $this->ModelUser->cekData(['email' => $email]);

        // Jika usernya ada
        if ($user) {
            // Jika user sudah aktif
            if ($user['is_active'] == 1) {
                // Cek password
                if (password_verify($password, $user['password'])) {
                    // Siapkan data session
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    // Set session (CI4 way)
                    session()->set($data);

                    // Cek role ID
                    if ($user['role_id'] == 1) {
                        return redirect()->to('admin'); // CI4 redirect
                    } else {
                        if ($user['image'] == 'default.jpg') {
                            session()->setFlashdata('pesan', '<div class="alert alert-info alert-message" role="alert">Silahkan Ubah Profile Anda untuk Ubah Photo Profil</div>');
                        }
                        return redirect()->to('user'); // CI4 redirect
                    }
                } else {
                    // Password salah
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Password salah!!</div>');
                    return redirect()->to('autentifikasi');
                }
            } else {
                // User belum diaktifasi
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">User belum diaktifasi!!</div>');
                return redirect()->to('autentifikasi');
            }
        } else {
            // Email tidak terdaftar
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Email tidak terdaftar!!</div>');
            return redirect()->to('autentifikasi');
        }
    }

    /**
     * Halaman Registrasi
     * Adaptasi dari Pertemuan 10 (hlm. 83-85)
     */
    public function registrasi()
    {
        // Jika sudah login, tidak bisa registrasi
        if (session()->get('email')) {
            return redirect()->to('user');
        }

        // Aturan validasi (CI4 style)
        $rules = [
            'nama' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama Belum diisi!!'
                ]
            ],
            'email' => [
                'label' => 'Alamat Email',
                'rules' => 'required|trim|valid_email|is_unique[user.email]',
                'errors' => [
                    'required' => 'Email Belum diisi!!',
                    'valid_email' => 'Email Tidak Benar!!',
                    'is_unique' => 'Email Sudah Terdaftar!'
                ]
            ],
            'password' => [ // Diubah dari 'password1'
                'label' => 'Password',
                'rules' => 'required|trim|min_length[3]|matches[password2]',
                'errors' => [
                    'required' => 'Password harus diisi!',
                    'min_length' => 'Password Terlalu Pendek',
                    'matches' => 'Password Tidak Sama!!'
                ]
            ],
            'password2' => [
                'label' => 'Repeat Password',
                'rules' => 'required|trim|matches[password]', // Diubah dari 'matches[password1]'
                'errors' => [
                    'required' => 'Ulangi Password harus diisi!',
                    'matches' => 'Password Tidak Sama!!'
                ]
            ]
        ];

        // Cek validasi
        if (!$this->validate($rules)) {
            // Jika validasi gagal, tampilkan form registrasi
            $data['judul'] = 'Registrasi Member';
            $data['validation'] = $this->validator; // Kirim error validasi ke view
            
            echo view('autentifikasi/aute_header', $data);
            echo view('autentifikasi/registrasi', $data);
            echo view('autentifikasi/aute_footer');
        } else {
            // Jika validasi sukses
            // Siapkan data untuk disimpan (hlm. 85)
            $data = [
                'nama' => htmlspecialchars($this->request->getPost('nama')),
                'email' => htmlspecialchars($this->request->getPost('email')),
                'image' => 'default.jpg',
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT), // Ambil 'password'
                'role_id' => 2,
                'is_active' => 0, // 0 = belum aktif
                'tanggal_input' => time()
            ];

            // Simpan ke database (menggunakan model CI4)
            $this->ModelUser->simpanData($data);

            session()->setFlashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Selamat!! akun member anda sudah dibuat. Silahkan Aktivasi Akun anda</div>');
            return redirect()->to('autentifikasi');
        }
    }

    /**
     * Halaman Akses Diblok
     * Adaptasi dari Pertemuan 10 (hlm. 83)
     */
    public function blok()
    {
        echo view('autentifikasi/blok');
    }

    /**
     * Halaman Gagal (misal: aktivasi)
     * Adaptasi dari Pertemuan 10 (hlm. 83)
     */
    public function gagal()
    {
        echo view('autentifikasi/gagal');
    }

    /**
     * Method Logout
     * (Tidak ada di modul, tapi diperlukan oleh view)
     */
    public function logout()
    {
        // Hapus data session (CI4 way)
        session()->remove(['email', 'role_id']);
        
        // Set pesan flashdata
        session()->setFlashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Anda telah logout!</div>');
        
        // Redirect ke halaman login
        return redirect()->to('autentifikasi');
    }

    /**
     * Method Lupa Password (Placeholder)
     * (Tidak ada di modul, tapi direferensikan di view)
     */
    public function lupaPassword()
    {
        $data['judul'] = 'Lupa Password';
        
        echo view('autentifikasi/aute_header', $data);
        // Buat view 'lupa-password.php' jika diperlukan
        echo "Halaman Lupa Password belum dibuat."; 
        echo view('autentifikasi/aute_footer');
    }
}