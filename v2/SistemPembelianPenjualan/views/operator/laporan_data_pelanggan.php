<?php
session_start();

// Check if user is logged in and has the correct role (operator)
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'operator') {
    header('Location: ../login.php'); // Redirect to login page if not logged in or not operator
    exit();
}

include '../../db/config.php';  // Include your DB connection

// Fetch all pelanggan (customers) and the total number of items purchased by each customer
$query = $conn->prepare("
    SELECT p.*, 
           (SELECT SUM(dt.jumlah) 
            FROM transaksi t
            JOIN detail_transaksi dt ON t.NomorOrder = dt.NomorOrder
            WHERE t.KodePelanggan = p.KodePelanggan) AS total_barang,
           (SELECT GROUP_CONCAT(b.NamaBarang SEPARATOR ', ') 
            FROM transaksi t
            JOIN detail_transaksi dt ON t.NomorOrder = dt.NomorOrder
            JOIN barang b ON dt.kode_barang = b.KodeBarang
            WHERE t.KodePelanggan = p.KodePelanggan) AS barang_dibeli
    FROM pelanggan p
");
$query->execute();
$pelangganList = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Optional: Style to format the printed page */
        @media print {
            body {
                font-family: Arial, sans-serif;
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
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h3>Daftar Pelanggan</h3>
        <a href="add_pelanggan.php" class="btn btn-success mb-3 no-print    ">Tambah Pelanggan</a>
        <!-- Tombol Kembali Menggunakan Browser History -->
        <a href="javascript:history.back()" class="btn btn-secondary mb-3 no-print">Kembali</a>

        <!-- Print Button -->
        <button class="btn btn-primary mb-3 no-print" onclick="window.print()">Print Laporan</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Pelanggan</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Total Barang Dibeli</th>
                    <th class="no-print">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pelangganList as $pelanggan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pelanggan['KodePelanggan']); ?></td>
                        <td><?php echo htmlspecialchars($pelanggan['NamaPelanggan']); ?></td>
                        <td><?php echo htmlspecialchars($pelanggan['AlamatPelanggan']); ?></td>
                        <td><?php echo htmlspecialchars($pelanggan['NoTelpPelanggan']); ?></td>
                        <td><?php echo $pelanggan['total_barang'] ? $pelanggan['total_barang'] : 0; ?> items</td>
                        <td class="no-print">
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#itemsModal<?php echo $pelanggan['KodePelanggan']; ?>">
                                Lihat Barang
                            </button>
                        </td>
                    </tr>

                    <!-- Modal for displaying purchased items -->
                    <div class="modal fade" id="itemsModal<?php echo $pelanggan['KodePelanggan']; ?>" tabindex="-1" aria-labelledby="itemsModalLabel<?php echo $pelanggan['KodePelanggan']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="itemsModalLabel<?php echo $pelanggan['KodePelanggan']; ?>">Barang yang Dibeli oleh <?php echo htmlspecialchars($pelanggan['NamaPelanggan']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Barang yang dibeli:</strong> <?php echo htmlspecialchars($pelanggan['barang_dibeli']) ? $pelanggan['barang_dibeli'] : 'Tidak ada barang yang dibeli'; ?></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        // Add functionality for printing the page
        function printPage() {
            window.print();
        }
    </script>
</body>

</html>