<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-lg" style="margin-top: 50px; margin-bottom: 50px;">
                <div class="card-header bg-success text-white text-center">
                    <h5 class="mb-0">Informasi Booking</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                        <h4 class="card-title">Booking Berhasil!</h4>
                        <p class="card-text">Terima kasih, <strong><?= $user['nama']; ?></strong>, telah melakukan booking.</p>
                    </div>
                    
                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>No. Booking:</strong>
                                    <span class="float-right"><?= $info['id_booking']; ?></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Tgl. Booking:</strong>
                                    <span class="float-right"><?= date('d F Y', strtotime($info['tgl_booking'])); ?></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Batas Ambil:</strong>
                                    <span class="float-right"><?= date('d F Y', strtotime($info['batas_ambil'])); ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <a href="<?= base_url('booking/exportToPdf/' . $info['id_booking']); ?>" class="btn btn-primary btn-lg">
                                    <i class="fas fa-print"></i> Cetak Bukti Booking
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4" role="alert">
                        <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Perhatian!</h4>
                        <p>Silakan cetak bukti booking ini dan bawa ke perpustakaan untuk pengambilan buku. Batas pengambilan buku adalah 2 hari dari tanggal booking.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>