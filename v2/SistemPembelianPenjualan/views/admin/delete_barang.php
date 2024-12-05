<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';

// Delete barang berdasarkan ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $conn->prepare("DELETE FROM Barang WHERE KodeBarang = ?");
    $query->execute([$id]);

    header('Location: list_barang.php');
} else {
    header('Location: list_barang.php');
    exit();
}
