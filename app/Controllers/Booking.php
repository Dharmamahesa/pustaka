<?php

namespace App\Controllers;

use App\Models\ModelBooking;
use App\Models\ModelUser;
use App\Models\ModelBuku;
use Dompdf\Dompdf; // Panggil library Dompdf

class Booking extends BaseController
{
    protected $ModelBooking;
    protected $ModelUser;
    protected $ModelBuku;
    protected $helpers = ['form', 'url', 'session']; // Muat helper

    public function __construct()
    {
        // Inisialisasi model
        $this->ModelBooking = new ModelBooking();
        $this->ModelUser = new ModelUser();
        $this->ModelBuku = new ModelBuku();
        
        // Panggil helper cek_login (pastikan 'pustaka' helper ada di Autoload.php)
        cek_login();
    }

    /**
     * Halaman Keranjang Booking (Keranjang Belanja)
     * Menampilkan data buku di tabel 'temp'
     */
    public function index()
    {
        $id_user = session()->get('id_user'); // Ambil id_user dari session
        $email_user = session()->get('email');
        
        $data['user'] = $this->ModelUser->cekData(['email' => $email_user]);
        $data['judul'] = "Data Booking";
        $data['booking'] = $this->ModelBooking->getDataTemp(['id_user' => $id_user]);
        $data['user'] = $this->ModelUser->cekData(['email' => $email_user]);

        // Hitung total bayar (jika ada denda/harga, di CI3 tidak ada, jadi 0)
        $data['total'] = 0; 
        
        echo view('templates/templates-user/header', $data);
        echo view('booking/data-booking', $data);
        echo view('templates/templates-user/footer');
    }

    /**
     * Aksi untuk menambah buku ke keranjang (tabel temp)
     */
    public function tambahBooking($id_buku)
    {
        $email_user = session()->get('email');
        $user = $this->ModelUser->cekData(['email' => $email_user]);
        $id_user = $user['id']; // Dapatkan id_user

        // Ambil data buku
        $buku = $this->ModelBuku->bukuWhere(['id' => $id_buku])->getRowArray();

        // Validasi 1: Cek stok
        if ($buku['stok'] < 1) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Stok buku yang Anda pilih habis!</div>');
            return redirect()->to(base_url());
        }

        // Validasi 2: Cek maksimal 3 buku
        $cekBooking = $this->ModelBooking->getCountTemp(['id_user' => $id_user]);
        if ($cekBooking >= 3) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Maksimal 3 buku yang dapat dibooking!</div>');
            return redirect()->to(base_url());
        }

        // Validasi 3: Cek apakah buku sudah ada di keranjang
        $cekBukuTemp = $this->ModelBooking->getCountTemp(['id_user' => $id_user, 'id_buku' => $id_buku]);
        if ($cekBukuTemp > 0) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Buku ini sudah ada di keranjang Anda!</div>');
            return redirect()->to(base_url());
        }

        // Jika lolos validasi, masukkan ke tabel temp
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
     * Aksi untuk menghapus buku dari keranjang
     */
    public function hapusbooking($id_buku)
    {
        $id_user = session()->get('id_user');
        $this->ModelBooking->deleteData(['id_buku' => $id_buku, 'id_user' => $id_user], 'temp');
        
        session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Buku berhasil dihapus dari keranjang!</div>');
        return redirect()->to(base_url('booking'));
    }

    /**
     * Aksi untuk menyelesaikan proses booking
     * Memindahkan data dari 'temp' ke 'booking' dan 'booking_detail'
     */
    public function bookingSelesai()
    {
        $email_user = session()->get('email');
        $user = $this->ModelUser->cekData(['email' => $email_user]);
        $id_user = $user['id']; // Dapatkan id_user

        $tgl_sekarang = date('Y-m-d');
        $id_booking = $this->ModelBooking->kodeOtomatis('booking', 'id_booking');
        
        $data_booking = [
            'id_booking' => $id_booking,
            'tgl_booking' => $tgl_sekarang,
            'batas_ambil' => date('Y-m-d', strtotime('+2 days', strtotime($tgl_sekarang))),
            'id_user' => $id_user
        ];

        // Simpan ke tabel booking
        $this->ModelBooking->simpanData('booking', $data_booking);

        // Ambil semua data dari temp, pindahkan ke booking_detail
        $tempData = $this->ModelBooking->getDataTemp(['id_user' => $id_user]);
        foreach ($tempData as $row) {
            $data_detail = [
                'id_booking' => $id_booking,
                'id_buku' => $row['id_buku']
            ];
            $this->ModelBooking->simpanData('booking_detail', $data_detail);
        }

        // Kosongkan tabel temp
        $this->ModelBooking->deleteData(['id_user' => $id_user], 'temp');

        // Tampilkan halaman info booking
        $data['user'] = $user;
        $data['judul'] = "Booking Selesai";
        $data['info'] = $this->ModelBooking->getDatabyId('booking', ['id_booking' => $id_booking]);
        
        echo view('templates/templates-user/header', $data);
        echo view('booking/info-booking', $data);
        echo view('templates/templates-user/footer');
    }

    /**
     * Mencetak bukti booking ke PDF
     */
    public function exportToPdf($id_booking)
    {
        $email_user = session()->get('email');
        $user = $this->ModelUser->cekData(['email' => $email_user]);

        $data['user'] = $user;
        $data['judul'] = "Bukti Booking";
        $data['booking'] = $this->ModelBooking->getDatabyId('booking', ['id_booking' => $id_booking]);
        $data['detail'] = $this->ModelBooking->getBookingDetail($user['id']);

        // Persiapan Dompdf
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set(array('isRemoteEnabled' => true));
        $dompdf->setOptions($options);
        
        $dompdf->loadHtml(view('booking/bukti-pdf', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Output PDF
        $dompdf->stream('bukti-booking-' . $id_booking . '.pdf', array('Attachment' => 0));
    }
}