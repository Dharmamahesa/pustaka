<?php

namespace App\Controllers;

use App\Models\ModelBuku;
use App\Models\ModelUser;

class Buku extends BaseController
{
    protected $ModelBuku;
    protected $ModelUser;
    
    // Memuat helper yang diperlukan
    protected $helpers = ['form', 'url', 'session'];

    public function __construct()
    {
        $this->ModelBuku = new ModelBuku();
        $this->ModelUser = new ModelUser();
        cek_login();
    }

    /**
     * Halaman Manajemen Data Buku
     */
    public function index()
    {
        $data['judul'] = 'Data Buku';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // Mengambil data buku (sudah array)
        $data['buku'] = $this->ModelBuku->getBuku();
        $data['kategori'] = $this->ModelBuku->getKategori();
        
        // Aturan Validasi Tambah Buku
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
                'rules' => 'max_size[image,3072]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar (maks 3MB)',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Format gambar harus jpg/jpeg/png'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $data['validation'] = $this->validator;
            
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('buku/index', $data);
            echo view('templates/footer');
        } else {
            // Proses Upload Gambar
            $fileGambar = $this->request->getFile('image');
            
            if ($fileGambar->getError() == 4) {
                $namaGambar = 'book-default-cover.jpg';
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

    /**
     * Halaman Manajemen Kategori
     */
    public function kategori()
    {
        $data['judul'] = 'Kategori Buku';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
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

    public function ubahKategori()
    {
        $data['judul'] = 'Ubah Data Kategori';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        $id = $this->request->getUri()->getSegment(3);
        // Menggunakan first() agar aman
        $data['kategori'] = $this->ModelBuku->kategoriWhere(['id' => $id])->getResultArray(); 
        // Jika logic di view ubah_kategori mengharapkan satu row array, gunakan ->getRowArray() atau ->first() pada model.
        // Namun codeigniter 3 style biasanya result array untuk loop. Kita biarkan result array jika view melakukan foreach.

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
            echo view('buku/ubah_kategori', $data);
            echo view('templates/footer');
        } else {
            $data = [
                'kategori' => $this->request->getPost('kategori')
            ];
            
            $this->ModelBuku->updateKategori(['id' => $this->request->getPost('id')], $data);
            session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Kategori Berhasil Diupdate!</div>');
            return redirect()->to(base_url('buku/kategori'));
        }
    }

    /**
     * Method Hapus Buku
     */
    public function hapusBuku($id)
    {
        // Hapus file gambar lama jika bukan default (opsional)
        // $buku = $this->ModelBuku->bukuWhere(['id' => $id])->first();
        // if ($buku['image'] != 'book-default-cover.jpg') { unlink('assets/img/upload/' . $buku['image']); }

        $this->ModelBuku->hapusBuku(['id' => $id]);
        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Data Buku Berhasil Dihapus!</div>');
        return redirect()->to(base_url('buku'));
    }
    
    /**
     * Method Ubah Buku (YANG HILANG TADI)
     */
    public function ubahBuku($id)
    {
        $data['judul'] = 'Ubah Data Buku';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // Ambil data buku berdasarkan ID (gunakan first() agar dapat array 1 baris)
        $data['buku'] = $this->ModelBuku->bukuWhere(['id' => $id])->first();
        $data['kategori'] = $this->ModelBuku->getKategori();

        // Rules validasi
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
                'rules' => 'max_size[image,3072]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar (maks 3MB)',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Format gambar harus jpg/jpeg/png'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $data['validation'] = $this->validator;
            
            echo view('templates/header', $data);
            echo view('templates/sidebar', $data);
            echo view('templates/topbar', $data);
            echo view('buku/ubah_buku', $data);
            echo view('templates/footer');
        } else {
            // Cek jika ada gambar yang akan diupload
            $fileGambar = $this->request->getFile('image');
            
            if ($fileGambar && $fileGambar->getError() == 4) {
                // Jika tidak upload gambar baru, pakai nama gambar lama
                $namaGambar = $this->request->getPost('old_pict');
            } elseif ($fileGambar) {
                // Jika upload gambar baru
                $namaGambar = $fileGambar->getRandomName();
                $fileGambar->move('assets/img/upload', $namaGambar);
            } else {
                $namaGambar = $this->request->getPost('old_pict');
            }

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

            $this->ModelBuku->updateBuku($dataUpdate, ['id' => $id]);
            session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Data Buku Berhasil Diubah!</div>');
            return redirect()->to(base_url('buku'));
        }
    }
}