<?php
session_start();
include '../db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $nomor_handphone = $_POST['nomor_handphone'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password untuk keamanan

    // Validasi apakah nomor telepon sudah ada
    $query_check = $conn->prepare("SELECT * FROM pelanggan WHERE NoTelpPelanggan = :nomor_handphone");
    $query_check->bindParam(':nomor_handphone', $nomor_handphone, PDO::PARAM_STR);
    $query_check->execute();

    if ($query_check->rowCount() > 0) {
        $error_message = "Nomor handphone sudah digunakan! Silakan gunakan nomor lain.";
    } else {
        // Tambahkan data pelanggan baru
        $query_insert = $conn->prepare("
            INSERT INTO pelanggan (NamaPelanggan, AlamatPelanggan, NoTelpPelanggan)
            VALUES (:nama, :alamat, :nomor_handphone)
        ");
        $query_insert->bindParam(':nama', $nama, PDO::PARAM_STR);
        $query_insert->bindParam(':alamat', $alamat, PDO::PARAM_STR);
        $query_insert->bindParam(':nomor_handphone', $nomor_handphone, PDO::PARAM_STR);

        if ($query_insert->execute()) {

            $query_insert_user = $conn->prepare("
        INSERT INTO users (Username, Password, Level)
        VALUES (:username, :password, :level)
        ");


            $level = "user";

            $query_insert_user->bindParam(':username', $nama, PDO::PARAM_STR);
            $query_insert_user->bindParam(':password', $_POST['password'], PDO::PARAM_STR); // Ensure password is hashed before insertion
            $query_insert_user->bindParam(':level', $level, PDO::PARAM_STR);
            $query_insert_user->execute();

            // Set session dan redirect ke dashboard
            $_SESSION['username'] = $username;
            $_SESSION['level'] = 'user'; // Level user sebagai pelanggan
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3e9ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .register-container {
            max-width: 450px;
            width: 100%;
            padding: 30px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(153, 102, 255, 0.2);
        }

        .register-container h3 {
            font-weight: bold;
            margin-bottom: 10px;
            color: #6f42c1;
            text-align: center;
        }

        .register-container p {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            position: relative;
            margin-bottom: 15px;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #d8b4f1;
            padding: 10px 15px 10px 40px;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #a66dff;
            box-shadow: 0 0 5px rgba(166, 109, 255, 0.5);
        }

        .form-group i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #a66dff;
        }

        .btn-primary {
            background: linear-gradient(45deg, #d8b4f1, #a66dff);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            width: 100%;
            color: white;
            font-weight: bold;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #a66dff, #6f42c1);
        }

        .footer-text {
            margin-top: 15px;
            font-size: 0.85rem;
            color: #6c757d;
            text-align: center;
        }

        .footer-text a {
            color: #a66dff;
            text-decoration: none;
            font-weight: bold;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        .alert {
            font-size: 0.9rem;
            margin-bottom: 15px;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h3>Sign Up</h3>
        <p>Join us and start your journey!</p>

        <!-- Tampilkan pesan error jika ada -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="registrasi.php" method="POST">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" name="nama" placeholder="Nama Lengkap" required>
            </div>
            <div class="form-group">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" class="form-control" name="alamat" placeholder="Alamat" required>
            </div>
            <div class="form-group">
                <i class="fas fa-phone-alt"></i>
                <input type="text" class="form-control" name="nomor_handphone" placeholder="Nomor Handphone" required>
            </div>
            <div class="form-group">
                <i class="fas fa-user-circle"></i>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <p class="footer-text">Already have an account? <a href="login.php">Log In</a></p>
    </div>
</body>
</html>