<?php
session_start();

// Pastikan hanya user/pelanggan yang bisa mengakses halaman ini
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'operator') {
    header('Location: ../login.php'); // Redirect ke halaman login jika bukan user
    exit();
}

include '../../db/config.php';

// Ambil semua data barang dari database
$query = $conn->query("SELECT KodeBarang, NamaBarang, JenisBarang, Stock, HargaBeli FROM Barang");
$barang = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $conn->query("SELECT * from pemasok");
$pemasok = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

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
                                min="1" />
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm add-to-cart">
                                Tambah
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4 class="mt-5">Keranjang Permintaan</h4>
        <div>
            <select name="pemasok" class="form-control my-2 " style="width: 200px;" id="">
                <option>Pilih Pemasok</option>
                <?php foreach ($pemasok as $item) : ?>
                    <option value="<?= $item['KodePemasok']; ?>"><?= $item['NamaPemasok']; ?></option>
                <?php endforeach ?>
            </select>
        </div>
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

            let pemasok = '';

            const selectElement = document.querySelector('select[name="pemasok"]');

            if (selectElement.value == 'Pilih Pemasok') {
                alert('Ops, pilih pemasok dulu')
            } else {
                let transaksi = {
                    TanggalOrder: new Date().toISOString().split('T')[0],
                    NomorPO: Math.floor(Math.random() * 100000000000),
                    TanggalPo: new Date().toISOString().split('T')[0],
                    TotalHarga: total,
                    KodePemasok: selectElement.value,
                    type: "beli"
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

            }
        });
    </script>
</body>

</html>