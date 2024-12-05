<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'user') {
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

        /* Styling untuk card box informasi transaksi dan tabel */
        .transaction-box {
            background-color: #f1e6ff; /* Light purple */
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .transaction-box h5 {
            color: #6f42c1; /* Purple for headings */
        }

        .transaction-box p {
            font-size: 16px;
            margin-bottom: 8px;
        }

        /* Styling untuk tabel transaksi */
        table {
            background-color: #f1e6ff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            text-align: left;
            padding: 12px;
        }

        table th {
            background-color: #6f42c1;
            color: white;
        }

        table td {
            background-color: #ffffff;
            color: #333;
        }

        table td span {
            font-weight: bold;
        }

        .print-button {
            background-color: #6f42c1;
            color: white;
        }

        .print-button:hover {
            background-color: #5a31a1;
        }

        /* Styling tombol Print dan Kembali */
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button-container .btn {
            width: 48%; /* Menyusun tombol berdampingan */
        }
    </style>

</head>

<body>
    <div class="container mt-5">
        <h3>Detail Transaksi</h3>

        <!-- Box untuk informasi utama transaksi dan tabel detail barang -->
        <div class="transaction-box mb-4">
            <h5>Informasi Transaksi</h5>
            <p><strong>Nomor Order:</strong> <?= htmlspecialchars($details[0]['NomorOrder']) ?></p>
            <p><strong>Tanggal Order:</strong> <?= htmlspecialchars($details[0]['TanggalOrder']) ?></p>
            <p><strong>Nomor PO:</strong> <?= htmlspecialchars($details[0]['NomorPO']) ?></p>
            <p><strong>Tanggal PO:</strong> <?= htmlspecialchars($details[0]['TanggalPO']) ?></p>

            <!-- Table untuk detail barang -->
            <h5>Detail Barang</h5>
            <table class="table table-bordered">
                <thead>
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
                            <td>Rp. <?= number_format($item['HargaBeli'], 2) ?></td>
                            <td><?= htmlspecialchars($item['Qty']) ?></td>
                            <td>Rp. <?= number_format($item['TotalHarga'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>

        <!-- Tombol Print dan Kembali sejajar -->
        <div class="button-container">
            <button class="btn btn-primary print-button" onclick="window.print()">Print</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
        </div>

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

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                table th, table td {
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
