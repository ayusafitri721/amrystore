<?php
include '../../db/config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['transaksi'], $data['detailtransaksi'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit();
}

$transaksi = $data['transaksi'];
$detailTransaksi = $data['detailtransaksi'];


try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("INSERT INTO transaksi (TanggalOrder, KodePelanggan, NomorPO, TanggalPO, TotalHarga, type) 
                            VALUES (:TanggalOrder, :KodePelanggan, :NomorPO, :TanggalPO, :TotalHarga, :type)");
    $stmt->bindParam(':TanggalOrder', $transaksi['TanggalOrder']);
    $stmt->bindParam(':KodePelanggan', $transaksi['KodePelanggan']);
    $stmt->bindParam(':NomorPO', $transaksi['NomorPO']);
    $stmt->bindParam(':TanggalPO', $transaksi['TanggalOrder']);
    $stmt->bindParam(':TotalHarga', $transaksi['TotalHarga']);
    $stmt->bindParam(':type', $transaksi['type']);
    $stmt->execute();

    $transaksiId = $conn->lastInsertId();

    foreach ($detailTransaksi as $item) {
        $stmt = $conn->prepare("INSERT INTO detail_transaksi (NomorOrder, kode_barang, jumlah) 
                                VALUES (:NomorOrder, :kode_barang, :jumlah)");
        $stmt->bindParam(':NomorOrder', $transaksiId);
        $stmt->bindParam(':kode_barang', $item['kode_barang']);
        $stmt->bindParam(':jumlah', $item['jumlah']);
        $stmt->execute();


        $stmt = $conn->prepare("UPDATE barang SET Stock = Stock - :jumlah WHERE KodeBarang = :kode_barang");
        $stmt->bindParam(':jumlah', $item['jumlah']);
        $stmt->bindParam(':kode_barang', $item['kode_barang']);
        $stmt->execute();
    }

    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
