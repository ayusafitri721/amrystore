<?php
session_start();

// Pastikan hanya user/pelanggan yang bisa mengakses halaman ini
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'user') {
    header('Location: ../login.php'); // Redirect ke halaman login jika bukan user
    exit();
}

include '../../db/config.php';

// Ambil semua data barang dari database
$query = $conn->query("SELECT KodeBarang, NamaBarang, JenisBarang, Stock, HargaBeli FROM Barang");
$barang = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    /* Tampilan Umum */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f6f9;
        color: #333;
    }

    .container {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h3, h4 {
        color: #6a1b9a;
        font-size: 24px;
        font-weight: bold;
    }

    .btn-secondary {
        background-color: #6a1b9a;
        color: white;
        border: none;
        transition: background-color 0.3s;
    }

    .btn-secondary:hover {
        background-color: #9c27b0;
    }

    /* Tab Navigation */
    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
        margin-bottom: 20px;
    }

    .tab-button {
        padding: 10px 20px;
        border: 1px solid #ddd;
        border-bottom: none;
        background-color: #f3e5f5;
        color: #6a1b9a;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .tab-button:hover {
        background-color: #9c27b0;
        color: white;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Tabel Daftar Barang */
    #barangTable,
    #cartTable {
        margin-top: 20px;
        border-collapse: collapse;
        width: 100%;
    }

    #barangTable th,
    #barangTable td,
    #cartTable th,
    #cartTable td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
    }

    #barangTable th,
    #cartTable th {
        background-color: #f3e5f5;
        font-weight: bold;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    /* Kolom Gambar */
    #barangTable td img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
    }

    .qty-input {
        width: 60px;
        text-align: center;
    }

    /* Tombol Keranjang */
    .add-to-cart {
        background-color: #6a1b9a;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .add-to-cart:disabled {
        background-color: #ccc;
    }

    .add-to-cart:hover {
        background-color: #9c27b0;
    }

    /* Keranjang Belanja */
    #cartTable td {
        vertical-align: middle;
    }

    .remove-from-cart {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .remove-from-cart:hover {
        background-color: #c0392b;
    }

    .flex {
        display: flex;
        gap: 10px;
        justify-content: space-between;
        align-items: center;
    }

    #total {
        font-size: 1.2em;
        font-weight: bold;
        color: #6a1b9a;
    }

    #checkoutButton {
        background-color: #4caf50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #checkoutButton:disabled {
        background-color: #cccccc;
    }

    #checkoutButton:hover {
        background-color: #388e3c;
    }

    .product-item {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 10px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        gap: 10px;
    }

    .product-item img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 10px;
    }

    .product-info {
        flex: 1;
    }

    .product-info h5 {
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
    }

    .product-info p {
        color: #777;
        margin-bottom: 10px;
    }

    .add-to-cart-btn {
        background-color: #6a1b9a;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .add-to-cart-btn:hover {
        background-color: #9c27b0;
    }

    /* Keranjang Belanja */
    .cart-summary {
        margin-top: 30px;
        background-color: #f3e5f5;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .cart-item {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        align-items: center;
    }

    .cart-item img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
    }

    .cart-item-info {
        flex: 1;
    }

    .cart-item-info h6 {
        margin-bottom: 5px;
        font-size: 16px;
        color: #333;
    }

    .cart-item-info p {
        color: #777;
        margin-bottom: 5px;
    }

    .cart-item-price {
        font-size: 16px;
        font-weight: bold;
        color: #6a1b9a;
    }

</style>

