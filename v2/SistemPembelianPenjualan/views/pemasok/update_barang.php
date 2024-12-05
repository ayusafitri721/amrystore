<?php
session_start();
include '../../db/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kodeBarang = $_POST['KodeBarang'];
    $namaBarang = $_POST['NamaBarang'];
    $jenisBarang = $_POST['JenisBarang'];
    $stock = $_POST['Stock'];
    $qty = $_POST['Qty'];

    $query = "UPDATE barang_pemasok SET NamaBarang = ?, JenisBarang = ?, Stock = ?, Qty = ? WHERE KodeBarang = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$namaBarang, $jenisBarang, $stock, $qty, $kodeBarang]);

    header("Location: barang_pemasok.php"); // Redirect to view page
}

if (isset($_GET['id'])) {
    $kodeBarang = $_GET['id'];
    $query = "SELECT * FROM barang_pemasok WHERE KodeBarang = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$kodeBarang]);
    $barang = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Barang Pemasok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Edit Barang Pemasok</h3>
        <form method="POST">
            <input type="hidden" name="KodeBarang" value="<?= $barang['KodeBarang'] ?>">
            <div class="mb-3">
                <label for="NamaBarang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="NamaBarang" name="NamaBarang" value="<?= $barang['NamaBarang'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="JenisBarang" class="form-label">Jenis Barang</label>
                <input type="text" class="form-control" id="JenisBarang" name="JenisBarang" value="<?= $barang['JenisBarang'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="Stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="Stock" name="Stock" value="<?= $barang['Stock'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="Qty" class="form-label">Qty</label>
                <input type="number" class="form-control" id="Qty" name="Qty" value="<?= $barang['Qty'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>

</html>