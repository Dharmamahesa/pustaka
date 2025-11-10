<!DOCTYPE html>
<html>
<head>
    <title>Bukti Booking</title>
    <style>
        body { font-family: sans-serif; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        h3, h4 { text-align: center; }
        .info { margin-top: 20px; }
        .info p { margin: 5px 0; }
    </style>
</head>
<body>
    <h3><?= $judul; ?></h3>
    <hr>
    
    <div class="info">
        <p><strong>Nama Anggota:</strong> <?= $user['nama']; ?></p>
        <p><strong>Email:</strong> <?= $user['email']; ?></p>
        <p><strong>No. Booking:</strong> <?= $booking['id_booking']; ?></p>
        <p><strong>Tanggal Booking:</strong> <?= date('d F Y', strtotime($booking['tgl_booking'])); ?></p>
        <p><strong>Batas Ambil:</strong> <?= date('d F Y', strtotime($booking['batas_ambil'])); ?></p>
    </div>

    <h4>Daftar Buku yang Dibooking</h4>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Judul Buku</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun Terbit</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($detail as $d) : ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $d['judul_buku']; ?></td>
                <td><?= $d['pengarang']; ?></td>
                <td><?= $d['penerbit']; ?></td>
                <td><?= $d['tahun_terbit']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: center; font-size: 12px;">
        <p>Harap tunjukkan bukti booking ini kepada petugas saat pengambilan buku.</p>
    </div>
</body>
</html>