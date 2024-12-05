<?php
include('../../db/config.php');
session_start();

$username = $_SESSION['username'] ?? '';

if ($username) {
    
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = $_POST['password'];
        $stmt3 = $conn->prepare("UPDATE users SET Password = :password WHERE Username = :username");
        $stmt3->execute(['password' => $password, 'username' => $username]);
    }

    
    if (isset($_POST['nama_pelanggan']) && !empty($_POST['nama_pelanggan']) && isset($_POST['telepon']) && !empty($_POST['telepon'])) {
        $namaPelanggan = $_POST['nama_pelanggan'];
        $noTelpPelanggan = $_POST['telepon'];

        
        $stmt4 = $conn->prepare("UPDATE pelanggan SET NamaPelanggan = :namaPelanggan, NoTelpPelanggan = :noTelpPelanggan WHERE NamaPelanggan = :username");
        $stmt4->execute(['namaPelanggan' => $namaPelanggan, 'noTelpPelanggan' => $noTelpPelanggan, 'username' => $username]);

   
        if ($namaPelanggan !== $username) {
            $stmt5 = $conn->prepare("UPDATE users SET Username = :newUsername WHERE Username = :username");
            $stmt5->execute(['newUsername' => $namaPelanggan, 'username' => $username]);

        
            session_destroy();
            header("Location: ../views/login.php"); 
            exit;
        }
    }

    header("Location: ../../views/dashboard.php"); 
} else {
    echo "Username tidak ditemukan.";
    exit;
}
