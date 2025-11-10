<h3><?= $judul; ?></h3>
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Judul Buku</th>
            <th>Pengarang</th>
            <th>Penerbit</th>
            <th>Tahun Terbit</th>
            <th>ISBN</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($buku as $b) : ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $b['judul_buku']; ?></td>
            <td><?= $b['pengarang']; ?></td>
            <td><?= $b['penerbit']; ?></td>
            <td><?= $b['tahun_terbit']; ?></td>
            <td><?= $b['isbn']; ?></td>
            <td><?= $b['stok']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>