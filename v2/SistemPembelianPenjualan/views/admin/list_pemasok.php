<?php
session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';  // Include your database configuration

// Ambil data pemasok
$query = $conn->query("SELECT * FROM pemasok");
$pemasok = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pemasok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Daftar Pemasok</h3>
        
        <!-- Tombol tambah pemasok -->
        <a href="add_pemasok.php" class="btn btn-primary mb-3">Tambah Pemasok</a>
        <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>

        <!-- Tabel daftar pemasok -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Pemasok</th>
                    <th>Nama Pemasok</th>
                    <th>Alamat</th>
                    <th>No Telepon</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pemasok as $item): ?>
                <tr>
                    <td><?php echo $item['KodePemasok']; ?></td>
                    <td><?php echo $item['NamaPemasok']; ?></td>
                    <td><?php echo $item['Alamat']; ?></td>
                    <td><?php echo $item['NoTelp']; ?></td>
                    <td><?php echo $item['Email']; ?></td>
                    <td>
                        <a href="edit_pemasok.php?id=<?php echo $item['KodePemasok']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_pemasok.php?id=<?php echo $item['KodePemasok']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pemasok ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>