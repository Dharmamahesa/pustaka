<div class="container-fluid">

    <div class="row">
        <div class="col-lg-12">
            <?= session()->getFlashdata('pesan'); ?>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Buku yang Sedang Dipinjam</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No Pinjam</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Batas Kembali</th>
                                    <th>Total Denda (Hingga Hari Ini)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pinjam)) : ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada buku yang sedang dipinjam.</td>
                                    </tr>
                                <?php endif; ?>
                                
                                <?php 
                                $today = new \CodeIgniter\I18n\Time('now');
                                foreach ($pinjam as $p) : 
                                    $tgl_kembali = new \CodeIgniter\I18n\Time($p['tgl_kembali']);
                                    $denda = 0;
                                    if ($today->isAfter($tgl_kembali)) {
                                        $selisih = $tgl_kembali->difference($today)->getDays();
                                        $denda = $selisih * 5000; // Asumsi denda 5000/hari
                                    }
                                ?>
                                    <tr>
                                        <td><?= $p['no_pinjam']; ?></td>
                                        <td><?= date('d M Y', strtotime($p['tgl_pinjam'])); ?></td>
                                        <td><?= date('d M Y', strtotime($p['tgl_kembali'])); ?></td>
                                        <td>Rp. <?= number_format($denda, 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Pengembalian Buku</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No Pinjam</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Tgl Kembali</th>
                                    <th>Tgl Dikembalikan</th>
                                    <th>Total Denda</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($kembali)) : ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada riwayat pengembalian.</td>
                                    </tr>
                                <?php endif; ?>
                                
                                <?php foreach ($kembali as $k) : ?>
                                    <tr>
                                        <td><?= $k['no_pinjam']; ?></td>
                                        <td><?= date('d M Y', strtotime($k['tgl_pinjam'])); ?></td>
                                        <td><?= date('d M Y', strtotime($k['tgl_kembali'])); ?></td>
                                        <td><?= date('d M Y', strtotime($k['tgl_pengembalian'])); ?></td>
                                        <td>Rp. <?= number_format($k['total_denda'], 0, ',', '.'); ?></td>
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