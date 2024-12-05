<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki level admin atau operator
if (!isset($_SESSION['username']) || ($_SESSION['level'] !== 'admin' && $_SESSION['level'] !== 'operator')) {
    header('Location: ../login.php'); // Redirect ke halaman login jika tidak memiliki level admin atau operator
    exit();
}

include '../../db/config.php';

// Pastikan ada parameter KodeBarang di URL
if (isset($_GET['id'])) {
    $kodeBarang = $_GET['id'];

    // Ambil data barang berdasarkan KodeBarang
    $query = $conn->prepare("SELECT * FROM Barang WHERE KodeBarang = ?");
    $query->execute([$kodeBarang]);
    $barang = $query->fetch(PDO::FETCH_ASSOC);

    // Jika barang tidak ditemukan, redirect ke daftar barang
    if (!$barang) {
        header('Location: list_barang.php');
        exit();
    }
}

// Proses update data barang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kodeBarang = $_POST['KodeBarang'];
    $namaBarang = $_POST['NamaBarang'];
    $jenisBarang = $_POST['JenisBarang'];
    $stock = $_POST['Stock'];
    $hargaBeli = $_POST['HargaBeli'];
    $totalHarga = $hargaBeli * $stock;
    $qty = $stock; // Kolom Qty yang disesuaikan dengan stock

    // Update query untuk mengubah data barang
    $query = $conn->prepare("UPDATE Barang SET NamaBarang = ?, JenisBarang = ?, Stock = ?, HargaBeli = ?, TotalHarga = ?, Qty = ? WHERE KodeBarang = ?");
    $query->execute([$namaBarang, $jenisBarang, $stock, $hargaBeli, $totalHarga, $qty, $kodeBarang]);

    // Redirect setelah berhasil update data
    header('Location: list_barang.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Edit Barang</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="KodeBarang" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="KodeBarang" name="KodeBarang" value="<?php echo $barang['KodeBarang']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="NamaBarang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="NamaBarang" name="NamaBarang" value="<?php echo $barang['NamaBarang']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="JenisBarang" class="form-label">Jenis Barang</label>
                <input type="text" class="form-control" id="JenisBarang" name="JenisBarang" value="<?php echo $barang['JenisBarang']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="Stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="Stock" name="Stock" value="<?php echo $barang['Stock']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="HargaBeli" class="form-label">Harga Beli</label>
                <input type="number" class="form-control" id="HargaBeli" name="HargaBeli" value="<?php echo $barang['HargaBeli']; ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</body>
</html>