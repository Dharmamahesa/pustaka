<!DOCTYPE html>
<html>
<head>
    <title><?= $judul; ?></title>
    <style>
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        h3, p { text-align: center; }
    </style>
</head>
<body onload="window.print()">
    <h3><?= $judul; ?></h3>
    <p>Periode: <?= $tgl_mulai . ' s/d ' . $tgl_akhir; ?></p>
    <hr>
    <table class="table">
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
                <td>Rp. <?= number_format($p['total_denda'], 0, ',', '.'); ?></td>
                <td><?= $p['status']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>