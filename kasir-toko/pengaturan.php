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

if (isset($_POST['simpan_pengaturan'])) {
    $nama   = $_POST['nama_toko'];
    $alamat = $_POST['alamat_toko'];
    $telp   = $_POST['no_telp'];
    $footer = $_POST['footer_struk'];
    $update = mysqli_query($conn, "UPDATE pengaturan SET nama_toko='$nama', alamat='$alamat', no_telp='$telp', footer_struk='$footer' WHERE id=1");
    if ($update) $pesan_sukses = "Pengaturan disimpan!";
    else $pesan_gagal = "Gagal menyimpan.";
}

$query = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1");
$data  = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Toko</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="d-flex">
    <div class="sidebar p-3" style="width: 250px;">
        <h4 class="text-center mb-4">Kasir Swalayan</h4>
        <a href="index.php"><i class="bi bi-box-seam me-2"></i> Stok Barang</a>
        <a href="barang_masuk.php"><i class="bi bi-arrow-down-square me-2"></i> Barang Masuk</a>
        <a href="barang_keluar.php"><i class="bi bi-arrow-up-square me-2"></i> Barang Keluar</a>
        <a href="laporan.php"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Keuangan</a>
        <a href="pengaturan.php" class="active"><i class="bi bi-gear me-2"></i> Pengaturan Toko</a>
        
        <hr>
        <a href="kasir.php" class="bg-warning text-dark fw-bold"><i class="bi bi-cart4 me-2"></i> Mode Kasir</a>

        <hr>
        <a href="logout.php" class="bg-danger text-white fw-bold" onclick="return confirm('Yakin ingin keluar?')">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
    </div>

    <div class="flex-grow-1 p-4">
        <h2>Pengaturan Toko</h2>
        <?php if ($pesan_sukses) echo "<div class='alert alert-success'>$pesan_sukses</div>"; ?>
        
        <div class="card mt-3" style="max-width: 700px;">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3"><label>Nama Toko</label><input type="text" class="form-control" name="nama_toko" value="<?= $data['nama_toko']; ?>" required></div>
                    <div class="mb-3"><label>Alamat Toko</label><textarea class="form-control" name="alamat_toko" rows="2" required><?= $data['alamat']; ?></textarea></div>
                    <div class="mb-3"><label>No Telp</label><input type="text" class="form-control" name="no_telp" value="<?= $data['no_telp']; ?>" required></div>
                    <div class="mb-3"><label>Footer Struk</label><input type="text" class="form-control" name="footer_struk" value="<?= $data['footer_struk']; ?>" required></div>
                    <button type="submit" name="simpan_pengaturan" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>