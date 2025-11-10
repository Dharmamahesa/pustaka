<h3><?= $judul; ?></h3>
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Member Sejak</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($anggota as $a) : ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $a['nama']; ?></td>
            <td><?= $a['email']; ?></td>
            <td><?= date('d F Y', $a['tanggal_input']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>