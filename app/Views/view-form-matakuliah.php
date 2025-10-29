<html>
<head>
    <title>Form Input Matakuliah</title>
</head>
<body>
    <center>
        <form action="<?= base_url('matakuliah/cetak'); ?>" method="post">
            <table>
                <tr>
                    <th colspan="3">Form Input Data Mata Kuliah</th>
                </tr>
                <tr>
                    <td colspan="3">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <th>Kode MTK</th>
                    <th>:</th>
                    <td>
                        <input type="text" name="kode" id="kode" value="<?= old('kode'); ?>">
                        <div style="color: red; font-size: small; margin-top: 5px;">
                            <?= service('validation')->getError('kode'); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Nama MTK</th>
                    <td>:</td>
                    <td>
                        <input type="text" name="nama" id="nama" value="<?= old('nama'); ?>">
                        <div style="color: red; font-size: small; margin-top: 5px;">
                            <?= service('validation')->getError('nama'); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>SKS</th>
                    <td>:</td>
                    <td>
                        <select name="sks" id="sks">
                            <option value="">Pilih SKS</option>
                            <option value="2" <?= old('sks') == 2 ? 'selected' : ''; ?>>2</option>
                            <option value="3" <?= old('sks') == 3 ? 'selected' : ''; ?>>3</option>
                            <option value="4" <?= old('sks') == 4 ? 'selected' : ''; ?>>4</option>
                        </select>
                        <div style="color: red; font-size: small; margin-top: 5px;">
                            <?= service('validation')->getError('sks'); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="center">
                        <input type="submit" value="Submit">
                    </td>
                </tr>
            </table>
        </form>
    </center>
</body>
</html>