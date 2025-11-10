<!DOCTYPE html>
<html>
<head>
    <title><?= $judul; ?></title>
    <style>
        body { font-family: sans-serif; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        h3 { text-align: center; }
    </style>
</head>
<body>
    <h3><?= $judul; ?></h3>
    <hr>
    <table class="table">
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
</body>
</html>