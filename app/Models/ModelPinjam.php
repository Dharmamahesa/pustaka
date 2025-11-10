<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPinjam extends Model
{
    protected $table = 'pinjam';
    protected $primaryKey = 'no_pinjam';
    protected $allowedFields = [
        'no_pinjam', 
        'tgl_pinjam', 
        'id_booking', 
        'id_user', 
        'tgl_kembali', 
        'tgl_pengembalian', 
        'status', 
        'total_denda'
    ];

    // Method untuk menyimpan data ke tabel pinjam
    public function simpanPinjam($data)
    {
        return $this->db->table($this->table)->insert($data);
    }

    // Method untuk menyimpan data ke tabel pinjam_detail
    public function simpanDetail($data)
    {
        return $this->db->table('pinjam_detail')->insert($data);
    }

    // Method untuk mengambil data dari tabel tertentu dengan kondisi
    public function getDataWhere($table, $where)
    {
        return $this->db->table($table)->where($where)->get();
    }
    
    // Method untuk update data
    public function updateData($table, $data, $where)
    {
        $this->db->table($table)->where($where)->update($data);
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

    // Method untuk kode otomatis no_pinjam
    public function kodeOtomatis($key, $table)
    {
        // Contoh CI3: $this->db->select('right(no_pinjam,3) as kode', false);
        $builder = $this->db->table($table);
        $builder->select('RIGHT(' . $key . ', 3) as kode', false);
        $builder->orderBy($key, 'DESC');
        $builder->limit(1);
        $query = $builder->get();

        if ($query->getNumRows() <> 0) {
            $data = $query->getRow();
            $kode = intval($data->kode) + 1;
        } else {
            $kode = 1;
        }

        $batas = str_pad($kode, 3, "0", STR_PAD_LEFT);
        $kode_tampil = "PJ" . date('Ymd') . $batas;
        return $kode_tampil;
    }

    /**
     * Join data Peminjaman
     */
    public function joinData()
    {
        $builder = $this->db->table('pinjam p');
        $builder->select('p.*, u.nama, u.email');
        $builder->join('user u', 'u.id = p.id_user');
        return $builder->get();
    }

    /**
     * Join data booking (untuk halaman admin)
     */
    public function getJoinBooking()
    {
        $builder = $this->db->table('booking bo');
        $builder->select('bo.*, u.nama, u.email');
        $builder->join('user u', 'u.id = bo.id_user');
        return $builder->get();
    }
    public function laporanPeminjaman($tgl_mulai, $tgl_akhir)
    {
        $builder = $this->db->table('pinjam p');
        $builder->select('p.*, d.id_buku, d.denda, b.judul_buku, b.pengarang, b.penerbit, u.nama, u.email');
        $builder->join('pinjam_detail d', 'p.no_pinjam = d.no_pinjam');
        $builder->join('buku b', 'b.id = d.id_buku');
        $builder->join('user u', 'u.id = p.id_user');
        $builder->where('p.tgl_pinjam >=', $tgl_mulai);
        $builder->where('p.tgl_pinjam <=', $tgl_akhir);
        
        return $builder->get()->getResultArray();
    }
}