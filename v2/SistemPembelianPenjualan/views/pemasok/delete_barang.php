<?php
session_start();
include '../../db/config.php';


if (isset($_GET['id'])) {
    $kodeBarang = $_GET['id'];

    $query = "DELETE FROM barang_pemasok WHERE KodeBarang = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$kodeBarang]);

    header("Location: barang_pemasok.php"); // Redirect to view page
}