<body>
    <div class="container mt-5">
        <h3>Daftar Barang</h3>
        <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>
        <table class="table table-bordered" id="barangTable">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jenis Barang</th>
                    <th>Stock</th>
                    <th>Harga Beli</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($barang as $item): ?>
                    <tr data-kode="<?= $item['KodeBarang'] ?>" data-nama="<?= $item['NamaBarang'] ?>" data-harga="<?= $item['HargaBeli'] ?>">
                        <td><?= $item['KodeBarang'] ?></td>
                        <td><?= $item['NamaBarang'] ?></td>
                        <td><?= $item['JenisBarang'] ?></td>
                        <td><?= $item['Stock'] ?></td>
                        <td>Rp.<?= number_format($item['HargaBeli'], 2) ?></td>
                        <td>
                            <input type="number" class="form-control qty-input"
                                value="1"
                                max="<?= $item['Stock']; ?>"
                                min="1"
                                <?= $item['Stock'] == 0 ? 'disabled' : ''; ?>>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm add-to-cart"
                                <?= $item['Stock'] == 0 ? 'disabled' : ''; ?>>
                                Tambah
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Checkout Section -->
        <h4 class="mt-5">Keranjang Belanja</h4>
        <table class="table table-bordered" id="cartTable">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Barang akan ditambahkan melalui JavaScript -->
            </tbody>
        </table>

        <div class="flex gap-2">
            <button id="checkoutButton" class="btn btn-success" disabled>Checkout</button>
            <h4 id="total">
                Total :
            </h4>
        </div>
    </div>

    <script>
        const cart = [];
        let total = 0;
        document.getElementById('total').textContent = "Total : " + total;
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const kode = row.dataset.kode;
                const nama = row.dataset.nama;
                const harga = parseFloat(row.dataset.harga);
                const qtyInput = row.querySelector('.qty-input');
                const qty = parseInt(qtyInput.value);

                if (qty <= 0) {
                    alert("Jumlah harus lebih dari 0!");
                    return;
                }

                const existingItem = cart.find(item => item.kode === kode);
                if (existingItem) {
                    existingItem.qty += qty;
                    existingItem.totalHarga += qty * harga;
                } else {
                    cart.push({
                        kode,
                        nama,
                        harga,
                        qty,
                        totalHarga: qty * harga
                    });
                }
                total += qty * harga;

                renderCart();
            });
        });

        function renderCart() {
            const cartTableBody = document.querySelector('#cartTable tbody');
            cartTableBody.innerHTML = '';

            cart.forEach((item, index) => {
                const row = `
                    <tr>
                        <td>${item.kode}</td>
                        <td>${item.nama}</td>
                        <td>Rp.${item.harga.toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
                        <td>${item.qty}</td>
                        <td>Rp.${item.totalHarga.toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
                        <td><button class="btn btn-danger btn-sm remove-from-cart" data-index="${index}">Hapus</button></td>
                    </tr>
                `;
                cartTableBody.innerHTML += row;
            });

            document.querySelectorAll('.remove-from-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const index = this.dataset.index;
                    cart.splice(index, 1);
                    renderCart();
                });
            });

            document.getElementById('checkoutButton').disabled = cart.length === 0;

            document.getElementById('total').textContent = "Total : " + total;
        }


        document.getElementById('checkoutButton').addEventListener('click', function() {

            let transaksi = {
                TanggalOrder: new Date().toISOString().split('T')[0],
                KodePelanggan: <?php echo $_SESSION['id_pelanggan']; ?>,
                NomorPO: Math.floor(Math.random() * 100000000000),
                TanggalPo: new Date().toISOString().split('T')[0],
                TotalHarga: total,
                type: "jual"
            };

            let detailtransaksi = [];


            document.querySelectorAll('#cartTable tbody tr').forEach(function(row) {
                let kodeBarang = row.cells[0].innerText;
                let jumlah = row.cells[3].innerText;

                detailtransaksi.push({
                    kode_barang: kodeBarang,
                    jumlah: jumlah
                });
            });

            const data = {
                transaksi,
                detailtransaksi
            }

            console.log(data);

            fetch("./proses_checkout.php", {
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    method: "post"
                }).then(response => response.json())
                .then(data => {
                    if (data.success == true) {
                        alert("Berhasil checkout barang !")
                        window.location.reload()
                    } else {
                        alert(data.error);
                    }
                })
        });
    </script>
</body>

</html>