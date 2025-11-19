<div class="container-fluid">
    <?= session()->getFlashdata('pesan'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $judul; ?></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>ID Booking</th>
                                    <th>Pemesan</th>
                                    <th>Email</th>
                                    <th>Tgl Booking</th>
                                    <th>Batas Ambil</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($booking as $b) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $b['id_booking']; ?></td>
                                        <td><?= $b['nama']; ?></td>
                                        <td><?= $b['email']; ?></td>
                                        <td><?= date('d M Y', strtotime($b['tgl_booking'])); ?></td>
                                        <td><?= date('d M Y', strtotime($b['batas_ambil'])); ?></td>
                                        <td>
                                            <a href="<?= base_url('pinjam/pinjamAct/' . $b['id_booking']); ?>" 
                                               class="btn btn-primary btn-sm">
                                               <i class="fas fa-fw fa-check"></i> Konfirmasi Pinjam
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>