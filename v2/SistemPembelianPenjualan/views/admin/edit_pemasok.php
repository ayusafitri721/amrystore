<?php
session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';  // Include your database configuration

// Ambil ID pemasok yang ingin diedit
if (!isset($_GET['id'])) {
    header('Location: list_pemasok.php');
    exit();
}

$id = $_GET['id'];

// Ambil data pemasok berdasarkan ID
$query = $conn->prepare("SELECT * FROM pemasok WHERE KodePemasok = :id");
$query->bindParam(':id', $id);
$query->execute();
$pemasok = $query->fetch(PDO::FETCH_ASSOC);

// Jika pemasok tidak ditemukan, redirect ke daftar pemasok
if (!$pemasok) {
    header('Location: list_pemasok.php');
    exit();
}

// Proses update data pemasok
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPemasok = $_POST['NamaPemasok'];
    $alamat = $_POST['Alamat'];
    $noTelp = $_POST['NoTelp'];
    $email = $_POST['Email'];

    // Validasi input
    if (empty($namaPemasok) || empty($alamat) || empty($noTelp) || empty($email)) {
        $error = "Semua field harus diisi!";
    } else {
        // Update data pemasok ke database
        $query_update = $conn->prepare("UPDATE pemasok SET NamaPemasok = :NamaPemasok, Alamat = :Alamat, NoTelp = :NoTelp, Email = :Email WHERE KodePemasok = :id");
        $query_update->bindParam(':NamaPemasok', $namaPemasok);
        $query_update->bindParam(':Alamat', $alamat);
        $query_update->bindParam(':NoTelp', $noTelp);
        $query_update->bindParam(':Email', $email);
        $query_update->bindParam(':id', $id);

        if ($query_update->execute()) {
            $success = "Data pemasok berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui data pemasok!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemasok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Edit Pemasok</h3>
        
        <!-- Pesan error/success -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="edit_pemasok.php?id=<?php echo $pemasok['KodePemasok']; ?>" method="POST">
            <div class="mb-3">
                <label for="NamaPemasok" class="form-label">Nama Pemasok</label>
                <input type="text" class="form-control" id="NamaPemasok" name="NamaPemasok" value="<?php echo $pemasok['NamaPemasok']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="Alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="Alamat" name="Alamat" value="<?php echo $pemasok['Alamat']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="NoTelp" class="form-label">No Telepon</label>
                <input type="text" class="form-control" id="NoTelp" name="NoTelp" value="<?php echo $pemasok['NoTelp']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="Email" class="form-label">Email</label>
                <input type="email" class="form-control" id="Email" name="Email" value="<?php echo $pemasok['Email']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Perbarui Pemasok</button>
            <a href="list_pemasok.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>