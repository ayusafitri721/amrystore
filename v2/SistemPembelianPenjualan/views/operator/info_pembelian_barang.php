<?php
session_start();

// Check if the user is logged in and has the correct role (operator)
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'operator') {
    header('Location: ../login.php'); // Redirect to login page if not logged in or not operator
    exit();
}

include '../../db/config.php';  // Include your DB connection

// Check if date range filter is set
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : null;
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : null;

// Prepare the base query
$queryStr = "
    SELECT t.NomorOrder, t.TanggalOrder, t.status, b.NamaBarang, SUM(dt.jumlah) AS total_barang, p.NamaPemasok
    FROM transaksi t
    JOIN detail_transaksi dt ON t.NomorOrder = dt.NomorOrder
    JOIN barang b ON dt.kode_barang = b.KodeBarang
    JOIN pemasok p ON t.KodeSupplier = p.KodePemasok  -- Join with the pemasok table to get supplier name
    WHERE t.type = 'beli'
";

// Add date range filter if provided
if ($dateFrom && $dateTo) {
    $queryStr .= " AND t.TanggalOrder BETWEEN :dateFrom AND :dateTo";
}

$queryStr .= " GROUP BY t.NomorOrder, t.TanggalOrder, b.NamaBarang, p.NamaPemasok";

// Prepare and execute the query
$query = $conn->prepare($queryStr);

// Bind parameters if date range is set
if ($dateFrom && $dateTo) {
    $query->bindParam(':dateFrom', $dateFrom);
    $query->bindParam(':dateTo', $dateTo);
}

$query->execute();

// Fetch the results
$pembelianList = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian Barang</title>
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
        <h3>Laporan Pembelian Barang</h3>

        <!-- Filter form for date range -->
        <form method="GET" action="" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" name="date_from" id="date_from" value="<?php echo $dateFrom; ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="date_to" id="date_to" value="<?php echo $dateTo; ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-4">Filter</button>
                </div>
            </div>
        </form>

        <!-- Print Button -->
        <button class="btn btn-primary mb-3 no-print" onclick="window.print()">Print Laporan</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nomor Order</th>
                    <th>Tanggal Order</th>
                    <th>Nama Barang</th>
                    <th>Total Barang</th>
                    <th>Status</th>
                    <th>Nama Pemasok</th> <!-- Column for supplier name -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pembelianList as $pembelian): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pembelian['NomorOrder']); ?></td>
                        <td><?php echo htmlspecialchars($pembelian['TanggalOrder']); ?></td>
                        <td><?php echo htmlspecialchars($pembelian['NamaBarang']); ?></td>
                        <td><?php echo htmlspecialchars($pembelian['total_barang']); ?> items</td>
                        <td><?php echo htmlspecialchars($pembelian['status']); ?></td>
                        <td><?php echo htmlspecialchars($pembelian['NamaPemasok']); ?></td> <!-- Display supplier name -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>