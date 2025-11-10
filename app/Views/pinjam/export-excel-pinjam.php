<h3><?= $judul; ?></h3>
<p>Periode: <?= $tgl_mulai . ' s/d ' . $tgl_akhir; ?></p>
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama Anggota</th>
            <th>Judul Buku</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Tgl Dikembalikan</th>
            <th>Total Denda</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($pinjam as $p) : ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $p['nama']; ?></td>
            <td><?= $p['judul_buku']; ?></td>
            <td><?= $p['tgl_pinjam']; ?></td>
            <td><?= $p['tgl_kembali']; ?></td>
            <td><?= $p['tgl_pengembalian']; ?></td>
            <td><?= $p['total_denda']; ?></td>
            <td><?= $p['status']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>