<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header font-weight-bold"><?= $judul; ?></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?= base_url('assets/img/upload/') . $buku['image']; ?>" class="img-fluid" alt="<?= $buku['judul_buku']; ?>">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Judul Buku</th>
                                    <td><?= $buku['judul_buku']; ?></td>
                                </tr>
                                <tr>
                                    <th>Pengarang</th>
                                    <td><?= $buku['pengarang']; ?></td>
                                </tr>
                                <tr>
                                    <th>Penerbit</th>
                                    <td><?= $buku['penerbit']; ?></td>
                                </tr>
                                <tr>
                                    <th>Tahun Terbit</th>
                                    <td><?= $buku['tahun_terbit']; ?></td>
                                </tr>
                                <tr>
                                    <th>ISBN</th>
                                    <td><?= $buku['isbn']; ?></td>
                                </tr>
                                <tr>
                                    <th>Stok</th>
                                    <td><?= $buku['stok']; ?></td>
                                </tr>
                            </table>

                            <?php if ($buku['stok'] < 1) : ?>
                                <a href="#" class="btn btn-outline-secondary disabled"><i class="fas fa-fw fa-ban"></i> Stok Habis</a>
                            <?php else : ?>
                                <a href="<?= base_url('booking/tambahBooking/' . $buku['id']); ?>" class="btn btn-primary"><i class="fas fa-fw fa-shopping-cart"></i> Booking</a>
                            <?php endif; ?>
                            
                            <a href="<?= base_url(); ?>" class="btn btn-danger"><i class="fas fa-fw fa-reply"></i> Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>