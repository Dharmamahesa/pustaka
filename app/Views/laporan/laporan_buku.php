<div class="container-fluid">
    <?= session()->getFlashdata('pesan'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $judul; ?></h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('laporan/cetak_laporan_buku'); ?>" class="btn btn-primary btn-block"><i class="fas fa-print"></i> Cetak Laporan</a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('laporan/laporan_buku_pdf'); ?>" class="btn btn-danger btn-block"><i class="far fa-file-pdf"></i> Download PDF</a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('laporan/export_excel_buku'); ?>" class="btn btn-success btn-block"><i class="far fa-file-excel"></i> Export ke Excel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>