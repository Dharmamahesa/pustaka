<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelUser extends Model
{
    // ==================================================================================
    // KONFIGURASI MODEL
    // ==================================================================================
    // Nama tabel yang digunakan oleh Model (Halaman 42)
    protected $table = 'user'; 
    protected $primaryKey = 'id'; 
    
    // Field yang diizinkan untuk diisi saat insert/update
    protected $allowedFields = ['nama', 'email', 'image', 'password', 'role_id', 'is_active', 'tanggal_input'];
    

    // ==================================================================================
    // MANAJEMEN DATA (Halaman 46)
    // ==================================================================================

    // Fungsi untuk menyimpan data user baru (digunakan saat Registrasi) (Halaman 46, simpanData)
    public function simpanData($data = null)
    {
        // Menggunakan Query Builder CI4
        return $this->db->table($this->table)->insert($data);
    }

    // Fungsi untuk cek data atau mendapatkan data user berdasarkan kondisi (Halaman 46, cekData & getUserWhere)
    // Digunakan untuk proses login
    public function cekData($where = null)
    {
        return $this->db->table($this->table)->getWhere($where);
    }
    
    // Fungsi untuk mendapatkan data user dengan batasan 10 data (Halaman 49, getUserLimit)
    public function getUserLimit()
    {
        // Menggunakan Query Builder CI4
        return $this->db->table($this->table)
                        ->select('*')
                        ->limit(10) // Batasi 10 baris
                        ->get();
    }
    
    // Fungsi untuk mengecek hak akses user
    // Asumsi: Anda memiliki tabel 'access_menu' untuk logika ini (Halaman 48)
    public function cekUserAccess($where = null)
    {
        return $this->db->table('access_menu')
                        ->select('*')
                        ->where($where)
                        ->get();
    }
    
    // Note: Fungsi getUSerWhere() di modul memiliki fungsi yang sama dengan cekData()
    // di CI4, keduanya bisa diimplementasikan dengan method getWhere().
}