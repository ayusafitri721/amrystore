<?php

session_start();

include '../../db/config.php';

    $idPemasok = $_SESSION['id_pemasok'];
$query = "SELECT * FROM barang_pemasok WHERE id_pemasok = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$idPemasok]);
$barangPemasok = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Barang Pemasok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Barang Pemasok</h3>
        <a href="create_barang.php" class="btn btn-primary mb-3">Tambah Barang</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jenis Barang</th>
                    <th>Stock</th>
                    <th>Qty</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($barangPemasok as $item): ?>
                    <tr>
                        <td><?= $item['KodeBarang'] ?></td>
                        <td><?= $item['NamaBarang'] ?></td>
                        <td><?= $item['JenisBarang'] ?></td>
                        <td><?= $item['Stock'] ?></td>
                        <td><?= $item['Qty'] ?></td>
                        <td>
                            <a href="update_barang.php?id=<?= $item['KodeBarang'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_barang.php?id=<?= $item['KodeBarang'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>