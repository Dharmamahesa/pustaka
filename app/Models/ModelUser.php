<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelUser extends Model
{
    // Tentukan tabel yang akan digunakan
    protected $table            = 'user';
    
    // Tentukan primary key
    protected $primaryKey       = 'id';
    
    // Tentukan kolom yang diizinkan untuk diisi
    protected $allowedFields    = [
        'nama', 
        'email', 
        'image', 
        'password', 
        'role_id', 
        'is_active', 
        'tanggal_input'
    ];

    // Kita tidak menggunakan timestamps bawaan CI4
    protected $useTimestamps = false;

    /**
     * Menyimpan data user baru (untuk registrasi)
     * Adaptasi dari hlm. 85
     */
    public function simpanData($data = null)
    {
        // CI4: Cukup gunakan $this->insert()
        // Ini setara dengan $this->db->insert('user', $data);
        return $this->insert($data);
    }

    /**
     * Mengambil 1 baris data user berdasarkan kondisi
     * Adaptasi dari hlm. 85
     * * Menggunakan ->getRowArray() untuk mengembalikan array,
     * agar tidak error di controller atau view.
     */
    public function cekData($where = null)
    {
        // Ini setara dengan $this->db->get_where('user', $where);
        return $this->where($where)->get()->getRowArray();
    }

    /**
     * Mengambil 1 baris data user berdasarkan kondisi
     * Adaptasi dari hlm. 86
     */
    public function getUserWhere($where = null)
    {
        // Ini setara dengan $this->db->get_where('user', $where);
        return $this->where($where)->get()->getRowArray();
    }

    /**
     * Mengambil data user dengan limit
     * Adaptasi dari hlm. 86
     * * Menggunakan ->getResultArray() untuk mengambil
     * semua hasil sebagai array.
     */
    public function getUserLimit()
{
    return $this->orderBy('id', 'DESC')->findAll(5);
}
    /**
     * Mengecek akses user (Code dari modul hlm. 86)
     * Catatan: Tabel 'access_menu' tidak ada di pustaka.sql,
     * jadi fungsi ini mungkin tidak akan terpakai.
     */
    public function cekUserAccess($where = null)
    {
        // Kode ini diadaptasi langsung dari modul
        return $this->db->table('access_menu')->where($where)->get();
    }
    
}