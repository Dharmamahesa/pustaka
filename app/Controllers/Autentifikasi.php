<?php

namespace App\Controllers;

use App\Models\ModelUser; 
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


class Autentifikasi extends BaseController 
{
    // Deklarasi properti Model
    protected $ModelUser;
    
    // Memuat helper yang dibutuhkan: url, form, session
    protected $helpers = ['url', 'form', 'session'];

    
    /**
     * Method untuk inisialisasi Controller (Pengganti __construct() di CI4)
     * Ini dieksekusi setelah semua service dasar framework siap.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Wajib dipanggil untuk inisialisasi framework
        parent::initController($request, $response, $logger); 

        // [FIX KRITIS]: Instansiasi Model dilakukan di sini, bukan di __construct()
        $this->ModelUser = new ModelUser();
    }
    
    // ==================================================================================
    // LOGIN (Method index) - Halaman 61
    // ==================================================================================
    public function index()
    {
        // Jika statusnya sudah login, arahkan (Halaman 61)
        if (session()->get('email')) {
            return redirect()->to(base_url('admin')); 
        }

        // Aturan Validasi untuk Login (Halaman 61)
        $rules = [
            'email' => ['label' => 'Alamat Email', 'rules' => 'required|trim|valid_email', 'errors' => ['required' => 'Email Harus diisi!!', 'valid_email' => 'Email Tidak Benar!!']],
            'password' => ['label' => 'Password', 'rules' => 'required|trim', 'errors' => ['required' => 'Password Harus diisi']]
        ];

        $data['judul'] = 'Login';
        
        if (!$this->validate($rules)) {
            $data['validation'] = $this->validator;
            
            echo view('templates/aute_header', $data);
            echo view('autentifikasi/login', $data);
            echo view('templates/aute_footer');
            return;
        } 

        $this->_login();
    }

    // Logika Otentikasi Internal (Private Helper) - Halaman 62
    private function _login()
    {
        $email = $this->request->getPost('email', FILTER_SANITIZE_EMAIL);
        $password = $this->request->getPost('password');

        // Panggil ModelUser yang sudah terinisialisasi
        $user = $this->ModelUser->cekData(['email' => $email])->getRowArray(); 

        if ($user) {
            if ($user['is_active'] == 1) {
                if (password_verify($password, $user['password'])) {
                    
                    $data = ['email' => $user['email'], 'role_id' => $user['role_id']];
                    session()->set($data);

                    if ($user['role_id'] == 1) {
                        return redirect()->to(base_url('admin'));
                    } else {
                        return redirect()->to(base_url('user'));
                    }
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Password salah!!</div>');
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">User belum diaktifasi!!</div>');
            }
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Email tidak terdaftar!!</div>');
        }
        
        return redirect()->to(base_url('autentifikasi'));
    }

    // ==================================================================================
    // REGISTRASI - Halaman 85
    // ==================================================================================
    public function registrasi()
    {
        // ... (Logika Registrasi sama seperti sebelumnya, hanya bagian initController yang diubah)
        if (session()->get('email')) {
            return redirect()->to(base_url('user'));
        }

        $rules = [
            'nama' => ['label' => 'Nama Lengkap', 'rules' => 'required', 'errors' => ['required' => 'Nama Belum diisi!!']],
            'email' => ['label' => 'Alamat Email', 'rules' => 'required|trim|valid_email|is_unique[user.email]', 'errors' => ['required' => 'Email Belum diisi!!', 'valid_email' => 'Email Tidak Benar!!', 'is_unique' => 'Email Sudah Terdaftar!']],
            'password1' => ['label' => 'Password', 'rules' => 'required|trim|min_length[3]|matches[password2]', 'errors' => ['min_length' => 'Password Terlalu Pendek', 'matches' => 'Password Tidak Sama!!']],
            'password2' => ['label' => 'Repeat Password', 'rules' => 'required|trim|matches[password1]']
        ];

        $data['judul'] = 'Registrasi Member';

        if (!$this->validate($rules)) {
            $data['validation'] = $this->validator;

            echo view('templates/aute_header', $data);
            echo view('autentifikasi/registrasi', $data);
            echo view('templates/aute_footer');
            return;
        } 

        $dataSimpan = [
            'nama' => htmlspecialchars($this->request->getPost('nama')),
            'email' => htmlspecialchars($this->request->getPost('email')),
            'image' => 'default.jpg',
            'password' => password_hash($this->request->getPost('password1'), PASSWORD_DEFAULT), 
            'role_id' => 2, 
            'is_active' => 1, 
            'tanggal_input' => time()
        ];
        
        $this->ModelUser->simpanData($dataSimpan); 

        session()->setFlashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Selamat!! Akun member anda sudah dibuat. Silahkan Login!</div>');
        return redirect()->to(base_url('autentifikasi'));
    }

    // ... (Sisa method lain seperti blok, gagal, logout) ...
    public function blok() { return view('autentifikasi/blok'); }
    public function gagal() { return view('autentifikasi/gagal'); }
    public function logout() { session()->destroy(); session()->setFlashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Anda berhasil logout.</div>'); return redirect()->to(base_url('autentifikasi')); }
}