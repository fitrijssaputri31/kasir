<?php
require 'koneksi.php';

// Ambil ID transaksi dari URL
$id_transaksi = isset($_GET['id']) ? $_GET['id'] : '';

// 1. Ambil Data Toko (untuk Header Struk)
$query_toko = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
$toko = mysqli_fetch_assoc($query_toko);

// 2. Ambil Data Transaksi Utama
$query_trx = mysqli_query($conn, "SELECT * FROM transaksi WHERE id = '$id_transaksi'");
$trx = mysqli_fetch_assoc($query_trx);

// Jika transaksi tidak ditemukan, kembalikan ke kasir
if (!$trx) {
    header("Location: kasir.php");
    exit();
}

// 3. Ambil Detail Barang yang dibeli
$query_detail = mysqli_query($conn, "SELECT transaksi_detail.*, barang.nama_barang 
                                     FROM transaksi_detail 
                                     JOIN barang ON transaksi_detail.id_barang = barang.id 
                                     WHERE id_transaksi = '$id_transaksi'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja - <?= $toko['nama_toko']; ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #eee;
        }
        .struk-container {
            width: 300px; /* Standar kertas thermal 58mm/80mm */
            background: #fff;
            padding: 15px;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header, .footer { text-align: center; margin-bottom: 10px; }
        .header h3 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 12px; }
        
        .garis { border-top: 1px dashed #000; margin: 10px 0; }
        
        .item { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 13px; }
        .item-name { width: 100%; margin-bottom: 3px; font-weight: bold; }
        .item-row { display: flex; justify-content: space-between; }
        
        .total-row { font-weight: bold; font-size: 1.1em; margin-top: 5px; }
        .metode-row { font-size: 12px; margin-top: 5px; }

        .footer { margin-top: 20px; font-size: 11px; }

        /* Tombol tidak ikut ter-print */
        @media print {
            body { background: #fff; }
            .struk-container { box-shadow: none; margin: 0; width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="struk-container">
    <div class="header">
        <h3><?= $toko['nama_toko']; ?></h3>
        <p><?= $toko['alamat']; ?><br>Telp: <?= $toko['no_telp']; ?></p>
    </div>

    <div class="garis"></div>

    <div style="font-size: 12px; margin-bottom: 10px;">
        No: <?= $trx['no_struk']; ?><br>
        Tgl: <?= date('d/m/Y H:i', strtotime($trx['tanggal'])); ?>
    </div>

    <div class="content">
        <?php while ($row = mysqli_fetch_assoc($query_detail)) { ?>
        <div style="margin-bottom: 8px;">
            <div class="item-name"><?= $row['nama_barang']; ?></div>
            <div class="item-row">
                <span><?= $row['qty']; ?> x <?= number_format($row['subtotal'] / $row['qty']); ?></span>
                <span><?= number_format($row['subtotal']); ?></span>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="garis"></div>

    <div class="item total-row">
        <span>TOTAL</span>
        <span>Rp <?= number_format($trx['total_bayar']); ?></span>
    </div>
    
    <div class="item metode-row">
        <span>PEMBAYARAN</span>
        <span><?= $trx['metode']; ?></span>
    </div>

    <div class="garis"></div>
    
    <div class="footer">
        <p><?= $toko['footer_struk']; ?></p>
        <small>Terima Kasih</small>
    </div>

    <button class="no-print" onclick="window.print()" style="width:100%; padding: 10px; margin-top: 10px; cursor: pointer; background: #333; color: #fff; border: none; font-weight: bold;">
        CETAK STRUK
    </button>
    <button class="no-print" onclick="window.location.href='kasir.php'" style="width:100%; padding: 10px; margin-top: 5px; cursor: pointer; background: #ddd; border: none;">
        TRANSAKSI BARU
    </button>
</div>

</body>
</html>