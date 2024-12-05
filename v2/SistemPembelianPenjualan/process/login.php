<?php
session_start();
include '../db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query_admin = $conn->prepare("SELECT * FROM users WHERE Username = :username AND Password = :password");
    $query_admin->execute([
        ':username' => $username,
        ':password' => $password
    ]);

    $admin = $query_admin->fetch(PDO::FETCH_ASSOC);


    if ($admin) {

        if ($admin['Level'] == 'user') {
            $query_admin = $conn->prepare("SELECT * FROM pelanggan WHERE NamaPelanggan = :username");
            $query_admin->execute([
                ':username' => $username,
            ]);
            $user = $query_admin->fetch(PDO::FETCH_ASSOC);
            $_SESSION['id_pelanggan'] = $user['KodePelanggan'];
        }

        if ($admin['Level'] == 'pemasok') {
            $query_admin = $conn->prepare("SELECT * FROM pemasok WHERE Email = :email");
            $query_admin->execute([
                ':email' => $username,
            ]);
            $user = $query_admin->fetch(PDO::FETCH_ASSOC);
            $_SESSION['id_pemasok'] = $user['KodePemasok'];
        }

        $_SESSION['id'] = $admin['ID'];
        $_SESSION['username'] = $admin['Username'];
        $_SESSION['level'] = $admin['Level'];
        header("Location: ../views/dashboard.php");
        exit();
    }

    // Jika tidak ditemukan di kedua tabel
    echo "<script>alert('Invalid credentials!'); window.location.href='../views/login.php';</script>";
}
