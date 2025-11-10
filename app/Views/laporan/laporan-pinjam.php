<div class="container-fluid">
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
                            <div class="col-sm-3">
                                <label for="tgl_mulai">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?= set_value('tgl_mulai'); ?>">
                            </div>
                            <div class="col-sm-3">
                                <label for="tgl_akhir">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir" value="<?= set_value('tgl_akhir'); ?>">
                            </div>
                        </div>
                        
                        <p class="text-muted">Pilih jenis laporan:</p>
                        
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <button type="submit" name="laporan_type" value="print" class="btn btn-primary btn-block"><i class="fas fa-print"></i> Cetak Laporan</button>
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" name="laporan_type" value="pdf" class="btn btn-danger btn-block"><i class="far fa-file-pdf"></i> Download PDF</button>
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" name="laporan_type" value="excel" class="btn btn-success btn-block"><i class="far fa-file-excel"></i> Export ke Excel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>