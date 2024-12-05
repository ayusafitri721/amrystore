<?php
session_start();

// Check if user is logged in and has the correct role (admin)
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php'); // Redirect to login page if not logged in or not admin
    exit();
}

include '../../db/config.php';  // Include the DB connection

// Check if there's a 'id' parameter in the URL
if (isset($_GET['id'])) {
    $kodePelanggan = $_GET['id'];

    // Check if the customer exists
    $query = $conn->prepare("SELECT * FROM pelanggan WHERE KodePelanggan = ?");
    $query->execute([$kodePelanggan]);
    $pelanggan = $query->fetch(PDO::FETCH_ASSOC);

    if ($pelanggan) {
        // Delete the customer from the database
        $deleteQuery = $conn->prepare("DELETE FROM pelanggan WHERE KodePelanggan = ?");
        $deleteQuery->execute([$kodePelanggan]);

        // Redirect to the customer list page after deletion
        header('Location: list_pelanggan.php');
        exit();
    } else {
        // If customer not found, redirect to the list page
        header('Location: list_pelanggan.php');
        exit();
    }
} else {
    // If no 'id' is provided in the URL, redirect to the list page
    header('Location: list_pelanggan.php');
    exit();
}
