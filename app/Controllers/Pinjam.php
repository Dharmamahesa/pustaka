<?php

namespace App\Controllers;

use App\Models\ModelPinjam;
use App\Models\ModelBooking;
use App\Models\ModelUser;
use App\Models\ModelBuku; // Kita butuh ini untuk update stok

class Pinjam extends BaseController
{
    protected $ModelPinjam;
    protected $ModelBooking;
    protected $ModelUser;
    protected $ModelBuku;
    protected $helpers = ['url', 'session']; // Muat helper

    public function __construct()
    {
        // Inisialisasi model
        $this->ModelPinjam = new ModelPinjam();
        $this->ModelBooking = new ModelBooking();
        $this->ModelUser = new ModelUser();
        $this->ModelBuku = new ModelBuku();
        
        // Panggil helper cek_login (pastikan 'pustaka' helper ada di Autoload.php)
        cek_login();
    }

    /**
     * Halaman Data Peminjaman (Admin)
     * Menampilkan data yang status='Pinjam'
     */
    public function index()
    {
        $data['judul'] = "Data Peminjaman";
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // Ambil data join pinjam dan user
        $data['pinjam'] = $this->ModelPinjam->joinData()->getResultArray();

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('pinjam/data-pinjam', $data);
        echo view('templates/footer');
    }

    /**
     * Halaman Daftar Booking (Admin)
     * Menampilkan data booking yang masuk
     */
    public function daftarBooking()
    {
        $data['judul'] = "Daftar Booking";
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // Ambil data join booking dan user
        $data['booking'] = $this->ModelPinjam->getJoinBooking()->getResultArray();

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        // View ini ada di folder booking
        echo view('booking/daftar-booking', $data); 
        echo view('templates/footer');
    }

    /**
     * Aksi Konfirmasi Peminjaman (Admin)
     * Mengubah data dari Booking menjadi Pinjam
     */
    public function pinjamAct($id_booking)
    {
        // Ambil data booking berdasarkan id
        $booking = $this->ModelBooking->getDatabyId('booking', ['id_booking' => $id_booking]);
        $id_user = $booking['id_user'];
        $tgl_pinjam = date('Y-m-d');
        $tgl_kembali = date('Y-m-d', strtotime('+3 days', strtotime($tgl_pinjam)));

        // Buat data untuk tabel pinjam
        $data_pinjam = [
            'no_pinjam' => $this->ModelPinjam->kodeOtomatis('no_pinjam', 'pinjam'),
            'tgl_pinjam' => $tgl_pinjam,
            'id_booking' => $id_booking,
            'id_user' => $id_user,
            'tgl_kembali' => $tgl_kembali,
            'tgl_pengembalian' => '0000-00-00',
            'status' => 'Pinjam',
            'total_denda' => 0
        ];

        // Simpan ke tabel pinjam
        $this->ModelPinjam->simpanPinjam($data_pinjam);

        // Ambil detail booking
        $detail = $this->ModelBooking->getDataWhere('booking_detail', ['id_booking' => $id_booking])->getResultArray();

        foreach ($detail as $row) {
            $id_buku = $row['id_buku'];
            
            // Simpan ke pinjam_detail
            $data_detail = [
                'no_pinjam' => $data_pinjam['no_pinjam'],
                'id_buku' => $id_buku,
                'denda' => 0 // Denda awal 0
            ];
            $this->ModelPinjam->simpanDetail($data_detail);

            // Update stok dan dipinjam di tabel buku
            $this->ModelBuku->updateBuku(
                ['dipinjam' => 'dipinjam+1', 'stok' => 'stok-1'],
                ['id' => $id_buku]
            );
        }

        // Hapus data dari booking dan booking_detail
        $this->ModelBooking->deleteData(['id_booking' => $id_booking], 'booking');
        $this->ModelBooking->deleteData(['id_booking' => $id_booking], 'booking_detail');

        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Data Peminjaman berhasil disimpan!</div>');
        return redirect()->to(base_url('pinjam'));
    }

    /**
     * Aksi Pengembalian Buku (Admin)
     */
    public function ubahStatus($id_pinjam)
    {
        // Ambil data pinjam
        $pinjam = $this->ModelPinjam->getDatabyId('pinjam', ['no_pinjam' => $id_pinjam]);
        $tgl_kembali = $pinjam['tgl_kembali'];
        $tgl_pengembalian = date('Y-m-d');

        // Hitung denda (CI4 Time)
        $kembali = new \CodeIgniter\I18n\Time($tgl_kembali);
        $pengembalian = new \CodeIgniter\I18n\Time($tgl_pengembalian);
        $denda_per_hari = 5000;
        $total_denda = 0;

        if ($pengembalian->isAfter($kembali)) {
            $selisih = $kembali->difference($pengembalian)->getDays();
            $total_denda = $selisih * $denda_per_hari;
        }

        // Data update untuk tabel pinjam
        $data_update = [
            'tgl_pengembalian' => $tgl_pengembalian,
            'status' => 'Kembali',
            'total_denda' => $total_denda
        ];

        $this->ModelPinjam->updateData('pinjam', $data_update, ['no_pinjam' => $id_pinjam]);

        // Update stok dan dipinjam di tabel buku
        $detail = $this->ModelPinjam->getDataWhere('pinjam_detail', ['no_pinjam' => $id_pinjam])->getResultArray();
        foreach ($detail as $row) {
            $id_buku = $row['id_buku'];
            $this->ModelBuku->updateBuku(
                ['dipinjam' => 'dipinjam-1', 'stok' => 'stok+1'],
                ['id' => $id_buku]
            );
        }

        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Buku berhasil dikembalikan!</div>');
        return redirect()->to(base_url('pinjam'));
    }
}