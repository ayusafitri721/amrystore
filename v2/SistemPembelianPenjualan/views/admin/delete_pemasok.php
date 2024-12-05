<?php
session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php';  // Include your database configuration

// Ambil ID pemasok yang ingin dihapus
if (!isset($_GET['id'])) {
    header('Location: list_pemasok.php');
    exit();
}

$id = $_GET['id'];

// Hapus data pemasok berdasarkan ID
$query = $conn->prepare("DELETE FROM pemasok WHERE KodePemasok = :id");
$query->bindParam(':id', $id);

if ($query->execute()) {
    $success = "Pemasok berhasil dihapus!";
} else {
    $error = "Gagal menghapus pemasok!";
}

header('Location: list_pemasok.php');
exit();
?>