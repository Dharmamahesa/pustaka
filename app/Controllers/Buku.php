<?php

namespace App\Controllers;

use App\Models\ModelBuku; // Import ModelBuku
use App\Models\ModelUser; // Import ModelUser

// Buku harus memperluas BaseController
class Buku extends BaseController 
{
    protected $ModelBuku;
    protected $ModelUser;
    
    // Memuat helper yang dibutuhkan: url, form, session, filesystem (untuk upload)
    protected $helpers = ['url', 'form', 'session', 'filesystem'];

    public function __construct()
    {
        // Inisialisasi Model
        parent::__construct();
        $this->ModelBuku = new ModelBuku();
        $this->ModelUser = new ModelUser();
        
        // Panggil helper cek_login() dari pustaka_helper.php (asumsi sudah dibuat)
        // helper('pustaka'); // Uncomment jika pustaka_helper.php sudah dibuat
        // cek_login(); // Uncomment jika pustaka_helper.php sudah dibuat
    }

    // ==================================================================================
    // 1. MANAJEMEN BUKU: TAMPIL DATA DAN TAMBAH BUKU (INDEX) - Halaman 98-102
    // ==================================================================================
    public function index()
    {
        $data['judul'] = 'Data Buku'; // Halaman 99
        
        // Ambil data user yang sedang login (Halaman 99)
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')])->getRowArray();
        
        // Ambil semua data buku dan kategori (Halaman 99)
        $data['buku'] = $this->ModelBuku->getBuku()->getResultArray();
        $data['kategori'] = $this->ModelBuku->getKategori()->getResultArray();
        
        // Aturan Validasi (Halaman 99-100)
        $rules = [
            'judul_buku' => ['label' => 'Judul Buku', 'rules' => 'required|min_length[3]', 'errors' => ['required' => 'Judul Buku harus diisi']],
            'id_kategori' => ['label' => 'Kategori', 'rules' => 'required', 'errors' => ['required' => 'Kategori harus diisi']],
            'pengarang' => ['label' => 'Nama Pengarang', 'rules' => 'required|min_length[3]', 'errors' => ['required' => 'Nama pengarang harus diisi']],
            'penerbit' => ['label' => 'Nama Penerbit', 'rules' => 'required|min_length[3]', 'errors' => ['required' => 'Nama penerbit harus diisi']],
            'tahun' => ['label' => 'Tahun Terbit', 'rules' => 'required|min_length[3]|max_length[4]|numeric', 'errors' => ['required' => 'Tahun terbit harus diisi', 'numeric' => 'Hanya boleh diisi angka']],
            'isbn' => ['label' => 'Nomor ISBN', 'rules' => 'required|min_length[3]|numeric', 'errors' => ['required' => 'Nomor ISBN harus diisi', 'numeric' => 'Yang anda masukan bukan angka']],
            'stok' => ['label' => 'Stok', 'rules' => 'required|numeric', 'errors' => ['required' => 'Stok harus diisi', 'numeric' => 'Yang anda masukan bukan angka']],
        ];

        // 2. Jalankan Validasi dan Proses Upload (Halaman 100)
        if (!$this->validate($rules)) {
            // Jika validasi GAGAL, tampilkan form dengan pesan error
            $data['validation'] = $this->validator;

            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('buku/index', $data); // View Data Buku
            echo view('templates/footer');
            return;
        } 

        // 3. Jika validasi BERHASIL, proses data
        $image = $this->request->getFile('image'); // Ambil file upload

        if ($image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName(); // Buat nama unik
            $image->move('assets/img/upload', $newName); // Pindahkan ke assets/img/upload
            $gambar = $newName;
        } else {
            $gambar = 'book-default-cover.jpg'; // Gambar default jika tidak ada upload
        }
        
        $dataSimpan = [
            'judul_buku' => $this->request->getPost('judul_buku', FILTER_SANITIZE_STRING),
            'id_kategori' => $this->request->getPost('id_kategori'),
            'pengarang' => $this->request->getPost('pengarang', FILTER_SANITIZE_STRING),
            'penerbit' => $this->request->getPost('penerbit', FILTER_SANITIZE_STRING),
            'tahun_terbit' => $this->request->getPost('tahun'),
            'isbn' => $this->request->getPost('isbn'),
            'stok' => $this->request->getPost('stok'),
            'dipinjam' => 0,
            'dibooking' => 0,
            'image' => $gambar
        ];
        
        $this->ModelBuku->simpanBuku($dataSimpan); // Simpan ke database (Halaman 101)
        session()->setFlashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Data Buku Berhasil Ditambahkan</div>');
        return redirect()->to(base_url('buku'));
    }

