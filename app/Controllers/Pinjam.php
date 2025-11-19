<?php

namespace App\Controllers;

use App\Models\ModelPinjam;
use App\Models\ModelBooking;
use App\Models\ModelUser;
use App\Models\ModelBuku;
use CodeIgniter\I18n\Time;

class Pinjam extends BaseController
{
    protected $ModelPinjam;
    protected $ModelBooking;
    protected $ModelUser;
    protected $ModelBuku;
    
    protected $helpers = ['url', 'session', 'form'];

    public function __construct()
    {
        $this->ModelPinjam = new ModelPinjam();
        $this->ModelBooking = new ModelBooking();
        $this->ModelUser = new ModelUser();
        $this->ModelBuku = new ModelBuku();
        
        cek_login();
    }

    public function index()
    {
        $data['judul'] = "Data Peminjaman";
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // PERBAIKAN: Tambahkan ->get() sebelum ->getResultArray()
        // Karena joinData() mengembalikan Builder, kita harus eksekusi dulu query-nya
        $data['pinjam'] = $this->ModelPinjam->joinData()->get()->getResultArray();

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('pinjam/data-pinjam', $data);
        echo view('templates/footer');
    }

    public function daftarBooking()
    {
        $data['judul'] = "Daftar Booking";
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // getJoinBooking() di model sudah melakukan ->get(), jadi aman
        $data['booking'] = $this->ModelPinjam->getJoinBooking()->getResultArray();

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('booking/daftar-booking', $data);
        echo view('templates/footer');
    }

    public function pinjamAct($id_booking)
    {
        $booking = $this->ModelBooking->getDatabyId('booking', ['id_booking' => $id_booking]);
        
        if (!$booking) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Data booking tidak ditemukan!</div>');
            return redirect()->to(base_url('pinjam/daftarBooking'));
        }

        $id_user = $booking['id_user'];
        $tgl_pinjam = date('Y-m-d');
        $tgl_kembali = date('Y-m-d', strtotime('+3 days', strtotime($tgl_pinjam)));

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

        $this->ModelPinjam->simpanPinjam($data_pinjam);

        $detail = $this->ModelBooking->getDataWhere('booking_detail', ['id_booking' => $id_booking])->getResultArray();

        foreach ($detail as $row) {
            $id_buku = $row['id_buku'];
            
            $data_detail = [
                'no_pinjam' => $data_pinjam['no_pinjam'],
                'id_buku' => $id_buku,
                'denda' => 0
            ];
            $this->ModelPinjam->simpanDetail($data_detail);

            $this->ModelBuku->simpanPinjam($id_buku);
        }

        $this->ModelBooking->deleteData(['id_booking' => $id_booking], 'booking');
        $this->ModelBooking->deleteData(['id_booking' => $id_booking], 'booking_detail');

        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Data Peminjaman berhasil disimpan!</div>');
        return redirect()->to(base_url('pinjam'));
    }

    public function ubahStatus($id_pinjam)
    {
        $pinjam = $this->ModelPinjam->getDatabyId('pinjam', ['no_pinjam' => $id_pinjam]);
        
        if (!$pinjam) {
            return redirect()->to(base_url('pinjam'));
        }

        $tgl_kembali = $pinjam['tgl_kembali'];
        $tgl_pengembalian = date('Y-m-d');

        $kembali = Time::parse($tgl_kembali);
        $pengembalian = Time::parse($tgl_pengembalian);
        
        $denda_per_hari = 5000;
        $total_denda = 0;

        if ($pengembalian->isAfter($kembali)) {
            $selisih = $kembali->difference($pengembalian)->getDays();
            $total_denda = abs($selisih) * $denda_per_hari;
        }

        $data_update = [
            'tgl_pengembalian' => $tgl_pengembalian,
            'status' => 'Kembali',
            'total_denda' => $total_denda
        ];

        $this->ModelPinjam->updateData('pinjam', $data_update, ['no_pinjam' => $id_pinjam]);

        $detail = $this->ModelPinjam->getDataWhere('pinjam_detail', ['no_pinjam' => $id_pinjam])->getResultArray();
        
        foreach ($detail as $row) {
            $this->ModelBuku->updateStokKembali($row['id_buku']);
        }

        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Buku berhasil dikembalikan!</div>');
        return redirect()->to(base_url('pinjam'));
    }
}