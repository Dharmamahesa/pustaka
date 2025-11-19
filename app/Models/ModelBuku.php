<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelBuku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'id';
    // Pastikan semua kolom ada di sini
    protected $allowedFields = ['judul_buku', 'id_kategori', 'pengarang', 'penerbit', 'tahun_terbit', 'isbn', 'stok', 'dipinjam', 'dibooking', 'image'];

    public function getBuku($id = null)
    {
        if ($id === null) {
            return $this->findAll();
        }
        return $this->getWhere(['id' => $id]);
    }

    public function bukuWhere($where)
    {
        return $this->where($where);
    }

    public function simpanBuku($data = null)
    {
        $this->insert($data);
    }

    public function updateBuku($data = null, $where = null)
    {
        $this->update($where, $data);
    }

    public function hapusBuku($where = null)
    {
        $this->where($where)->delete();
    }

    public function total($field, $where)
    {
        $builder = $this->db->table($this->table);
        $builder->select('SUM(' . $field . ') as total');
        $builder->where($where);
        return $builder->get()->getRow()->total;
    }

    public function getKategori()
    {
        return $this->db->table('kategori')->get()->getResultArray();
    }

    public function kategoriWhere($where)
    {
        return $this->db->table('kategori')->where($where)->get();
    }

    public function simpanKategori($data = null)
    {
        $this->db->table('kategori')->insert($data);
    }

    public function hapusKategori($where = null)
    {
        $this->db->table('kategori')->where($where)->delete();
    }

    public function updateKategori($where = null, $data = null)
    {
        $this->db->table('kategori')->update($data, $where);
    }

    public function joinKategoriBuku($where)
    {
        $builder = $this->db->table('buku');
        $builder->select('buku.id_kategori,kategori.kategori');
        $builder->from('buku');
        $builder->join('kategori', 'kategori.id = buku.id_kategori');
        $builder->where($where);
        return $builder->get();
    }
    
    /**
     * Mengurangi Stok Buku (Saat Booking Selesai)
     * Menggunakan set(..., false) agar tidak dianggap string
     */
    public function kurangiStok($id_buku)
    {
        // Stok berkurang 1, Dibooking bertambah 1
        return $this->db->table($this->table)
            ->set('stok', 'stok - 1', false)
            ->set('dibooking', 'dibooking + 1', false)
            ->where('id', $id_buku)
            ->update();
    }

    /**
     * Membatalkan Booking (Stok Kembali)
     */
    public function kembalikanStok($id_buku)
    {
        // Stok bertambah 1, Dibooking berkurang 1
        return $this->db->table($this->table)
            ->set('stok', 'stok + 1', false)
            ->set('dibooking', 'dibooking - 1', false)
            ->where('id', $id_buku)
            ->update();
    }

    /**
     * Saat Admin Konfirmasi Peminjaman
     * Dibooking berkurang, Dipinjam bertambah (Stok tetap karena sudah dikurangi saat booking)
     */
    public function simpanPinjam($id_buku)
    {
         return $this->db->table($this->table)
            ->set('dibooking', 'dibooking - 1', false)
            ->set('dipinjam', 'dipinjam + 1', false)
            ->where('id', $id_buku)
            ->update();
    }

    /**
     * Saat Buku Dikembalikan ke Perpustakaan
     * Dipinjam berkurang, Stok bertambah
     */
    public function updateStokKembali($id_buku)
    {
        return $this->db->table($this->table)
            ->set('stok', 'stok + 1', false)
            ->set('dipinjam', 'dipinjam - 1', false)
            ->where('id', $id_buku)
            ->update();
    }
}