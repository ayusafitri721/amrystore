<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'pemasok') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';

$idPemasok = $_SESSION['id_pemasok'];

$query = $conn->prepare("SELECT * FROM transaksi WHERE KodeSupplier = :supplier AND status = 'kirim'");
$query->bindParam(':supplier', $idPemasok, PDO::PARAM_INT);
$query->execute();
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
                    <th>Actions</th>
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
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="./detail_transaksi.php?id=<?= $item['NomorOrder']; ?>" class="btn btn-secondary">Detail</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>