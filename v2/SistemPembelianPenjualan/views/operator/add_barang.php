<?php
session_start();
// Pengecekan apakah pengguna sudah login dan memiliki peran sebagai 'operator'
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'operator') {
    header('Location: ../login.php'); // Redirect ke halaman login jika bukan operator
    exit();
}

include '../../db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kodeBarang = $_POST['KodeBarang'];
    $namaBarang = $_POST['NamaBarang'];
    $jenisBarang = $_POST['JenisBarang'];
    $stock = $_POST['Stock'];
    $hargaBeli = $_POST['HargaBeli'];
    $totalHarga = $hargaBeli * $stock;

    // Menambahkan nilai Qty, bisa sama dengan Stock atau nilai lainnya
    $qty = $stock;

    // Menyertakan kolom Qty dalam query INSERT
    $query = $conn->prepare("INSERT INTO Barang (KodeBarang, NamaBarang, JenisBarang, Stock, HargaBeli, TotalHarga, Qty) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $query->execute([$kodeBarang, $namaBarang, $jenisBarang, $stock, $hargaBeli, $totalHarga, $qty]);

    // Redirect setelah berhasil menyimpan data
    header('Location: list_barang.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Tambah Barang</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="KodeBarang" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="KodeBarang" name="KodeBarang" required>
            </div>
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
                <label for="HargaBeli" class="form-label">Harga Beli</label>
                <input type="number" class="form-control" id="HargaBeli" name="HargaBeli" required>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</body>
</html>