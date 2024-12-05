<?php
session_start();

// Check if user is logged in and has the correct role (admin)
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php'); // Redirect to login page if not logged in or not admin
    exit();
}

include '../../db/config.php';  // Make sure to include your DB connection

// Fetch all pelanggan (customers)
$query = $conn->query("SELECT * FROM users where Level='operator'");
$pelangganList = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Operator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Daftar Operator</h3>
        <a href="add_operator.php" class="btn btn-success mb-3">Tambah Operator</a>
        <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Opertor</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pelangganList as $pelanggan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pelanggan['ID']); ?></td>
                        <td><?php echo htmlspecialchars($pelanggan['Username']); ?></td>
                        <td>
                            <!-- Edit and Delete actions -->
                            <a href="edit_operator.php?id=<?php echo $pelanggan['ID']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_operator.php?id=<?php echo $pelanggan['ID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>