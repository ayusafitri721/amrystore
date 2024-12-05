<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'pemasok') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';

if (!isset($_GET['id'])) {
    echo "ID transaksi tidak ditemukan!";
    exit();
}

$NomorOrder = $_GET['id'];

$query = $conn->prepare("
    SELECT 
        t.NomorOrder,
        t.TanggalOrder,
        t.NomorPO,
        t.TanggalPO,
        dt.kode_barang AS KodeBarang,
        b.NamaBarang,
        b.HargaBeli,
        dt.jumlah AS Qty,
        (dt.jumlah * b.HargaBeli) AS TotalHarga
    FROM transaksi t
    LEFT JOIN detail_transaksi dt ON t.NomorOrder = dt.NomorOrder
    LEFT JOIN barang b ON dt.kode_barang = b.KodeBarang
    WHERE t.NomorOrder = :NomorOrder
");
$query->bindParam(':NomorOrder', $NomorOrder, PDO::PARAM_INT);
$query->execute();
$details = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>

</head>

<body>
    <div class="container mt-5">
        <h3>Detail Transaksi</h3>
        <!-- Tombol Kembali -->
        <a href="javascript:history.back()" class="btn btn-secondary mb-3 print-button">Kembali</a>
        <?php if ($details): ?>
            <!-- Informasi Utama Transaksi -->
            <div class="mb-4">
                <p><strong>Nomor Order:</strong> <?= htmlspecialchars($details[0]['NomorOrder']) ?></p>
                <p><strong>Tanggal Order:</strong> <?= htmlspecialchars($details[0]['TanggalOrder']) ?></p>
                <p><strong>Nomor PO:</strong> <?= htmlspecialchars($details[0]['NomorPO']) ?></p>
                <p><strong>Tanggal PO:</strong> <?= htmlspecialchars($details[0]['TanggalPO']) ?></p>
            </div>

            <!-- Detail Barang -->
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['KodeBarang']) ?></td>
                            <td><?= htmlspecialchars($item['NamaBarang']) ?></td>
                            <td>Rp.<?= number_format($item['HargaBeli'], 2) ?></td>
                            <td><?= htmlspecialchars($item['Qty']) ?></td>
                            <td>Rp.<?= number_format($item['TotalHarga'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button class="btn btn-primary mt-3 print-button" onclick="window.print()">Print</button>

        <?php else: ?>
            <p>Data detail transaksi tidak ditemukan!</p>
        <?php endif; ?>
    </div>

    <script>
        // Optional: Customize print styles
        const style = document.createElement('style');
        style.innerHTML = `
            @media print {
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                }
                .container {
                    width: 100%;
                }

                #transactionTable {
                    width: 100%;
                    border-collapse: collapse;
                }
                #transactionTable th, #transactionTable td {
                    padding: 8px;
                    text-align: left;
                    border: 1px solid #ddd;
                }
            }
        `;
        document.head.appendChild(style);
    </script>

</body>

</html>