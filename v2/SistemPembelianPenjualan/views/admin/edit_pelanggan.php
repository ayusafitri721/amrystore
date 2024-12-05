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

    // Fetch the existing customer data from the database
    $query = $conn->prepare("SELECT * FROM desa WHERE KodePelanggan = ?");
    $query->execute([$kodePelanggan]);
    $pelanggan = $query->fetch(PDO::FETCH_ASSOC);

    // If no customer is found, redirect to the list page
    if (!$pelanggan) {
        header('Location: list_pelanggan.php');
        exit();
    }
}

// Handle form submission to update customer details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPelanggan = $_POST['NamaPelanggan'];
    $alamatPelanggan = $_POST['AlamatPelanggan'];
    $noTelpPelanggan = $_POST['NoTelpPelanggan'];

    // Update the customer data in the database
    $updateQuery = $conn->prepare("UPDATE pelanggan SET NamaPelanggan = ?, AlamatPelanggan = ?, NoTelpPelanggan = ? WHERE KodePelanggan = ?");
    $updateQuery->execute([$namaPelanggan, $alamatPelanggan, $noTelpPelanggan, $kodePelanggan]);

    // Redirect to the list of customers after updating
    header('Location: list_pelanggan.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Edit Pelanggan</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="KodePelanggan" class="form-label">Kode Pelanggan</label>
                <input type="text" class="form-control" id="KodePelanggan" name="KodePelanggan" value="<?php echo htmlspecialchars($pelanggan['KodePelanggan']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="NamaPelanggan" class="form-label">Nama Pelanggan</label>
                <input type="text" class="form-control" id="NamaPelanggan" name="NamaPelanggan" value="<?php echo htmlspecialchars($pelanggan['NamaPelanggan']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="AlamatPelanggan" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="AlamatPelanggan" name="AlamatPelanggan" value="<?php echo htmlspecialchars($pelanggan['AlamatPelanggan']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="NoTelpPelanggan" class="form-label">No. Telepon</label>
                <input type="text" class="form-control" id="NoTelpPelanggan" name="NoTelpPelanggan" value="<?php echo htmlspecialchars($pelanggan['NoTelpPelanggan']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="list_pelanggan.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
