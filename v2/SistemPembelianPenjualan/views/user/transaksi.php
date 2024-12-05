<?php
session_start();

// Pastikan hanya user/pelanggan yang bisa mengakses halaman ini
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'user') {
    header('Location: ../login.php'); // Redirect ke halaman login jika bukan user
    exit();
}

include '../../db/config.php';

// Pastikan ada parameter KodeBarang di URL
if (isset($_GET['id'])) {
    $kodeBarang = $_GET['id'];

    // Ambil data barang berdasarkan KodeBarang
    $query = $conn->prepare("SELECT KodeBarang, NamaBarang, HargaBeli, Stock FROM Barang WHERE KodeBarang = ?");
    $query->execute([$kodeBarang]);
    $barang = $query->fetch(PDO::FETCH_ASSOC);

    if (!$barang) {
        header('Location: list_barang_user.php');
        exit();
    }
}


$username = $_SESSION['username'];



$queryPelanggan = $conn->prepare("SELECT KodePelanggan FROM pelanggan WHERE NamaPelanggan = ?");
$queryPelanggan->execute([$username]);
$pelanggan = $queryPelanggan->fetch(PDO::FETCH_ASSOC);


if (!$pelanggan) {
    echo "Pelanggan tidak ditemukan.";
    exit();
}

$kodePelanggan = $pelanggan['KodePelanggan'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kodeBarang = $_POST['KodeBarang'];
    $jumlahBeli = $_POST['JumlahBeli'];
    $totalHarga = $_POST['HargaBeli'] * $jumlahBeli;


    $tanggalOrder = date("Y-m-d H:i:s"); // Tanggal saat transaksi dilakukan
    $tanggalPO = $_POST['TanggalPO']; // Tanggal PO yang dipilih oleh user
    $nomorPO = rand(1000, 9999); // Generate nomor PO yang unik, misalnya




    $query = $conn->prepare("INSERT INTO Transaksi (TanggalOrder, TanggalPO, KodePelanggan, NomorPO, KodeBarang, JumlahBeli, TotalHarga) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $query->execute([$tanggalOrder, $tanggalPO, $kodePelanggan, $nomorPO, $kodeBarang, $jumlahBeli, $totalHarga]);


    $newStock = $barang['Stock'] - $jumlahBeli;


    $updateStock = $conn->prepare("UPDATE Barang SET Stock = ? WHERE KodeBarang = ?");
    $updateStock->execute([$newStock, $kodeBarang]);


    header('Location: list_transaksi_user.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Transaksi Pembelian</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="KodeBarang" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="KodeBarang" name="KodeBarang" value="<?php echo $barang['KodeBarang']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="NamaBarang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="NamaBarang" value="<?php echo $barang['NamaBarang']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="HargaBeli" class="form-label">Harga Beli</label>
                <input type="number" class="form-control" id="HargaBeli" name="HargaBeli" value="<?php echo $barang['HargaBeli']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="JumlahBeli" class="form-label">Jumlah Beli</label>
                <input type="number" class="form-control" id="JumlahBeli" name="JumlahBeli" min="1" max="<?php echo $barang['Stock']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="TanggalPO" class="form-label">Tanggal PO</label>
                <!-- Input untuk memilih tanggal PO -->
                <input type="date" class="form-control" id="TanggalPO" name="TanggalPO" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="TotalHarga" class="form-label">Total Harga</label>
                <input type="number" class="form-control" id="TotalHarga" value="<?php echo $barang['HargaBeli'] * 1; ?>" readonly> <!-- Default 1 item -->
            </div>
            <button type="submit" class="btn btn-success">Beli</button>
            <!-- Tombol Kembali menggunakan browser history -->
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        // JavaScript untuk menghitung TotalHarga secara otomatis
        document.getElementById('JumlahBeli').addEventListener('input', function() {
            var hargaBeli = document.getElementById('HargaBeli').value;
            var jumlahBeli = this.value;
            var totalHarga = hargaBeli * jumlahBeli;
            document.getElementById('TotalHarga').value = totalHarga;
        });
    </script>
</body>

</html>