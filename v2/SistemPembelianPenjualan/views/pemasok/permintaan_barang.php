<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'pemasok') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';

$idPemasok = $_SESSION['id_pemasok'];

if (isset($_GET['action']) && $_GET['action'] == 'kirim' && isset($_GET['NomorOrder'])) {
    $nomorOrder = $_GET['NomorOrder'];

    // Start a transaction to ensure atomicity
    $conn->beginTransaction();

    try {
        // Update the status of the transaction to 'kirim'
        $updateQuery = $conn->prepare("UPDATE transaksi SET status = 'kirim' WHERE NomorOrder = :NomorOrder AND KodeSupplier = :supplier");
        $updateQuery->bindParam(':NomorOrder', $nomorOrder, PDO::PARAM_INT);
        $updateQuery->bindParam(':supplier', $idPemasok, PDO::PARAM_INT);

        if ($updateQuery->execute()) {
            // Fetch the details of the transaction (quantities) from detail_transaksi
            $detailQuery = $conn->prepare("SELECT kode_barang, jumlah FROM detail_transaksi WHERE NomorOrder = :NomorOrder");
            $detailQuery->bindParam(':NomorOrder', $nomorOrder, PDO::PARAM_INT);
            $detailQuery->execute();
            $detailTransaksi = $detailQuery->fetchAll(PDO::FETCH_ASSOC);

            // Update stock in the barang table based on the quantities in detail_transaksi
            foreach ($detailTransaksi as $item) {
                $kodeBarang = $item['kode_barang'];
                $jumlah = $item['jumlah'];

                // Update the stock in the barang table
                $updateStockQuery = $conn->prepare("UPDATE barang SET Stock = Stock + :jumlah WHERE KodeBarang = :kodeBarang");
                $updateStockQuery->bindParam(':jumlah', $jumlah, PDO::PARAM_INT);
                $updateStockQuery->bindParam(':kodeBarang', $kodeBarang, PDO::PARAM_INT);
                $updateStockQuery->execute();
            }

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
$query = $conn->prepare("SELECT * FROM transaksi WHERE KodeSupplier = :supplier AND status = 'pending'");
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
                            <span class="badge bg-success"><?php echo $item['type']; ?></span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalDetail"
                                    onclick="loadDetail(<?php echo $item['NomorOrder']; ?>)">Detail</a>
                                <a href="?action=kirim&NomorOrder=<?php echo $item['NomorOrder']; ?>" class="btn btn-primary">Kirim</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLabel">Detail Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="detailBarangBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadDetail(nomorOrder) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_detail_transaksi.php?NomorOrder=' + nomorOrder, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    console.log(data)
                    const detailBarangBody = document.getElementById('detailBarangBody');
                    detailBarangBody.innerHTML = '';

                    data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.kode_barang}</td>
                            <td>${item.NamaBarang}</td>
                            <td>${item.jumlah}</td>
                        `;
                        detailBarangBody.appendChild(row);
                    });
                } else {
                    alert('Failed to load details.');
                }
            };
            xhr.send();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>