<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: login.php');
    exit();
}

include '../../db/config.php';  // Include your database configuration

// Handle form submission to add new customer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPelanggan = $_POST['NamaPelanggan'];
    $alamatPelanggan = $_POST['AlamatPelanggan'];
    $noTelpPelanggan = $_POST['NoTelpPelanggan'];

    // Insert new customer into the pelanggan table
    $query = $conn->prepare("INSERT INTO pelanggan (NamaPelanggan, AlamatPelanggan, NoTelpPelanggan) VALUES (?, ?, ?)");
    $query->execute([$namaPelanggan, $alamatPelanggan, $noTelpPelanggan]);


    $query_insert_user = $conn->prepare("
        INSERT INTO users (Username, Password, Level)
        VALUES (:username, :password, :level)
        ");


    $level = "user";

    $query_insert_user->bindParam(':username', $namaPelanggan, PDO::PARAM_STR);
    $query_insert_user->bindParam(':password', $namaPelanggan, PDO::PARAM_STR); // Ensure password is hashed before insertion
    $query_insert_user->bindParam(':level', $level, PDO::PARAM_STR);
    $query_insert_user->execute();



    // Redirect to list of customers after successful insertion
    header('Location: list_pelanggan.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Tambah Pelanggan Baru</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="NamaPelanggan" class="form-label">Nama Pelanggan</label>
                <input type="text" class="form-control" id="NamaPelanggan" name="NamaPelanggan" required>
            </div>
            <div class="mb-3">
                <label for="AlamatPelanggan" class="form-label">Alamat Pelanggan</label>
                <input type="text" class="form-control" id="AlamatPelanggan" name="AlamatPelanggan" required>
            </div>x
            <div class="mb-3">
                <label for="NoTelpPelanggan" class="form-label">No. Telepon Pelanggan</label>
                <input type="text" class="form-control" id="NoTelpPelanggan" name="NoTelpPelanggan" required>
            </div>
            <button type="submit" class="btn btn-success">Tambah Pelanggan</button>
            <a href="list_pelanggan.php" class="btn btn-primary">Kembali ke Daftar Pelanggan</a>
            <!-- Tombol Kembali Menggunakan Browser History -->
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>

</html>