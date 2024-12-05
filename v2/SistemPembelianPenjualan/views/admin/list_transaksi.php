<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';

// Ambil data transaksi yang terkait dengan barang, tanpa menggunakan kolom Qty
$query = $conn->query("SELECT * from transaksi");

$transaksi = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Daftar Transaksi</h3>
        <!-- Tombol Kembali Menggunakan Browser History -->
        <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nomor Order</th>
                    <th>Tanggal Order</th>
                    <th>Nomor PO</th>
                    <th>Tanggal PO</th>
                    <th>Total Transaksi</th>
                    <th>Type</th>
                 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transaksi as $item): ?>
                    <tr>
                        <td><?php echo $item['NomorOrder']; ?></td>
                        <td><?php echo $item['TanggalOrder']; ?></td>
                        <td><?php echo $item['NomorPO']; ?></td>
                        <td><?php echo $item['TanggalPO']; ?></td>
                        <td>Rp.<?php echo number_format($item['TotalHarga'], 2); ?></td>
                        <td>
                            <span class="badge bg-success">
                                <?php echo $item['type']; ?>
                            </span>
                       
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>