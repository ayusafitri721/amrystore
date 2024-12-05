<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'operator') {
    header('Location: ../login.php'); // Redirect to login page if not logged in or not operator
    exit();
}

include '../../db/config.php';
// Initialize variables for filtering
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';

// Build the query with optional filters
$queryStr = "
    SELECT t.*, SUM(dt.jumlah * b.hargaBeli) AS total_pendapatan
    FROM transaksi t
    JOIN detail_transaksi dt ON t.NomorOrder = dt.NomorOrder
    JOIN barang b ON dt.kode_barang = b.KodeBarang
    WHERE t.type='jual'
";

// Add date range filter if provided
if ($startDate && $endDate) {
    $queryStr .= " AND t.TanggalOrder BETWEEN :startDate AND :endDate";
}

// Add type filter if provided
if ($type) {
    $queryStr .= " AND t.type = :type";
}

$queryStr .= " GROUP BY t.NomorOrder";

// Prepare and execute the query
$query = $conn->prepare($queryStr);

if ($startDate && $endDate) {
    $query->bindParam(':startDate', $startDate);
    $query->bindParam(':endDate', $endDate);
}



$query->execute();
$pendapatanList = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendapatan</title>
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
        <h3>Laporan Pendapatan</h3>

        <!-- Filter Form -->
        <form method="POST" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Tanggal Order (Dari)</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $startDate; ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Tanggal Order (Sampai)</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $endDate; ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-4">Filter</button>
                </div>
            </div>
        </form>

        <!-- Print Button -->
        <button class="btn btn-primary mb-3 no-print" onclick="window.print()">Print Laporan</button>

        <!-- Table for displaying the report -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nomor Order</th>
                    <th>Tanggal Order</th>
                    <th>Type Transaksi</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendapatanList as $pendapatan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pendapatan['NomorOrder']); ?></td>
                        <td><?php echo htmlspecialchars($pendapatan['TanggalOrder']); ?></td>
                        <td><?php echo htmlspecialchars($pendapatan['type']); ?></td>
                        <td><?php echo number_format($pendapatan['total_pendapatan'], 2, ',', '.'); ?></td>
                    </tr>
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