<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'pemasok') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';

$idPemasok = $_SESSION['id_pemasok'];

// Fetch all the transactions where status is 'kirim'
$query = $conn->prepare("SELECT * FROM transaksi WHERE KodeSupplier = :supplier AND status = 'kirim'");
$query->bindParam(':supplier', $idPemasok, PDO::PARAM_INT);
$query->execute();
$transaksi = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch items for each transaction, including the item details from the barang table
function fetchTransactionDetails($nomorOrder)
{
    global $conn;
    $query = $conn->prepare("
    SELECT dt.*, b.NamaBarang, b.hargaBeli, 
    (dt.jumlah * b.hargaBeli) AS totalHarga 
    FROM detail_transaksi dt
    JOIN barang b ON dt.kode_barang = b.KodeBarang
    WHERE dt.NomorOrder = :NomorOrder
");

    $query->bindParam(':NomorOrder', $nomorOrder, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Optional: Style to format the printed page */
        @media print {
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }

            button {
                display: none;
            }

            .no-print {
                display: none;
            }

            .table th,
            .table td {
                padding: 8px;
                text-align: left;
                border: 1px solid #ddd;
            }

            .transaction-summary th,
            .transaction-summary td {
                border-top: 2px solid #ddd;
                border-bottom: 2px solid #ddd;
            }

            .item-details td {
                padding-left: 40px;
            }

            /* Improve the print layout */
            .container {
                max-width: 100%;
                margin: 0;
            }

            .table-bordered {
                border-collapse: collapse;
                width: 100%;
                margin-top: 20px;
            }

            /* Add some spacing before each row in the transaction summary */
            .transaction-summary {
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center">Daftar Transaksi</h3>
        <hr>
        <!-- Tombol Kembali Menggunakan Browser History -->
        <a href="javascript:history.back()" class="btn btn-secondary mb-3 no-print">Kembali</a>

        <!-- Print Button -->
        <button class="btn btn-primary mb-3 no-print" onclick="window.print()">Print Laporan</button>

        <table class="table table-bordered" id="transaksi-table">
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
                    <tr class="transaction-summary">
                        <td><?php echo $item['NomorOrder']; ?></td>
                        <td><?php echo date("d-m-Y", strtotime($item['TanggalOrder'])); ?></td>
                        <td><?php echo $item['NomorPO']; ?></td>
                        <td><?php echo date("d-m-Y", strtotime($item['TanggalPO'])); ?></td>
                        <td>Rp.<?php echo number_format($item['TotalHarga'], 2, ',', '.'); ?></td>
                        <td>
                            <span class="badge bg-success">
                                <?php echo $item['type']; ?>
                            </span>
                        </td>
                    </tr>

                    <!-- Details of the items bought -->
                    <?php
                    $details = fetchTransactionDetails($item['NomorOrder']);
                    foreach ($details as $detail): ?>
                        <tr class="item-details">
                            <td colspan="2">Item: <?php echo $detail['NamaBarang']; ?></td>
                            <td>Quantity: <?php echo $detail['jumlah']; ?></td>
                            <td>Harga Satuan: Rp.<?php echo number_format($detail['hargaBeli'], 2, ',', '.'); ?></td>
                            <td>Total Harga: Rp.<?php echo number_format($detail['totalHarga'], 2, ',', '.'); ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Add functionality for printing the page
        function printPage() {
            window.print();
        }
    </script>
</body>

</html>