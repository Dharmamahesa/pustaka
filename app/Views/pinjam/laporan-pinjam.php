<div class="container-fluid">
    <?= session()->getFlashdata('pesan'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $judul; ?></h6>
                </div>
                <div class="card-body">
                    <?php if (isset($validation) && $validation->getErrors()) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $validation->listErrors(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?= form_open('laporan/laporan_pinjam'); ?>
                        <div class="form-group row">
                            <label for="tgl_mulai" class="col-sm-2 col-form-label">Tanggal Mulai</label>
                            <div class="col-sm-3">
                                <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?= set_value('tgl_mulai'); ?>">
                            </div>
                            <label for="tgl_akhir" class="col-sm-2 col-form-label">Tanggal Akhir</label>
                            <div class="col-sm-3">
                                <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir" value="<?= set_value('tgl_akhir'); ?>">
                            </div>
                        </div>
                        
                        <hr>
                        <p class="text-muted font-weight-bold">Pilih Output Laporan:</p>
                        
                        <div class="form-group row">
                            <div class="col-sm-4 mb-2">
                                <button type="submit" name="laporan_type" value="print" class="btn btn-primary btn-block"><i class="fas fa-print"></i> Lihat / Cetak Laporan</button>
                            </div>
                            <div class="col-sm-4 mb-2">
                                <button type="submit" name="laporan_type" value="pdf" class="btn btn-danger btn-block"><i class="far fa-file-pdf"></i> Download PDF</button>
                            </div>
                            <div class="col-sm-4 mb-2">
                                <button type="submit" name="laporan_type" value="excel" class="btn btn-success btn-block"><i class="far fa-file-excel"></i> Export Excel</button>
                            </div>
                        </div>
                    </form>
                    
                    <?php if(isset($pinjam) && !empty($pinjam)) : ?>
                        <hr class="mt-4">
                        <h5 class="text-center mb-3">Preview Laporan</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>No Pinjam</th>
                                        <th>Peminjam</th>
                                        <th>Buku</th>
                                        <th>Tgl Pinjam</th>
                                        <th>Tgl Kembali</th>
                                        <th>Denda</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($pinjam as $p) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $p['no_pinjam']; ?></td>
                                        <td><?= $p['nama']; ?></td>
                                        <td><?= $p['judul_buku']; ?></td>
                                        <td><?= $p['tgl_pinjam']; ?></td>
                                        <td><?= $p['tgl_kembali']; ?></td>
                                        <td><?= $p['total_denda']; ?></td>
                                        <td><?= $p['status']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
</div>