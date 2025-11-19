<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelBooking;
use App\Models\ModelUser;
use App\Models\ModelBuku;
use Dompdf\Dompdf; // Pastikan library Dompdf sudah terinstall via Composer

class Booking extends BaseController
{
    protected $ModelBooking;
    protected $ModelUser;
    protected $ModelBuku;
    
    // Memuat helper yang dibutuhkan
    protected $helpers = ['form', 'url', 'session'];

    public function __construct()
    {
        // Inisialisasi Model
        $this->ModelBooking = new ModelBooking();
        $this->ModelUser = new ModelUser();
        $this->ModelBuku = new ModelBuku();
        
        // Cek Login (Pastikan user sudah login sebelum akses controller ini)
        cek_login();
    }

    /**
     * Halaman Keranjang (Data Booking Sementara)
     */
    public function index()
    {
        $email_user = session()->get('email');
        $user = $this->ModelUser->cekData(['email' => $email_user]);
        $id_user = $user['id'];
        
        $data = [
            'judul' => "Data Booking",
            'user' => $user,
            'booking' => $this->ModelBooking->getDataTemp(['id_user' => $id_user]),
            'count_temp' => $this->ModelBooking->getCountTemp(['id_user' => $id_user])
        ];
        
        echo view('templates/templates-user/header', $data);
        echo view('booking/data-booking', $data);
        echo view('templates/templates-user/footer');
    }

    /**
     * Proses Tambah Buku ke Keranjang (Tabel Temp)
     */
    public function tambahBooking($id_buku)
    {
        $email_user = session()->get('email');
        $user = $this->ModelUser->cekData(['email' => $email_user]);
        $id_user = $user['id'];

        // PERBAIKAN: Menggunakan first() bukan getRowArray()
        // first() otomatis mengambil 1 baris data sebagai array di CI4
        $buku = $this->ModelBuku->bukuWhere(['id' => $id_buku])->first();

        // 1. Validasi Stok Buku
        if ($buku['stok'] < 1) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Stok buku yang Anda pilih habis!</div>');
            return redirect()->to(base_url());
        }

        // 2. Validasi Maksimal Booking (Maks 3 Buku)
        $cekBooking = $this->ModelBooking->getCountTemp(['id_user' => $id_user]);
        if ($cekBooking >= 3) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Maksimal 3 buku yang dapat dibooking!</div>');
            return redirect()->to(base_url());
        }

        // 3. Validasi Buku Kembar (Tidak boleh booking buku yang sama)
        $cekBukuTemp = $this->ModelBooking->getCountTemp(['id_user' => $id_user, 'id_buku' => $id_buku]);
        if ($cekBukuTemp > 0) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Buku ini sudah ada di keranjang Anda!</div>');
            return redirect()->to(base_url());
        }

        // Jika lolos validasi, simpan ke tabel temp
        $data = [
            'id_user' => $id_user,
            'email_user' => $email_user,
            'id_buku' => $id_buku,
            'judul_buku' => $buku['judul_buku'],
            'image' => $buku['image'],
            'penulis' => $buku['pengarang'],
            'penerbit' => $buku['penerbit'],
            'tahun_terbit' => $buku['tahun_terbit'],
            'tgl_booking' => date('Y-m-d H:i:s')
        ];

        $this->ModelBooking->simpanData('temp', $data);

        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Buku berhasil ditambahkan ke keranjang!</div>');
        return redirect()->to(base_url());
    }

    /**
     * Hapus Item dari Keranjang
     */
    public function hapusbooking($id_buku)
    {
        $user = $this->ModelUser->cekData(['email' => session()->get('email')]);
        $id_user = $user['id'];

        $this->ModelBooking->deleteData(['id_buku' => $id_buku, 'id_user' => $id_user], 'temp');
        
        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Buku berhasil dihapus dari keranjang!</div>');
        return redirect()->to(base_url('booking'));
    }

    /**
     * Selesaikan Booking
     * Pindahkan data dari Temp -> Booking Detail & Update Stok
     */
    public function bookingSelesai()
    {
        $user = $this->ModelUser->cekData(['email' => session()->get('email')]);
        $id_user = $user['id'];

        $tgl_sekarang = date('Y-m-d');
        // Generate ID Booking (Pastikan fungsi kodeOtomatis ada di ModelBooking)
        $id_booking = $this->ModelBooking->kodeOtomatis('id_booking', 'booking');
        
        $data_booking = [
            'id_booking' => $id_booking,
            'tgl_booking' => $tgl_sekarang,
            'batas_ambil' => date('Y-m-d', strtotime('+2 days', strtotime($tgl_sekarang))),
            'id_user' => $id_user
        ];

        // 1. Simpan ke tabel booking (Header)
        $this->ModelBooking->simpanData('booking', $data_booking);

        // 2. Ambil data dari keranjang (temp)
        $tempData = $this->ModelBooking->getDataTemp(['id_user' => $id_user]);
        
        foreach ($tempData as $row) {
            $data_detail = [
                'id_booking' => $id_booking,
                'id_buku' => $row['id_buku']
            ];
            
            // A. Simpan ke tabel booking_detail
            $this->ModelBooking->simpanData('booking_detail', $data_detail);
            
            // B. Update Stok Buku (Kurangi Stok, Tambah Dibooking)
            // Pastikan method kurangiStok ada di ModelBuku
            $this->ModelBuku->kurangiStok($row['id_buku']);
        }

        // 3. Bersihkan Keranjang (Temp)
        $this->ModelBooking->deleteData(['id_user' => $id_user], 'temp');

        // Tampilkan halaman Info/Sukses
        $data = [
            'judul' => "Booking Selesai",
            'user' => $user,
            'info' => $this->ModelBooking->getDatabyId('booking', ['id_booking' => $id_booking]),
            'count_temp' => 0 
        ];
        
        echo view('templates/templates-user/header', $data);
        echo view('booking/info-booking', $data);
        echo view('templates/templates-user/footer');
    }

    /**
     * Export Bukti Booking ke PDF
     */
    public function exportToPdf($id_booking)
    {
        $user = $this->ModelUser->cekData(['email' => session()->get('email')]);

        $data = [
            'judul' => "Bukti Booking",
            'user' => $user,
            'booking' => $this->ModelBooking->getDatabyId('booking', ['id_booking' => $id_booking]),
            'detail' => $this->ModelBooking->getBookingDetail($user['id'])
        ];

        // Inisialisasi Dompdf
        $dompdf = new Dompdf();
        
        // Opsi agar bisa load gambar/css dari URL
        $options = $dompdf->getOptions();
        $options->set(['isRemoteEnabled' => true]);
        $dompdf->setOptions($options);
        
        // Load View ke PDF
        $dompdf->loadHtml(view('booking/bukti-pdf', $data));
        
        // Set Ukuran Kertas
        $dompdf->setPaper('A4', 'landscape');
        
        // Render PDF
        $dompdf->render();
        
        // Stream (Download/Preview)
        // Attachment 0 = Preview di browser, 1 = Download otomatis
        $dompdf->stream('bukti-booking-' . $id_booking . '.pdf', ['Attachment' => 0]);
    }
}