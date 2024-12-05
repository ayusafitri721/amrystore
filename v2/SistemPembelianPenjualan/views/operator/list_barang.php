<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'operator') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';

// Query untuk mengambil semua barang
$query = $conn->query("SELECT * FROM Barang");
$barang = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Daftar Barang</h3>
        <a href="add_barang.php" class="btn btn-primary mb-3">Tambah Barang</a>
                <!-- Tombol Kembali Menggunakan Browser History -->
                <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jenis Barang</th>
                    <th>Stock</th>
                    <th>Harga Beli</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($barang as $item): ?>
                <tr>
                    <td><?php echo $item['KodeBarang']; ?></td>
                    <td><?php echo $item['NamaBarang']; ?></td>
                    <td><?php echo $item['JenisBarang']; ?></td>
                    <td><?php echo $item['Stock']; ?></td>
                    <td><?php echo number_format($item['HargaBeli'], 0, ',', '.'); ?></td>
                    <td><?php echo number_format($item['TotalHarga'], 0, ',', '.'); ?></td>
                    <td>
                        <a href="edit_barang.php?id=<?php echo $item['KodeBarang']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>