    // ==================================================================================
    // 2. MANAJEMEN BUKU: UBAH DATA BUKU - Halaman 107-112
    // ==================================================================================
    public function ubahBuku()
    {
        $data['judul'] = 'Ubah Data Buku'; // Halaman 107
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')])->getRowArray();
        
        // Ambil ID dari URI segmen (Halaman 107)
        $id_buku = $this->request->uri->getSegment(3);

        $data['buku'] = $this->ModelBuku->bukuWhere(['id' => $id_buku])->getRowArray();
        $data['kategori'] = $this->ModelBuku->getKategori()->getResultArray();
        
        // Validasi dan Proses sama seperti method index() (Halaman 108-109)
        $rules = [
            'judul_buku' => ['label' => 'Judul Buku', 'rules' => 'required|min_length[3]'],
            // ... (sisa aturan validasi dihilangkan untuk keringkasan, tapi harus disertakan)
        ];

        if (!$this->validate($rules)) {
            // Jika validasi GAGAL, tampilkan form dengan data yang ada
            $data['validation'] = $this->validator;
            
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('buku/ubah_buku', $data); // View Ubah Buku
            echo view('templates/footer');
            return;
        } 

        // Proses Update Data dan Gambar (Halaman 109-111)
        $old_pict = $this->request->getPost('old_pict');
        $image = $this->request->getFile('image');
        $gambar = $old_pict;

        if ($image->isValid() && !$image->hasMoved() && $image->getName() != '') {
            // Ada gambar baru di-upload (Halaman 109)
            if ($old_pict != 'book-default-cover.jpg') {
                unlink('assets/img/upload/' . $old_pict); // Hapus gambar lama
            }

            $newName = $image->getRandomName(); 
            $image->move('assets/img/upload', $newName);
            $gambar = $newName;
        }

        $dataUpdate = [
            'judul_buku' => $this->request->getPost('judul_buku', FILTER_SANITIZE_STRING),
            'id_kategori' => $this->request->getPost('id_kategori'),
            // ... (sisa data update)
            'image' => $gambar
        ];
        
        $where = ['id' => $this->request->getPost('id')];
        $this->ModelBuku->updateBuku($dataUpdate, $where); // Update ke database (Halaman 112)
        session()->setFlashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Data Buku Berhasil Diubah</div>');
        return redirect()->to(base_url('buku'));
    }

    // ==================================================================================
    // 3. MANAJEMEN BUKU: HAPUS DATA BUKU - Halaman 112
    // ==================================================================================
    public function hapusBuku()
    {
        // Ambil ID dari URI segmen ke-3 (Halaman 112)
        $where = ['id' => $this->request->uri->getSegment(3)];
        
        // Hapus data dari database (Halaman 112)
        $this->ModelBuku->hapusBuku($where); 
        session()->setFlashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Data Buku Berhasil Dihapus</div>');
        return redirect()->to(base_url('buku'));
    }

    // ==================================================================================
    // 4. MANAJEMEN KATEGORI: TAMPIL DAN TAMBAH KATEGORI - Halaman 96-99
    // ==================================================================================
    public function kategori()
    {
        $data['judul'] = 'Kategori Buku'; // Halaman 96
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')])->getRowArray();
        $data['kategori'] = $this->ModelBuku->getKategori()->getResultArray(); // Ambil semua kategori
        
        // Aturan Validasi untuk tambah kategori (Halaman 96)
        $rules = [
            'kategori' => ['label' => 'Kategori', 'rules' => 'required', 'errors' => ['required' => 'Nama Kategori harus diisi']],
        ];

        if (!$this->validate($rules)) {
            // Jika validasi GAGAL, tampilkan form dengan pesan error
            $data['validation'] = $this->validator;
            
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('buku/kategori', $data); // View Kategori
            echo view('templates/footer');
            return;
        } 

        // Jika validasi BERHASIL, simpan data (Halaman 98-99)
        $dataSimpan = ['nama_kategori' => $this->request->getPost('kategori', FILTER_SANITIZE_STRING)];
        
        $this->ModelBuku->simpanKategori($dataSimpan);
        session()->setFlashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Data Kategori Berhasil Ditambahkan</div>');
        return redirect()->to(base_url('buku/kategori'));
    }

    // ==================================================================================
    // 5. MANAJEMEN KATEGORI: HAPUS KATEGORI - Halaman 99
    // ==================================================================================
    public function hapusKategori()
    {
        // Ambil ID dari URI segmen ke-3 (Halaman 99)
        $where = ['id_kategori' => $this->request->uri->getSegment(3)]; 
        
        $this->ModelBuku->hapusKategori($where); // Hapus data
        session()->setFlashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Data Kategori Berhasil Dihapus</div>');
        return redirect()->to(base_url('buku/kategori'));
    }
}