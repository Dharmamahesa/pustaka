<?php

namespace App\Controllers;

use App\Models\ModelBuku;
use App\Models\ModelUser;

class Buku extends BaseController
{
    protected $ModelBuku;
    protected $ModelUser;
    // Load helper form dan url
    protected $helpers = ['form', 'url', 'session'];

    public function __construct()
    {
        $this->ModelBuku = new ModelBuku();
        $this->ModelUser = new ModelUser();
        cek_login();
    }

    // Manajemen Buku
    public function index()
    {
        $data['judul'] = 'Data Buku';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // PERBAIKAN: Hapus ->getResultArray()
        $data['buku'] = $this->ModelBuku->getBuku();
        
        // PERBAIKAN: Hapus ->getResultArray() jika getKategori() return array.
        // (Cek ModelBuku, jika getKategori() pakai getResultArray() di dalamnya, maka di sini langsung pakai)
        // Asumsi ModelBuku::getKategori() mengembalikan result array
        $data['kategori'] = $this->ModelBuku->getKategori();
        
        // Aturan validasi untuk tambah buku
        $rules = [
            'judul_buku' => [
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Judul Buku harus diisi',
                    'min_length' => 'Judul buku terlalu pendek'
                ]
            ],
            'id_kategori' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kategori harus dipilih',
                ]
            ],
            'pengarang' => [
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Nama pengarang harus diisi',
                    'min_length' => 'Nama pengarang terlalu pendek'
                ]
            ],
            'penerbit' => [
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Nama penerbit harus diisi',
                    'min_length' => 'Nama penerbit terlalu pendek'
                ]
            ],
            'tahun' => [
                'rules' => 'required|numeric|min_length[3]|max_length[4]',
                'errors' => [
                    'required' => 'Tahun terbit harus diisi',
                    'numeric' => 'Hanya boleh diisi angka',
                    'min_length' => 'Tahun terlalu pendek',
                    'max_length' => 'Tahun terlalu panjang'
                ]
            ],
            'isbn' => [
                'rules' => 'required|min_length[3]|numeric',
                'errors' => [
                    'required' => 'Nomor ISBN harus diisi',
                    'min_length' => 'Nama ISBN terlalu pendek',
                    'numeric' => 'Yang anda masukan bukan angka'
                ]
            ],
            'stok' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Stok harus diisi',
                    'numeric' => 'Yang anda masukan bukan angka'
                ]
            ],
            'image' => [
                'rules' => 'max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar (maks 1MB)',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Format gambar harus jpg/jpeg/png'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            // Jika validasi gagal, tampilkan kembali view dengan error
            $data['validation'] = $this->validator;
            
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('buku/index', $data);
            echo view('templates/footer');
        } else {
            // Jika validasi sukses, simpan data
            
            // Upload gambar
            $fileGambar = $this->request->getFile('image');
            if ($fileGambar->getError() == 4) {
                $namaGambar = 'book-default-cover.jpg'; // Gambar default jika tidak ada upload
            } else {
                $namaGambar = $fileGambar->getRandomName();
                $fileGambar->move('assets/img/upload', $namaGambar);
            }

            $data = [
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

            $this->ModelBuku->simpanBuku($data);
            session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Data Buku Berhasil Ditambahkan!</div>');
            return redirect()->to(base_url('buku'));
        }
    }

    // --- MANAJEMEN KATEGORI ---

    public function kategori()
    {
        $data['judul'] = 'Kategori Buku';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // PERBAIKAN: Sesuaikan dengan return type di ModelBuku
        $data['kategori'] = $this->ModelBuku->getKategori(); 

        $rules = [
            'kategori' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Kategori harus diisi'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $data['validation'] = $this->validator;
            
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('buku/kategori', $data);
            echo view('templates/footer');
        } else {
            $this->ModelBuku->simpanKategori(['kategori' => $this->request->getPost('kategori')]);
            session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Kategori Berhasil Ditambahkan!</div>');
            return redirect()->to(base_url('buku/kategori'));
        }
    }

    public function hapusKategori($id)
    {
        $this->ModelBuku->hapusKategori(['id' => $id]);
        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Kategori Berhasil Dihapus!</div>');
        return redirect()->to(base_url('buku/kategori'));
    }
    
    public function hapusBuku($id)
    {
        $this->ModelBuku->hapusBuku(['id' => $id]);
        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Data Buku Berhasil Dihapus!</div>');
        return redirect()->to(base_url('buku'));
    }
    
    // Tambahkan method ubahBuku jika diperlukan...
}