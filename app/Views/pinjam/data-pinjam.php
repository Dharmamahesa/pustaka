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
                                    <th>No Pinjam</th>
                                    <th>Nama Anggota</th>
                                    <th>Email</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Tgl Kembali</th>
                                    <th>Tgl Dikembalikan</th>
                                    <th>Total Denda</th>
                                    <th>Status</th>
                                    <th>Pilihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pinjam as $p) : ?>
                                    <tr>
                                        <td><?= $p['no_pinjam']; ?></td>
                                        <td><?= $p['nama']; ?></td>
                                        <td><?= $p['email']; ?></td>
                                        <td><?= date('d M Y', strtotime($p['tgl_pinjam'])); ?></td>
                                        <td><?= date('d M Y', strtotime($p['tgl_kembali'])); ?></td>
                                        <td>
                                            <?php if ($p['tgl_pengembalian'] == '0000-00-00') : ?>
                                                -
                                            <?php else : ?>
                                                <?= date('d M Y', strtotime($p['tgl_pengembalian'])); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>Rp. <?= number_format($p['total_denda'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($p['status'] == 'Kembali') : ?>
                                                <span class="badge badge-success">Sudah Kembali</span>
                                            <?php else : ?>
                                                <span class="badge badge-warning">Dipinjam</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($p['status'] == 'Pinjam') : ?>
                                                <a href="<?= base_url('pinjam/ubahStatus/' . $p['no_pinjam']); ?>"
                                                   class="btn btn-primary btn-sm"
                                                   onclick="return confirm('Yakin buku ini akan dikembalikan?');">
                                                   <i class="fas fa-fw fa-undo-alt"></i> Kembalikan
                                                </a>
                                            <?php else : ?>
                                                <span class="badge badge-secondary">Selesai</span>
                                            <?php endif; ?>
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