<?php
session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Sertakan koneksi database
include '../../db/config.php';  // Include your database configuration

// Proses penambahan pemasok
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPemasok = $_POST['NamaPemasok'];
    $alamat = $_POST['Alamat'];
    $noTelp = $_POST['NoTelp'];
    $email = $_POST['Email'];

    // Validasi input
    if (empty($namaPemasok) || empty($alamat) || empty($noTelp) || empty($email)) {
        $error = "Semua field harus diisi!";
    } else {
        $query = $conn->prepare("INSERT INTO pemasok (NamaPemasok, Alamat, NoTelp, Email) VALUES (:NamaPemasok, :Alamat, :NoTelp, :Email)");
        $query->bindParam(':NamaPemasok', $namaPemasok);
        $query->bindParam(':Alamat', $alamat);
        $query->bindParam(':NoTelp', $noTelp);
        $query->bindParam(':Email', $email);

        if ($query->execute()) {

            $level = 'pemasok';

            $queryUsers = $conn->prepare("INSERT INTO users (username, password, level) VALUES (:username, :password, :level)");
            $queryUsers->bindParam(':username', $email);
            $queryUsers->bindParam(':password', $namaPemasok);
            $queryUsers->bindParam(':level', $level);

            $queryUsers->execute();

            $success = "Pemasok berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan pemasok!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pemasok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Tambah Pemasok</h3>

        <!-- Pesan error/success -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="add_pemasok.php" method="POST">
            <div class="mb-3">
                <label for="NamaPemasok" class="form-label">Nama Pemasok</label>
                <input type="text" class="form-control" id="NamaPemasok" name="NamaPemasok" required>
            </div>
            <div class="mb-3">
                <label for="Alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="Alamat" name="Alamat" required>
            </div>
            <div class="mb-3">
                <label for="NoTelp" class="form-label">No Telepon</label>
                <input type="text" class="form-control" id="NoTelp" name="NoTelp" required>
            </div>
            <div class="mb-3">
                <label for="Email" class="form-label">Email</label>
                <input type="email" class="form-control" id="Email" name="Email" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Pemasok</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>

</html>