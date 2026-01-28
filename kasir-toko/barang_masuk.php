<?php
session_start();
require 'koneksi.php';

// CEK LOGIN
if (empty($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$pesan_sukses = "";
$pesan_gagal = "";

if (isset($_POST['simpan_masuk'])) {
    $tanggal   = $_POST['tanggal'];
    $id_barang = $_POST['id_barang'];
    $qty       = $_POST['qty'];
    $keterangan = $_POST['keterangan'];

    if ($id_barang == "" || $qty <= 0) {
        $pesan_gagal = "Harap pilih barang dan masukkan jumlah yang benar.";
    } else {
        $simpan_riwayat = mysqli_query($conn, "INSERT INTO barang_masuk (id_barang, tanggal, qty, keterangan) VALUES ('$id_barang', '$tanggal', '$qty', '$keterangan')");
        $update_stok = mysqli_query($conn, "UPDATE barang SET stok = stok + '$qty' WHERE id = '$id_barang'");

        if ($simpan_riwayat && $update_stok) $pesan_sukses = "Stok berhasil ditambahkan!";
        else $pesan_gagal = "Gagal menyimpan: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="d-flex">
    <div class="sidebar p-3" style="width: 250px;">
        <h4 class="text-center mb-4">Kasir Swalayan</h4>
        <a href="index.php"><i class="bi bi-box-seam me-2"></i> Stok Barang</a>
        <a href="barang_masuk.php" class="active"><i class="bi bi-arrow-down-square me-2"></i> Barang Masuk</a>
        <a href="barang_keluar.php"><i class="bi bi-arrow-up-square me-2"></i> Barang Keluar</a>
        <a href="laporan.php"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Keuangan</a>
        <a href="pengaturan.php"><i class="bi bi-gear me-2"></i> Pengaturan Toko</a>
        
        <hr>
        <a href="kasir.php" class="bg-warning text-dark fw-bold"><i class="bi bi-cart4 me-2"></i> Mode Kasir</a>

        <hr>
        <a href="logout.php" class="bg-danger text-white fw-bold" onclick="return confirm('Yakin ingin keluar?')">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
    </div>

    <div class="flex-grow-1 p-4">
        <h2>Input Barang Masuk</h2>
        <?php if ($pesan_sukses) echo "<div class='alert alert-success'>$pesan_sukses</div>"; ?>
        <?php if ($pesan_gagal) echo "<div class='alert alert-danger'>$pesan_gagal</div>"; ?>
        
        <div class="card mt-3" style="max-width: 600px;">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3"><label>Tanggal Masuk</label><input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d'); ?>" required></div>
                    <div class="mb-3"><label>Pilih Barang</label>
                        <select class="form-select" name="id_barang" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php
                            $ambil_barang = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
                            while ($barang = mysqli_fetch_assoc($ambil_barang)) {
                                echo "<option value='$barang[id]'>$barang[nama_barang] (Stok: $barang[stok])</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3"><label>Jumlah (Qty)</label><input type="number" class="form-control" name="qty" required></div>
                    <div class="mb-3"><label>Keterangan</label><textarea class="form-control" name="keterangan"></textarea></div>
                    <button type="submit" name="simpan_masuk" class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">Riwayat Terakhir</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Tanggal</th><th>Barang</th><th>Qty</th><th>Ket</th></tr></thead>
                    <tbody>
                        <?php
                        $history = mysqli_query($conn, "SELECT barang_masuk.*, barang.nama_barang FROM barang_masuk JOIN barang ON barang_masuk.id_barang = barang.id ORDER BY barang_masuk.id DESC LIMIT 5");
                        while($h = mysqli_fetch_assoc($history)){
                            echo "<tr><td>$h[tanggal]</td><td>$h[nama_barang]</td><td class='text-success'>+$h[qty]</td><td>$h[keterangan]</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>