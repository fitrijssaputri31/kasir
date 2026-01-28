<?php
session_start();
require 'koneksi.php';

// CEK LOGIN
if (empty($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// 1. HITUNG OMZET
$query_omzet = mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM transaksi WHERE DATE(tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'");
$data_omzet = mysqli_fetch_assoc($query_omzet);
$omzet = $data_omzet['total'] ?: 0;

// 2. HITUNG MODAL
$query_modal = mysqli_query($conn, "SELECT SUM(transaksi_detail.qty * barang.harga_beli) as total_modal FROM transaksi_detail JOIN barang ON transaksi_detail.id_barang = barang.id JOIN transaksi ON transaksi_detail.id_transaksi = transaksi.id WHERE DATE(transaksi.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'");
$data_modal = mysqli_fetch_assoc($query_modal);
$total_modal = $data_modal['total_modal'] ?: 0;

$laba_bersih = $omzet - $total_modal;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
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
        <a href="laporan.php" class="active"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Keuangan</a>
        <a href="pengaturan.php"><i class="bi bi-gear me-2"></i> Pengaturan Toko</a>
        
        <hr>
        <a href="kasir.php" class="bg-warning text-dark fw-bold"><i class="bi bi-cart4 me-2"></i> Mode Kasir</a>

        <hr>
        <a href="logout.php" class="bg-danger text-white fw-bold" onclick="return confirm('Yakin ingin keluar?')">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
    </div>

    <div class="flex-grow-1 p-4">
        <h2>Laporan Laba & Rugi</h2>
        <p class="text-muted">Periode: <b><?= date('d M Y', strtotime($tgl_awal)); ?></b> s/d <b><?= date('d M Y', strtotime($tgl_akhir)); ?></b></p>
        
        <div class="card mb-4">
            <div class="card-body">
                <form action="" method="GET" class="row g-3 align-items-end">
                    <div class="col-auto"><label>Dari</label><input type="date" class="form-control" name="tgl_awal" value="<?= $tgl_awal; ?>"></div>
                    <div class="col-auto"><label>Sampai</label><input type="date" class="form-control" name="tgl_akhir" value="<?= $tgl_akhir; ?>"></div>
                    <div class="col-auto"><button type="submit" class="btn btn-primary">Tampilkan</button></div>
                    <div class="col-auto"><a href="export_laporan.php?tgl_awal=<?= $tgl_awal; ?>&tgl_akhir=<?= $tgl_akhir; ?>" target="_blank" class="btn btn-success">Export Excel</a></div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4"><div class="card bg-primary text-white p-3"><h3>Rp <?= number_format($omzet); ?></h3><small>Total Omzet</small></div></div>
            <div class="col-md-4"><div class="card bg-danger text-white p-3"><h3>Rp <?= number_format($total_modal); ?></h3><small>Total Modal</small></div></div>
            <div class="col-md-4"><div class="card bg-success text-white p-3"><h3>Rp <?= number_format($laba_bersih); ?></h3><small>Keuntungan</small></div></div>
        </div>

        <div class="card">
            <div class="card-header">Rincian Penjualan</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead><tr><th>Tanggal</th><th>Barang</th><th>Qty</th><th>Modal</th><th>Jual</th><th>Laba/Item</th><th>Total Laba</th></tr></thead>
                    <tbody>
                        <?php
                        $query_detail = mysqli_query($conn, "SELECT transaksi.tanggal, barang.nama_barang, transaksi_detail.qty, barang.harga_beli, barang.harga_jual FROM transaksi_detail JOIN barang ON transaksi_detail.id_barang = barang.id JOIN transaksi ON transaksi_detail.id_transaksi = transaksi.id WHERE DATE(transaksi.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir' ORDER BY transaksi.tanggal DESC");
                        $total_laba_final = 0; 
                        while ($d = mysqli_fetch_assoc($query_detail)) {
                            $laba = ($d['harga_jual'] - $d['harga_beli']) * $d['qty'];
                            $total_laba_final += $laba;
                            echo "<tr><td>".date('d/m/Y', strtotime($d['tanggal']))."</td><td>$d[nama_barang]</td><td>$d[qty]</td><td>".number_format($d['harga_beli'])."</td><td>".number_format($d['harga_jual'])."</td><td>".number_format($d['harga_jual']-$d['harga_beli'])."</td><td class='text-success fw-bold'>+".number_format($laba)."</td></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr><td colspan="6" class="text-end fw-bold">TOTAL KESELURUHAN LABA</td><td class="text-warning fw-bold">Rp <?= number_format($total_laba_final); ?></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>