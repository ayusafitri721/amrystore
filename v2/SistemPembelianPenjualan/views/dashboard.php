<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['level'];

// Ambil data stok barang yang hampir habis untuk admin/operator
$lowStockItems = [];
if ($role == 'admin' || $role == 'operator') {
    $stmt = $conn->prepare("SELECT NamaBarang, Stock FROM Barang WHERE Stock < 2");
    $stmt->execute();
    $lowStockItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$menu = '';

if ($role === 'admin') {
    $menu = '
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarAdminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Admin Menu
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarAdminDropdown">
                <li><a class="dropdown-item" href="admin/list_barang.php">Kelola Barang</a></li>
                <li><a class="dropdown-item" href="admin/list_transaksi.php">Lihat Transaksi</a></li>
                <li><a class="dropdown-item" href="admin/list_pelanggan.php">Daftar Pelanggan</a></li>
                <li><a class="dropdown-item" href="admin/list_pemasok.php">Daftar Pemasok</a></li>
                <li><a class="dropdown-item" href="admin/list_operator.php">Daftar Operator</a></li>
                <li><a class="dropdown-item" href="admin/list_permintaan.php">Permintaan Barang</a></li>
            </ul>
        </li>
    ';
} elseif ($role === 'operator') {
    $menu = '
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarOperatorDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Manager Menu
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarOperatorDropdown">
                <li><a class="dropdown-item" href="operator/list_barang.php">Kelola Barang</a></li>
                <li><a class="dropdown-item" href="operator/list_transaksi_operator.php">Beli Barang Ke Pemasok</a></li>
                <li><a class="dropdown-item" href="operator/laporan_data_pelanggan.php">Laporan Data Pelanggan</a></li>
                <li><a class="dropdown-item" href="operator/laporan_pendapatan.php">Laporan Pendapatan</a></li>
                <li><a class="dropdown-item" href="operator/laporan_pengeluaran.php">Laporan Pengeluaran</a></li>
                <li><a class="dropdown-item" href="operator/info_pembelian_barang.php">Info Pembelian Barang</a></li>
            </ul>
        </li>
    ';
} elseif ($role === 'user') {
    $menu = '
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                User Menu
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarUserDropdown">
                <li><a class="dropdown-item" href="user/list_barang.php">Lihat Barang</a></li>
                <li><a class="dropdown-item" href="user/list_transaksi_user.php">Lihat Transaksi</a></li>
                <li><a class="dropdown-item" href="user/profile.php">Profile</a></li>
            </ul>
        </li>
    ';
} elseif ($role === 'pemasok') {
    $menu = '
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarPemasokDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Pemasok Menu
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarPemasokDropdown">
                <li><a class="dropdown-item" href="pemasok/barang_pemasok.php">Data Kelola Barang</a></li>
                <li><a class="dropdown-item" href="pemasok/permintaan_barang.php">Permintaan Barang</a></li>
                <li><a class="dropdown-item" href="pemasok/list_transaksi_pemasok.php">Riwayat Transaksi</a></li>
                <li><a class="dropdown-item" href="pemasok/buat_laporan.php">Buat Laporan</a></li>
            </ul>
        </li>
    ';
} else {
    $menu = '<li class="nav-item"><span class="nav-link text-danger">Tetot gabisa masuk</span></li>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3e5f5;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background: linear-gradient(45deg, #e1bee7, #ba68c8);
            padding: 15px 0;
        }

        .navbar .nav-link {
            color: white;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .navbar .nav-link:hover {
            color: #ff4081;
        }

        .navbar .navbar-brand {
            font-size: 1.8em;
            font-weight: bold;
            color: white;
        }

        .navbar .navbar-nav li {
            margin-right: 15px;
        }

        
        .content-box {
    background: linear-gradient(135deg, #e9d3ff, #f3e8ff); 
    padding: 50px; /* Menambah padding */
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 100%; 
    max-width: 100%; 
    margin: 20px 0;
    color: #4a148c;
}

.alert-danger {
    background: linear-gradient(120deg, #ffccbc, #ffab91); 
    color: #d84315; 
}



        .section-title {
            color: #6a1b9a;
            text-align: center;
            margin-bottom: 20px;
        }

        .card-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}


.card-1:hover {
    background-color: #ffe082; 
}

.card-2:hover {
    background-color: #80deea; 
}

.card-3:hover {
    background-color: #a5d6a7; 
}

.card-4:hover {
    background-color: #ef9a9a; 
}

.card-5:hover {
    background-color: #ce93d8; 
}

.card-6:hover {
    background-color: #ffab91; 
}

.card-7:hover {
    background-color: #A59D84; 
}

.card-8:hover {
    background-color: #F0C1E1;
}



.card-deck {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}


    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Amry Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php echo $menu; ?>
                    <li class="nav-item"><a href="#produkKami" class="nav-link">Produk Kami</a></li>
                    <li class="nav-item"><a href="#tentangKami" class="nav-link">Tentang Kami</a></li>
                    <li class="nav-item">
                        <a href="../process/logout.php" class="nav-link logout-btn">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="content-box">
            <h1 class="text-center">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p class="text-center">Amry Store menyediakan berbagai produk berkualitas dengan harga terjangkau.</p>
        </div>

        <?php if ($role == 'admin' || $role == 'operator') : ?>
            <?php if (!empty($lowStockItems)): ?>
                <div class="content-box alert alert-danger">
                    <h4 class="alert-heading">Stock Barang Habis</h4>
                    <ul>
                        <?php foreach ($lowStockItems as $item): ?>
                            <li><?php echo htmlspecialchars($item['NamaBarang']); ?> - Stock: <?php echo $item['Stock']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div id="produkKami" class="content-box">
    <h2 class="section-title">Produk Kami</h2>
    <div class="card-deck">
        <!-- Produk Cards -->
        <div class="card card-hover card-1">
            <img src="nike.jpeg" class="card-img-top" alt="Produk 1">
            <div class="card-body">
                <h5 class="card-title">Nike</h5>
                <p class="card-text">Sepatu olahraga berkualitas tinggi untuk kenyamanan maksimal.</p>
            </div>
        </div>
        <div class="card card-hover card-2">
            <img src="kacamata.jpeg" class="card-img-top" alt="Produk 2">
            <div class="card-body">
                <h5 class="card-title">Kaca Mata Luna</h5>
                <p class="card-text">Kacamata stylish yang cocok untuk aktivitas sehari-hari.</p>
            </div>
        </div>
        <div class="card card-hover card-3">
            <img src="tempatmakan.jpeg" class="card-img-top" alt="Produk 3">
            <div class="card-body">
                <h5 class="card-title">Tempat Makan</h5>
                <p class="card-text">Tempat makan kedap udara yang menjaga makanan tetap segar.</p>
            </div>
        </div>
        <div class="card card-hover card-4">
            <img src="boneka.jpeg" class="card-img-top" alt="Produk 4">
            <div class="card-body">
                <h5 class="card-title">Boneka</h5>
                <p class="card-text">Boneka lucu dan lembut, cocok untuk hadiah atau koleksi.</p>
            </div>
        </div>
        <div class="card card-hover card-5">
            <img src="shirt.jpeg" class="card-img-top" alt="Produk 5">
            <div class="card-body">
                <h5 class="card-title">Sweeter</h5>
                <p class="card-text">Kemeja kasual yang nyaman untuk digunakan sehari-hari.</p>
            </div>
        </div>
        <div class="card card-hover card-6">
            <img src="tumblerharpot.jpeg" class="card-img-top" alt="Produk 6">
            <div class="card-body">
                <h5 class="card-title">Tumbler</h5>
                <p class="card-text">Tumbler bergaya dengan desain Harry Potter yang unik.</p>
            </div>
        </div>
        <div class="card card-hover card-7">
            <img src="jamtangan.jpeg" class="card-img-top" alt="Produk 7">
            <div class="card-body">
                <h5 class="card-title">Jam Tangan</h5>
                <p class="card-text">Jam tangan bertema Harry Potter dengan desain eksklusif yang menampilkan elemen magis seperti lambang Hogwarts dan sentuhan warna khas dunia sihir.</p>
            </div>
        </div>
        <div class="card card-hover card-8">
            <img src="gaming.jpeg" class="card-img-top" alt="Produk 8">
            <div class="card-body">
                <h5 class="card-title">Headphone Gaming</h5>
                <p class="card-text">Headphone gaming dengan suara surround 7.1 yang menghadirkan pengalaman bermain game yang imersif.</p>
            </div>
        </div>
    </div>
</div>


<div id="tentangKami" class="content-box">
    <h2 class="section-title">Tentang Kami</h2>
    <div class="row justify-content-center">
        <div class="col-md-4 text-center">
            <img src="Ayu Safitri.jpg" class="img-fluid rounded-circle shadow-lg" alt="Owner" style="max-width: 250px;">
        </div>
        <div class="col-md-8">
            <h3 class="text-center">Selamat Datang di Amry Store!</h3>
            <p class="text-justify">Amry Store adalah toko yang didirikan dengan tujuan untuk memberikan produk berkualitas tinggi dengan harga terjangkau. Kami percaya bahwa setiap orang berhak mendapatkan produk terbaik tanpa harus merogoh kocek yang dalam. Toko ini dipimpin oleh <strong>Ayu Safitri Al-Amry</strong>, seorang pengusaha muda yang berkomitmen untuk menghadirkan inovasi dan pelayanan terbaik di setiap transaksi yang kami lakukan.</p>
            <p class="text-justify">Dengan latar belakang di bidang teknologi (ngoding), Ayu Safitri memulai perjalanan Amry Store untuk mempermudah masyarakat dalam memperoleh barang berkualitas dengan pengalaman belanja yang nyaman. Kami berusaha untuk terus berkembang dan memperkenalkan produk-produk baru yang dapat memenuhi kebutuhan pelanggan kami.</p>
            <p class="text-justify">Kami percaya bahwa pelanggan adalah prioritas utama. Oleh karena itu, kami selalu siap mendengarkan feedback Anda untuk meningkatkan kualitas pelayanan kami.</p>
        </div>
    </div>

    <div class="text-center mt-5">
        <h4 class="mb-4">Visi dan Misi Kami</h4>
        <div class="row">
            <div class="col-md-6">
                <h5><strong>Visi</strong></h5>
                <p>Menjadi toko online terdepan yang menyediakan produk berkualitas tinggi dengan harga yang bersaing.</p>
            </div>
            <div class="col-md-6">
                <h5><strong>Misi</strong></h5>
                <ul class="list-unstyled">
                    <li>1. Menyediakan berbagai macam produk berkualitas dengan harga yang terjangkau.</li>
                    <li>2. Memberikan pengalaman berbelanja yang mudah, aman, dan nyaman.</li>
                    <li>3. Berinovasi dan selalu mengutamakan kepuasan pelanggan.</li>
                </ul>
            </div>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
