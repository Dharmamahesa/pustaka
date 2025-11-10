<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelBooking extends Model
{
    // Tentukan tabel utama untuk model ini
    protected $table = 'booking';
    protected $primaryKey = 'id_booking';
    
    // Tentukan kolom yang diizinkan untuk diisi
    protected $allowedFields = ['id_booking', 'tgl_booking', 'batas_ambil', 'id_user'];

    // Method untuk menyimpan data ke tabel tertentu
    public function simpanData($table, $data)
    {
        return $this->db->table($table)->insert($data);
    }

    // Method untuk mengambil data dari tabel tertentu dengan kondisi
    public function getDataWhere($table, $where)
    {
        return $this->db->table($table)->where($where)->get();
    }

    // Method untuk menghapus data
    public function deleteData($where, $table)
    {
        $this->db->table($table)->where($where)->delete();
    }

    // Method untuk mengambil 1 baris data
    public function getDatabyId($table, $where)
    {
        return $this->db->table($table)->where($where)->limit(1)->get()->getRowArray();
    }
    
    // Method untuk update data
    public function updateData($table, $data, $where)
    {
        $this->db->table($table)->where($where)->update($data);
    }

    // Method untuk menghitung jumlah data
    public function getCount($table, $where = null)
    {
        $builder = $this->db->table($table);
        if ($where) {
            $builder->where($where);
        }
        return $builder->countAllResults();
    }

    /**
     * Mengambil data booking dari tabel temp berdasarkan id_user
     */
    public function getDataTemp($where)
    {
        return $this->db->table('temp')->where($where)->get()->getResultArray();
    }

    /**
     * Menghitung total booking berdasarkan id_user
     */
    public function getCountTemp($where)
    {
        return $this->db->table('temp')->where($where)->countAllResults();
    }

    /**
     * Mengambil data join antara booking, booking_detail, dan buku
     */
    public function getBookingDetail($where)
    {
        $builder = $this->db->table('booking b');
        $builder->select('b.id_booking, b.tgl_booking, b.batas_ambil, u.nama, k.nama_kategori, bk.judul_buku, bk.pengarang, bk.penerbit, bk.tahun_terbit');
        $builder->join('booking_detail d', 'd.id_booking = b.id_booking');
        $builder->join('buku bk', 'bk.id = d.id_buku');
        $builder->join('kategori k', 'k.id_kategori = bk.id_kategori');
        $builder->join('user u', 'u.id = b.id_user');
        $builder->where('b.id_user', $where);
        return $builder->get()->getResultArray();
    }
}