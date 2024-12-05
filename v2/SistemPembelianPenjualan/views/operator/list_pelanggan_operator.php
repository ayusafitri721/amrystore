<?php
session_start();

// Check if user is logged in and has the correct role (operator)
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'operator') {
    header('Location: ../login.php'); // Redirect to login page if not logged in or not operator
    exit();
}

include '../../db/config.php';  // Include the DB connection

// Fetch all customers from the database
$query = $conn->query("SELECT * FROM pelanggan");
$pelangganList = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Daftar Pelanggan</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Pelanggan</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pelangganList as $pelanggan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pelanggan['KodePelanggan']); ?></td>
                        <td><?php echo htmlspecialchars($pelanggan['NamaPelanggan']); ?></td>
                        <td><?php echo htmlspecialchars($pelanggan['AlamatPelanggan']); ?></td>
                        <td><?php echo htmlspecialchars($pelanggan['NoTelpPelanggan']); ?></td>
                        <td>
                            <!-- Button to edit customer -->
                            <a href="edit_pelanggan_operator.php?id=<?php echo $pelanggan['KodePelanggan']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <!-- Button to delete customer -->
                            <a href="delete_pelanggan_operator.php?id=<?php echo $pelanggan['KodePelanggan']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="add_pelanggan_operator.php" class="btn btn-success">Tambah Pelanggan</a>
         <!-- Tombol Kembali menggunakan browser history -->
         <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
    </div>
</body>
</html>
