<!DOCTYPE html>
<html>
<head>
    <title><?= $judul; ?></title>
    <style>
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        h3 { text-align: center; }
    </style>
</head>
<body onload="window.print()">
    <h3><?= $judul; ?></h3>
    <hr>
    <table class="table">
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
</body>
</html>