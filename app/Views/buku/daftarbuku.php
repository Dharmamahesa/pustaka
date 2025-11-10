<div class="container">
    <div class="row">
        <div class="col">
            <div class="row">
                <?php foreach ($buku as $b) : ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <img src="<?= base_url('assets/img/upload/') . $b['image']; ?>" class="card-img-top" alt="<?= $b['judul_buku']; ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title text-truncate"><?= $b['judul_buku']; ?></h5>
                                <p class="card-text small text-truncate"><?= $b['pengarang']; ?></p>
                                <p class="card-text small text-truncate"><?= $b['penerbit']; ?></p>
                                <span class="badge badge-secondary mb-2"><?= $b['tahun_terbit']; ?></span>
                            </div>
                            <div class="card-footer" style="background-color: white;">
                                <?php if ($b['stok'] < 1) : ?>
                                    <a href="#" class="btn btn-outline-secondary btn-sm disabled">Stok Habis</a>
                                <?php else : ?>
                                    <a href="<?= base_url('booking/tambahBooking/' . $b['id']); ?>" class="btn btn-primary btn-sm">Booking</a>
                                <?php endif; ?>
                                
                                <a href="<?= base_url('home/detailBuku/' . $b['id']); ?>" class="btn btn-warning btn-sm">Detail</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>