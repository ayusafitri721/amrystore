<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';



if (isset($_GET['action']) && $_GET['action'] == 'kirim' && isset($_GET['NomorOrder'])) {
    $nomorOrder = $_GET['NomorOrder'];

    // Start a transaction to ensure atomicity
    $conn->beginTransaction();

    try {
        // Update the status of the transaction to 'kirim'
        $updateQuery = $conn->prepare("UPDATE transaksi SET status = 'pending' WHERE NomorOrder = :NomorOrder");
        $updateQuery->bindParam(':NomorOrder', $nomorOrder, PDO::PARAM_INT);

        if ($updateQuery->execute()) {
            $conn->commit();

            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error updating status.";
            $conn->rollBack();
        }
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}



// Ambil data transaksi yang terkait dengan barang, tanpa menggunakan kolom Qty
$query = $conn->prepare("SELECT t.*, p.NamaPemasok FROM transaksi t 
                        JOIN pemasok p ON t.KodeSupplier = p.KodePemasok 
                        WHERE t.status = 'admin'");
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
        <h3>Daftar Permintaan</h3>
        <!-- Tombol Kembali Menggunakan Browser History -->
        <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nomor Order</th>
                    <th>Tanggal Order</th>
                    <th>Nomor PO</th>
                    <th>Tanggal PO</th>
                    <th>Pemasok</th>
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
                        <td><?php echo $item['NamaPemasok']; ?></td>
                        <td>Rp.<?php echo number_format($item['TotalHarga'], 2); ?></td>
                        <td>
                            <span class="badge bg-success">
                                <?php echo $item['type']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="?action=kirim&NomorOrder=<?php echo $item['NomorOrder']; ?>" class="btn btn-primary">Teruskan Ke Pemasok!</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>