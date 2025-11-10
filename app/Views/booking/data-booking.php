<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?= session()->getFlashdata('pesan'); ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Buku</th>
                            <th scope="col">Penulis</th>
                            <th scope="col">Penerbit</th>
                            <th scope="col">Tahun</th>
                            <th scope="col">Pilihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($booking as $b) { ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <img src="<?= base_url('assets/img/upload/' . $b['image']); ?>" width="60" alt="">
                                </td>
                                <td><?= $b['penulis']; ?></td>
                                <td><?= $b['penerbit']; ?></td>
                                <td><?= $b['tahun_terbit']; ?></td>
                                <td>
                                    <a href="<?= base_url('booking/hapusbooking/' . $b['id_buku']); ?>" 
                                       onclick="return confirm('Yakin ingin menghapus buku ini dari keranjang?')" 
                                       class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <hr>
            <a class="btn btn-success btn-sm" href="<?= base_url(); ?>"><span class="fas fa-play"></span> Lanjutkan Booking Buku</a>
            
            <?php if (!empty($booking)) : ?>
                <a class="btn btn-primary btn-sm" href="<?= base_url('booking/bookingSelesai'); ?>"><span class="fas fa-check"></span> Selesaikan Booking</a>
            <?php endif; ?>
        </div>
    </div>
</div>