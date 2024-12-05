<?php
include '../../db/config.php';

session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaBarang = $_POST['NamaBarang'];
    $jenisBarang = $_POST['JenisBarang'];
    $stock = $_POST['Stock'];
    $qty = $_POST['Qty'];
    $idPemasok = $_SESSION['id_pemasok'];




    $query = "INSERT INTO barang_pemasok (NamaBarang, JenisBarang, Stock, Qty, id_pemasok) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$namaBarang, $jenisBarang, $stock, $qty, $idPemasok]);

    header("Location: barang_pemasok.php"); // Redirect to view page
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Barang Pemasok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Create Barang Pemasok</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="NamaBarang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="NamaBarang" name="NamaBarang" required>
            </div>
            <div class="mb-3">
                <label for="JenisBarang" class="form-label">Jenis Barang</label>
                <input type="text" class="form-control" id="JenisBarang" name="JenisBarang" required>
            </div>
            <div class="mb-3">
                <label for="Stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="Stock" name="Stock" required>
            </div>
            <div class="mb-3">
                <label for="Qty" class="form-label">Qty</label>
                <input type="number" class="form-control" id="Qty" name="Qty" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>

</html>