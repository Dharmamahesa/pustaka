<div class="container-fluid">
    <div class="row">
        <div class="col-lg-9">
            
            <?php if (isset($validation) && $validation->getErrors()) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $validation->listErrors(); ?>
                </div>
            <?php endif; ?>

            <?= form_open_multipart('buku/ubahBuku/' . $buku['id']); ?>
            
            <input type="hidden" name="id" value="<?= $buku['id']; ?>">
            <input type="hidden" name="old_pict" value="<?= $buku['image']; ?>">

            <div class="form-group row">
                <label for="judul_buku" class="col-sm-2 col-form-label">Judul Buku</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="judul_buku" name="judul_buku" value="<?= set_value('judul_buku', $buku['judul_buku']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="id_kategori" class="col-sm-2 col-form-label">Kategori</label>
                <div class="col-sm-10">
                    <select name="id_kategori" class="form-control">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategori as $k) { ?>
                            <option value="<?= $k['id_kategori']; ?>" <?= ($k['id_kategori'] == $buku['id_kategori']) ? 'selected' : ''; ?>>
                                <?= $k['nama_kategori']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="pengarang" class="col-sm-2 col-form-label">Pengarang</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="pengarang" name="pengarang" value="<?= set_value('pengarang', $buku['pengarang']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="penerbit" class="col-sm-2 col-form-label">Penerbit</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="penerbit" name="penerbit" value="<?= set_value('penerbit', $buku['penerbit']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="tahun" class="col-sm-2 col-form-label">Tahun Terbit</label>
                <div class="col-sm-10">
                    <select name="tahun" class="form-control">
                        <option value="">Pilih Tahun</option>
                        <?php for ($i = date('Y'); $i > 1990; $i--) { ?>
                            <option value="<?= $i; ?>" <?= ($i == $buku['tahun_terbit']) ? 'selected' : ''; ?>>
                                <?= $i; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="isbn" class="col-sm-2 col-form-label">ISBN</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="isbn" name="isbn" value="<?= set_value('isbn', $buku['isbn']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="stok" class="col-sm-2 col-form-label">Stok</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="stok" name="stok" value="<?= set_value('stok', $buku['stok']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2">Gambar</div>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-sm-3">
                            <img src="<?= base_url('assets/img/upload/') . $buku['image']; ?>" class="img-thumbnail">
                        </div>
                        <div class="col-sm-9">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="image" name="image">
                                <label class="custom-file-label" for="image">Pilih file baru</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row justify-content-end">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">Ubah</button>
                    <button class="btn btn-dark" onclick="window.history.go(-1)"> Kembali</button>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>
</div>