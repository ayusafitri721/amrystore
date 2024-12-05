<?php
include '../../db/config.php';

if (isset($_GET['NomorOrder'])) {
    $nomorOrder = $_GET['NomorOrder'];

    // Ambil detail transaksi berdasarkan NomorOrder
    $query = $conn->prepare("SELECT dt.kode_barang, b.NamaBarang, dt.jumlah 
                             FROM detail_transaksi dt 
                             JOIN barang b ON dt.kode_barang = b.KodeBarang
                             WHERE dt.NomorOrder = :NomorOrder");
    $query->bindParam(':NomorOrder', $nomorOrder, PDO::PARAM_INT);
    $query->execute();

    $detailTransaksi = $query->fetchAll(PDO::FETCH_ASSOC);

    // Mengembalikan data dalam format JSON
    echo json_encode($detailTransaksi);
}
