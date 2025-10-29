<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelBuku extends Model
{
    // ==================================================================================
    // KONFIGURASI MODEL
    // ==================================================================================
    protected $table = 'buku'; // Tabel utama yang dimanipulasi
    protected $primaryKey = 'id';
    
    // Field yang diizinkan untuk diisi (digunakan untuk insert/update)
    protected $allowedFields = [
        'judul_buku', 'id_kategori', 'pengarang', 'penerbit', 'tahun_terbit', 
        'isbn', 'stok', 'dipinjam', 'dibooking', 'image'
    ];


    // ==================================================================================
    // MANAJEMEN BUKU (Halaman 47)
    // ==================================================================================

    // Menampilkan semua data buku (getBuku/tampil)
    public function getBuku()
    {
        return $this->db->table($this->table)->get(); 
    }

    // Menampilkan data buku berdasarkan kondisi (bukuWhere)
    public function bukuWhere($where)
    {
        return $this->db->table($this->table)->getWhere($where);
    }
    
    // Menyimpan data buku (simpanBuku)
    public function simpanBuku($data = null)
    {
        return $this->db->table($this->table)->insert($data);
    }
    
    // Mengubah data buku (updateBuku)
    public function updateBuku($data = null, $where = null)
    {
        return $this->db->table($this->table)->update($data, $where);
    }
    
    // Menghapus data buku (hapusBuku)
    public function hapusBuku($where = null)
    {
        return $this->db->table($this->table)->delete($where);
    }

    // Menghitung total stok/dipinjam/dibooking (total)
    public function total($field, $where = [])
    {
        $builder = $this->db->table($this->table)->selectSum($field);
        
        if (!empty($where)) {
            $builder->where($where);
        }
        
        return $builder->get(); 
    }

    // Join tabel buku dan kategori (joinKategoriBuku)
    // Digunakan saat update data buku
    public function joinKategoriBuku($where)
    {
        return $this->db->table('buku')
                        // Perlu diperhatikan: nama kolom kategori di tabel kategori adalah 'nama_kategori' atau 'kategori'
                        // Saya menggunakan 'nama_kategori' sesuai SQL yang kita buat, dan alias 'kategori'
                        ->select('buku.id_kategori, kategori.nama_kategori as kategori')
                        ->join('kategori', 'kategori.id_kategori = buku.id_kategori') // Sesuaikan ID kolom
                        ->where($where)
                        ->get();
    }


    // ==================================================================================
    // MANAJEMEN KATEGORI (Halaman 50)
    // ==================================================================================
    // Perhatikan: Model ini menggunakan tabel terpisah ('kategori') untuk manajemen kategori

    // Menampilkan semua kategori (getKategori)
    public function getKategori()
    {
        return $this->db->table('kategori')->get();
    }
    
    // Menampilkan kategori berdasarkan kondisi (kategoriWhere)
    public function kategoriWhere($where)
    {
        return $this->db->table('kategori')->getWhere($where);
    }

    // Menyimpan data kategori (simpanKategori)
    public function simpanKategori($data = null)
    {
        return $this->db->table('kategori')->insert($data);
    }

    // Menghapus data kategori (hapusKategori)
    public function hapusKategori($where = null)
    {
        return $this->db->table('kategori')->delete($where);
    }

    // Update data kategori (updateKategori)
    public function updateKategori($data = null, $where = null)
    {
        return $this->db->table('kategori')->update($data, $where);
    }
}