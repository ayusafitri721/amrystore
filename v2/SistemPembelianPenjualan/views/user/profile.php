<?php
include('../../db/config.php');

session_start();

$username = $_SESSION['username'] ?? '';

if ($username) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE Username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $conn->prepare("SELECT * FROM pelanggan WHERE NamaPelanggan = :username");
    $stmt2->execute(['username' => $username]);
    $pelanggan = $stmt2->fetch(PDO::FETCH_ASSOC);
} else {
    echo "Username tidak ditemukan.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f3e5f5; /* Latar belakang ungu muda */
            font-family: 'Arial', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        h1 {
            color: #4a148c; /* Warna ungu tua */
        }

        .card {
            background-color: #f9f4ff; /* Background card ungu muda */
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            color: #6a1b9a; /* Teks ungu */
        }

        .card-title {
            color: #6a1b9a;
            font-weight: bold;
        }

        .modal-content {
            background-color: #f3e5f5; /* Background modal */
            border-radius: 10px;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ba68c8;
        }

        .btn-primary {
            background-color: #ba68c8;
            border-color: #ba68c8;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #9c4d99;
            border-color: #9c4d99;
        }

        .modal-header {
            background-color: #ba68c8; /* Header modal warna ungu */
            color: white;
        }

        .btn-close {
            color: white;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1>Profile - <?php echo htmlspecialchars($user['Username']); ?></h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">User Information</h5>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['Username']); ?></p>
                <p><strong>Password:</strong> (hidden for security)</p>
                <h5 class="mt-4">Customer Information</h5>
                <p><strong>Nama Pelanggan:</strong> <?php echo htmlspecialchars($pelanggan['NamaPelanggan']); ?></p>
                <p><strong>Alamat:</strong> <?php echo htmlspecialchars($pelanggan['AlamatPelanggan']); ?></p>
                <p><strong>Telepon:</strong> <?php echo htmlspecialchars($pelanggan['NoTelpPelanggan']); ?></p>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateProfileModal">Update Profile</button>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="update_profile.php" method="POST">
                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['Username']); ?>">

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo htmlspecialchars($pelanggan['AlamatPelanggan']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" value="<?php echo htmlspecialchars($pelanggan['NoTelpPelanggan']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
