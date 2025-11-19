<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPinjam extends Model
{
    protected $table = 'pinjam';
    protected $primaryKey = 'no_pinjam';
    protected $allowedFields = [
        'no_pinjam', 'tgl_pinjam', 'id_booking', 'id_user', 
        'tgl_kembali', 'tgl_pengembalian', 'status', 'total_denda'
    ];

    /**
     * Mengembalikan Query Builder untuk join tabel pinjam, booking, dan user.
     * Tidak diakhiri dengan get() agar bisa di-chaining di Controller.
     */
    public function joinData()
    {
        $builder = $this->db->table('pinjam p');
        $builder->select('*');
        $builder->join('booking b', 'b.id_booking = p.id_booking');
        $builder->join('user u', 'u.id = p.id_user');
        
        return $builder; // Mengembalikan builder, bukan hasil execute
    }

    /**
     * Khusus untuk admin melihat daftar booking
     */
    public function getJoinBooking()
    {
        $builder = $this->db->table('booking b');
        $builder->join('user u', 'u.id = b.id_user');
        return $builder->get();
    }

    public function simpanPinjam($data)
    {
        return $this->insert($data);
    }

    public function simpanDetail($data)
    {
        return $this->db->table('pinjam_detail')->insert($data);
    }

    // Method pembantu generik (adaptasi dari CI3)
    public function getDatabyId($table, $where)
    {
        return $this->db->table($table)->where($where)->get()->getRowArray();
    }

    public function getDataWhere($table, $where)
    {
        return $this->db->table($table)->where($where)->get();
    }

    public function updateData($table, $data, $where)
    {
        return $this->db->table($table)->where($where)->update($data);
    }
    
    public function deleteData($where, $table)
    {
        return $this->db->table($table)->where($where)->delete();
    }

    /**
     * Kode Otomatis untuk no_pinjam
     */
    public function kodeOtomatis($key, $table)
    {
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
        $kode_tampil = date('dmY') . $batas; // Format contoh: 18112025001
        return $kode_tampil;
    }

    /**
     * Query khusus untuk Laporan Peminjaman
     */
    public function laporanPeminjaman($tgl_mulai, $tgl_akhir) 
    {
         $builder = $this->db->table('pinjam p');
         $builder->join('user u', 'u.id = p.id_user');
         $builder->join('pinjam_detail pd', 'pd.no_pinjam = p.no_pinjam');
         $builder->join('buku b', 'b.id = pd.id_buku');
         $builder->where('p.tgl_pinjam >=', $tgl_mulai);
         $builder->where('p.tgl_pinjam <=', $tgl_akhir);
         return $builder->get()->getResultArray();
    }
}