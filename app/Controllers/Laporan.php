<?php

namespace App\Controllers;

use App\Models\ModelBuku;
use App\Models\ModelUser;
use App\Models\ModelPinjam;
use Dompdf\Dompdf;

class Laporan extends BaseController
{
    protected $ModelBuku;
    protected $ModelUser;
    protected $ModelPinjam;
    protected $helpers = ['form', 'url', 'session'];

    public function __construct()
    {
        $this->ModelBuku = new ModelBuku();
        $this->ModelUser = new ModelUser();
        $this->ModelPinjam = new ModelPinjam();
        
        cek_login();
    }

    // --- LAPORAN BUKU ---

    public function laporan_buku()
    {
        $data['judul'] = 'Laporan Data Buku';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        
        // PERBAIKAN: Hapus ->getResultArray()
        $data['buku'] = $this->ModelBuku->getBuku();
        
        $data['kategori'] = $this->ModelBuku->getKategori();

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('buku/laporan_buku', $data);
        echo view('templates/footer');
    }

    public function cetak_laporan_buku()
    {
        $data['judul'] = "Laporan Data Buku";
        // PERBAIKAN: Hapus ->getResultArray()
        $data['buku'] = $this->ModelBuku->getBuku();
        
        echo view('buku/laporan_print_buku', $data); 
    }

    public function laporan_buku_pdf()
    {
        $data['judul'] = "Laporan Data Buku"; 
        // PERBAIKAN: Hapus ->getResultArray()
        $data['buku'] = $this->ModelBuku->getBuku();

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set(array('isRemoteEnabled' => true));
        $dompdf->setOptions($options);
        
        $dompdf->loadHtml(view('buku/laporan_pdf_buku', $data)); 
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $dompdf->stream('laporan_data_buku.pdf', array('Attachment' => 0));
    }

    public function export_excel_buku()
    {
        $data = [
            'judul' => 'Laporan Data Buku',
            // PERBAIKAN: Hapus ->getResultArray()
            'buku' => $this->ModelBuku->getBuku()
        ];
        
        $this->response
             ->setHeader('Content-Type', 'application/vnd.ms-excel')
             ->setHeader('Content-Disposition', 'attachment;filename="laporan_buku.xls"')
             ->setBody(view('buku/export_excel_buku', $data));
        
        return $this->response;
    }

    // --- LAPORAN ANGGOTA ---

    public function laporan_anggota()
    {
        $data['judul'] = 'Laporan Data Anggota';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);
        $data['anggota'] = $this->ModelUser->where('role_id !=', 1)->findAll();

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('laporan/laporan_anggota', $data);
        echo view('templates/footer');
    }

    public function cetak_laporan_anggota()
    {
        $data['anggota'] = $this->ModelUser->where('role_id !=', 1)->findAll();
        $data['judul'] = "Laporan Data Anggota";
        echo view('laporan/laporan_print_anggota', $data);
    }

    public function laporan_anggota_pdf()
    {
        $data['anggota'] = $this->ModelUser->where('role_id !=', 1)->findAll();
        $data['judul'] = "Laporan Data Anggota";

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set(array('isRemoteEnabled' => true));
        $dompdf->setOptions($options);
        
        $dompdf->loadHtml(view('laporan/laporan_pdf_anggota', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan_data_anggota.pdf', array('Attachment' => 0));
    }

    public function export_excel_anggota()
    {
        $data = [
            'judul' => 'Laporan Data Anggota',
            'anggota' => $this->ModelUser->where('role_id !=', 1)->findAll()
        ];
        
        $this->response
             ->setHeader('Content-Type', 'application/vnd.ms-excel')
             ->setHeader('Content-Disposition', 'attachment;filename="laporan_anggota.xls"')
             ->setBody(view('laporan/export_excel_anggota', $data));
        
        return $this->response;
    }


    // --- LAPORAN PINJAM ---
    
    public function laporan_pinjam()
    {
        $data['judul'] = 'Laporan Data Peminjaman';
        $data['user'] = $this->ModelUser->cekData(['email' => session()->get('email')]);

        $rules = [
            'tgl_mulai' => 'required',
            'tgl_akhir' => 'required'
        ];

        if ($this->request->getMethod() === 'post' && $this->validate($rules)) {
            $tgl_mulai = $this->request->getPost('tgl_mulai');
            $tgl_akhir = $this->request->getPost('tgl_akhir');
            $laporan_type = $this->request->getPost('laporan_type');

            $data['tgl_mulai'] = $tgl_mulai;
            $data['tgl_akhir'] = $tgl_akhir;
            $data['pinjam'] = $this->ModelPinjam->laporanPeminjaman($tgl_mulai, $tgl_akhir);
            
            if ($laporan_type == 'print') {
                return $this->laporan_pinjam_print($tgl_mulai, $tgl_akhir);
            } elseif ($laporan_type == 'pdf') {
                return $this->laporan_pinjam_pdf($tgl_mulai, $tgl_akhir);
            } elseif ($laporan_type == 'excel') {
                return $this->export_excel_pinjam($tgl_mulai, $tgl_akhir);
            }
        }
        
        $data['validation'] = $this->validator;
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('templates/topbar', $data);
        echo view('pinjam/laporan-pinjam', $data);
        echo view('templates/footer');
    }

    public function laporan_pinjam_print($tgl_mulai, $tgl_akhir)
    {
        $data = [
            'judul' => "Laporan Data Peminjaman",
            'pinjam' => $this->ModelPinjam->laporanPeminjaman($tgl_mulai, $tgl_akhir),
            'tgl_mulai' => $tgl_mulai,
            'tgl_akhir' => $tgl_akhir
        ];
        echo view('pinjam/laporan-print-pinjam', $data);
    }

    public function laporan_pinjam_pdf($tgl_mulai, $tgl_akhir)
    {
        $data = [
            'judul' => "Laporan Data Peminjaman",
            'pinjam' => $this->ModelPinjam->laporanPeminjaman($tgl_mulai, $tgl_akhir),
            'tgl_mulai' => $tgl_mulai,
            'tgl_akhir' => $tgl_akhir
        ];

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set(array('isRemoteEnabled' => true));
        $dompdf->setOptions($options);
        
        $dompdf->loadHtml(view('pinjam/laporan-pdf-pinjam', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan_data_peminjaman.pdf', array('Attachment' => 0));
    }

    public function export_excel_pinjam($tgl_mulai, $tgl_akhir)
    {
        $data = [
            'judul' => 'Laporan Data Peminjaman',
            'pinjam' => $this->ModelPinjam->laporanPeminjaman($tgl_mulai, $tgl_akhir),
            'tgl_mulai' => $tgl_mulai,
            'tgl_akhir' => $tgl_akhir
        ];
        
        $this->response
             ->setHeader('Content-Type', 'application/vnd.ms-excel')
             ->setHeader('Content-Disposition', 'attachment;filename="laporan_peminjaman.xls"')
             ->setBody(view('pinjam/export-excel-pinjam', $data));
        
        return $this->response;
    }
}