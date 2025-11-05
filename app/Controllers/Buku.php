<?php

namespace App\Controllers;

use App\Models\ModelBuku;
use App\Models\ModelUser;

class Buku extends BaseController
{
    protected $ModelBuku;
    protected $ModelUser;
    
    // Tentukan helper yang akan digunakan di controller ini
    protected $helpers = ['form', 'url', 'session'];

    public function __construct()
    {
        // Inisialisasi Model
        $this->ModelBuku = new ModelBuku();
        $this->ModelUser = new ModelUser();
        
        // Panggil helper cek_login()
        // (Pastikan 'pustaka' helper sudah di-load di app/Config/Autoload.php)
        cek_login();
    }

    /**
     * ==========================================================
     * MANAJEMEN BUKU (CRUD)
     * Diadaptasi dari Pertemuan 12 (hlm. 98-110)
     * ==========================================================
     */

    /**
     * Method index()
     * Menampilkan data buku dan menangani penambahan buku baru
     */
    public function index()
    {
        $data['judul'] = 'Data Buku';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // Mengambil data buku (adaptasi dari ModelBuku->tampil() hlm. 99)
        // Kita gunakan getBuku() yang ada di ModelBuku.php (hlm. 101)
        $data['buku'] = $this->ModelBuku->getBuku()->getResultArray();
        
        // Mengambil data kategori
        $data['kategori'] = $this->ModelBuku->getKategori()->getResultArray();
        
        // Aturan validasi untuk tambah buku (dari hlm. 99-100)
        $rules = [
            'judul_buku' => [
                'label' => 'Judul Buku',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Judul Buku harus diisi',
                    'min_length' => 'Judul buku terlalu pendek'
                ]
            ],
            'id_kategori' => [
                'label' => 'Kategori',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kategori harus dipilih'
                ]
            ],
            'pengarang' => [
                'label' => 'Nama Pengarang',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Nama pengarang harus diisi',
                    'min_length' => 'Nama pengarang terlalu pendek'
                ]
            ],
            'penerbit' => [
                'label' => 'Nama Penerbit',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Nama penerbit harus diisi',
                    'min_length' => 'Nama penerbit terlalu pendek'
                ]
            ],
            'tahun' => [
                'label' => 'Tahun Terbit',
                'rules' => 'required|min_length[3]|max_length[4]|numeric',
                'errors' => [
                    'required' => 'Tahun terbit harus diisi',
                    'min_length' => 'Tahun terbit terlalu pendek',
                    'max_length' => 'Tahun terbit terlalu panjang',
                    'numeric' => 'Hanya boleh diisi angka'
                ]
            ],
            'isbn' => [
                'label' => 'Nomor ISBN',
                'rules' => 'required|min_length[3]|numeric',
                'errors' => [
                    'required' => 'Nama ISBN harus diisi',
                    'min_length' => 'Nama ISBN terlalu pendek',
                    'numeric' => 'Yang anda masukan bukan angka'
                ]
            ],
            'stok' => [
                'label' => 'Stok',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Stok harus diisi',
                    'numeric' => 'Yang anda masukan bukan angka'
                ]
            ]
        ];

        // Logika CI4: Cek apakah method POST dan validasinya lolos
        if ($this->request->getMethod() === 'post' && $this->validate($rules)) {
            // PROSES FORM JIKA VALID (hlm. 100-101)
            
            // Konfigurasi upload gambar
            $gambar = $this->request->getFile('image');
            $namaGambar = '';

            if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
                // Konfigurasi dari modul (hlm. 100)
                if ($gambar->getSize() <= 3000000) { // max_size 3000KB
                    // Cek tipe file (jpg, png, jpeg)
                    $allowedTypes = ['jpg', 'png', 'jpeg'];
                    if (in_array($gambar->getExtension(), $allowedTypes)) {
                        // Nama file baru (hlm. 100)
                        $namaGambar = 'img' . time();
                        $gambar->move(FCPATH . 'assets/img/upload/', $namaGambar);
                    }
                }
            }
            
            // Siapkan data untuk disimpan (hlm. 100-101)
            $dataSimpan = [
                'judul_buku' => $this->request->getPost('judul_buku'),
                'id_kategori' => $this->request->getPost('id_kategori'),
                'pengarang' => $this->request->getPost('pengarang'),
                'penerbit' => $this->request->getPost('penerbit'),
                'tahun_terbit' => $this->request->getPost('tahun'),
                'isbn' => $this->request->getPost('isbn'),
                'stok' => $this->request->getPost('stok'),
                'dipinjam' => 0,
                'dibooking' => 0,
                'image' => $namaGambar
            ];

            // Panggil model untuk menyimpan
            $this->ModelBuku->simpanBuku($dataSimpan);
            
            session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Buku baru berhasil ditambahkan!</div>');
            return redirect()->to('buku');

        } else {
            // TAMPILKAN HALAMAN JIKA GET REQUEST ATAU VALIDASI GAGAL
            
            // Kirim data validasi (jika ada error) ke view
            $data['validation'] = $this->validator;
            
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('buku/index', $data);
            echo view('templates/footer');
        }
    }

    /**
     * Method ubahBuku()
     * Menampilkan form ubah dan menangani update data buku
     * (hlm. 107-110)
     */
    public function ubahBuku($id = null)
    {
        // $id diambil dari URL, bukan segment
        if ($id === null) {
            return redirect()->to('buku');
        }

        $data['judul'] = 'Ubah Data Buku';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // Ambil data buku spesifik (hlm. 107)
        $data['buku'] = $this->ModelBuku->bukuWhere(['id' => $id])->getRowArray();
        
        // Ambil semua kategori (hlm. 108)
        $data['kategori'] = $this->ModelBuku->getKategori()->getResultArray();

        // Aturan validasi (sama seperti 'index', hlm. 108-109)
        $rules = [
            'judul_buku' => [
                'label' => 'Judul Buku',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Judul Buku harus diisi',
                    'min_length' => 'Judul buku terlalu pendek'
                ]
            ],
            'id_kategori' => [
                'label' => 'Kategori',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kategori harus dipilih'
                ]
            ],
            // ... (Tambahkan aturan validasi lain: pengarang, penerbit, tahun, isbn, stok)
            // ... (Sama seperti di method index() hlm. 108-109)
        ];

        // Logika CI4
        if ($this->request->getMethod() === 'post' && $this->validate($rules)) {
            // PROSES FORM JIKA VALID (hlm. 109)
            
            $gambar = $this->request->getFile('image');
            $namaGambar = $this->request->getPost('old_pict'); // Gambar lama

            if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
                // Konfigurasi dari modul (hlm. 109)
                if ($gambar->getSize() <= 3000000) {
                    $allowedTypes = ['jpg', 'png', 'jpeg'];
                    if (in_array($gambar->getExtension(), $allowedTypes)) {
                        // Hapus gambar lama
                        if ($namaGambar != '' && $namaGambar != 'book-default-cover.jpg') {
                            unlink(FCPATH . 'assets/img/upload/' . $namaGambar);
                        }
                        // Nama file baru
                        $namaGambar = 'img' . time();
                        $gambar->move(FCPATH . 'assets/img/upload/', $namaGambar);
                    }
                }
            }

            // Siapkan data untuk di-update (hlm. 109)
            $dataUpdate = [
                'judul_buku' => $this->request->getPost('judul_buku'),
                'id_kategori' => $this->request->getPost('id_kategori'),
                'pengarang' => $this->request->getPost('pengarang'),
                'penerbit' => $this->request->getPost('penerbit'),
                'tahun_terbit' => $this->request->getPost('tahun'),
                'isbn' => $this->request->getPost('isbn'),
                'stok' => $this->request->getPost('stok'),
                'image' => $namaGambar
            ];
            
            $where = ['id' => $this->request->getPost('id')];

            // Panggil model untuk update
            $this->ModelBuku->updateBuku($dataUpdate, $where);
            
            session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Data buku berhasil diubah!</div>');
            return redirect()->to('buku');

        } else {
            // TAMPILKAN HALAMAN JIKA GET REQUEST ATAU VALIDASI GAGAL
            
            // Kirim data validasi (jika ada error) ke view
            $data['validation'] = $this->validator;
            
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('buku/ubah_buku', $data);
            echo view('templates/footer');
        }
    }

    /**
     * Method hapusBuku()
     * (hlm. 110)
     */
    public function hapusBuku($id = null)
    {
        if ($id === null) {
            return redirect()->to('buku');
        }

        $where = ['id' => $id];
        $this->ModelBuku->hapusBuku($where);
        
        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Data buku berhasil dihapus!</div>');
        return redirect()->to('buku');
    }


    /**
     * ==========================================================
     * MANAJEMEN KATEGORI
     * Diadaptasi dari Pertemuan 11 (hlm. 96-97)
     * ==========================================================
     */
     
    /**
     * Method kategori()
     * Menampilkan data kategori dan menangani penambahan kategori baru
     */
    public function kategori()
    {
        $data['judul'] = 'Kategori Buku';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        $data['kategori'] = $this->ModelBuku->getKategori()->getResultArray();

        // Aturan validasi (hlm. 96)
        $rules = [
            'kategori' => [
                'label' => 'Kategori',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Kategori harus diisi'
                ]
            ]
        ];

        // Logika CI4
        if ($this->request->getMethod() === 'post' && $this->validate($rules)) {
            // PROSES FORM JIKA VALID (hlm. 96)
            $dataSimpan = [
                'kategori' => $this->request->getPost('kategori')
            ];
            $this->ModelBuku->simpanKategori($dataSimpan);
            
            session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Kategori baru berhasil ditambahkan!</div>');
            return redirect()->to('buku/kategori');

        } else {
            // TAMPILKAN HALAMAN JIKA GET REQUEST ATAU VALIDASI GAGAL
            
            // Kirim data validasi (jika ada error) ke view
            $data['validation'] = $this->validator;

            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('buku/kategori', $data);
            echo view('templates/footer');
        }
    }

    /**
     * Method hapusKategori()
     * (hlm. 97)
     */
    public function hapusKategori($id_kategori = null)
    {
        // ID Kategori berdasarkan skema DB adalah 'id_kategori'
        // Modul di hlm. 97 menggunakan 'id', ini mungkin typo.
        if ($id_kategori === null) {
            return redirect()->to('buku/kategori');
        }
        
        // Sesuaikan 'where' dengan nama kolom di DB
        $where = ['id_kategori' => $id_kategori];
        $this->ModelBuku->hapusKategori($where);
        
        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Kategori berhasil dihapus!</div>');
        return redirect()->to('buku/kategori');
    }
}