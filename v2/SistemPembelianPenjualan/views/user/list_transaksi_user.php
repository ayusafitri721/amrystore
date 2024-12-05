<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';

$KodePelanggan = $_SESSION['id_pelanggan'];

// Ambil data transaksi yang terkait dengan barang, tanpa menggunakan kolom Qty
$query = $conn->prepare("SELECT * FROM transaksi WHERE KodePelanggan = :KodePelanggan");
$query->bindParam(':KodePelanggan', $KodePelanggan, PDO::PARAM_INT);
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
    <style>
        /* Custom style for card */
        .transaction-card {
            background-color: #f1e6ff; /* Light purple background */
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .transaction-card h5 {
            color: #6f42c1; /* Purple color for title */
        }

        .transaction-card .badge {
            background-color: #6f42c1; /* Purple color for badge */
        }

        .transaction-card .btn {
            background-color: #6f42c1; /* Purple button */
            color: white;
        }

        .transaction-card .btn:hover {
            background-color: #5a31a1; /* Darker purple on hover */
        }

        .transaction-card .details {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .transaction-card .total-price {
            font-size: 18px;
            font-weight: bold;
            color: #6f42c1;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h3>Daftar Transaksi</h3>
        <!-- Tombol Kembali Menggunakan Browser History -->
        <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>

        <!-- Looping untuk menampilkan setiap transaksi -->
        <?php foreach ($transaksi as $item): ?>
            <div class="transaction-card">
                <h5>Nomor Order: <?php echo $item['NomorOrder']; ?></h5>
                <div class="details">
                    <p><strong>Tanggal Order:</strong> <?php echo $item['TanggalOrder']; ?></p>
                    <p><strong>Nomor PO:</strong> <?php echo $item['NomorPO']; ?></p>
                    <p><strong>Tanggal PO:</strong> <?php echo $item['TanggalPO']; ?></p>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="badge bg-success"><?php echo $item['type']; ?></span>
                    <span class="total-price">Rp. <?php echo number_format($item['TotalHarga'], 2); ?></span>
                </div>
                <div class="text-end mt-3">
                    <a href="./detail_transaksi.php?id=<?= $item['NomorOrder'] ?>" class="btn btn-secondary">Detail</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>
