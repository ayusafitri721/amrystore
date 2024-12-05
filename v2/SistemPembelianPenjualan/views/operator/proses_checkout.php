<?php
include '../../db/config.php';

// Get raw POST data and decode it
$data = json_decode(file_get_contents('php://input'), true);


// Check if the data is valid
if (!$data || !isset($data['transaksi'], $data['detailtransaksi'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit();
}

// Extract transaction and cart details
$transaksi = $data['transaksi'];
$detailTransaksi = $data['detailtransaksi'];


// Insert into the transaksi table
try {
    // Start transaction
    $conn->beginTransaction();

    $status = 'admin';

    // Insert into transaksi table
    $stmt = $conn->prepare("INSERT INTO transaksi (TanggalOrder, KodeSupplier, NomorPO, TanggalPO, TotalHarga, type , status) 
                            VALUES (:TanggalOrder, :KodePemasok, :NomorPO, :TanggalPO, :TotalHarga, :type,:status )");
    $stmt->bindParam(':TanggalOrder', $transaksi['TanggalOrder']);
    $stmt->bindParam(':KodePemasok', $transaksi['KodePemasok']);
    $stmt->bindParam(':NomorPO', $transaksi['NomorPO']);
    $stmt->bindParam(':TanggalPO', $transaksi['TanggalOrder']);
    $stmt->bindParam(':TotalHarga', $transaksi['TotalHarga']);
    $stmt->bindParam(':type', $transaksi['type']);
    $stmt->bindParam(':status', $status);

    $stmt->execute();

    // Get the last inserted transaction ID
    $transaksiId = $conn->lastInsertId();

    // Insert into detail_transaksi table
    foreach ($detailTransaksi as $item) {
        $stmt = $conn->prepare("INSERT INTO detail_transaksi (NomorOrder, kode_barang, jumlah) 
                                VALUES (:NomorOrder, :kode_barang, :jumlah)");
        $stmt->bindParam(':NomorOrder', $transaksiId);
        $stmt->bindParam(':kode_barang', $item['kode_barang']);
        $stmt->bindParam(':jumlah', $item['jumlah']);
        $stmt->execute();
    }

    // Commit the transaction
    $conn->commit();

    // Return a success response
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback the transaction if an error occurs
    $conn->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
