<?php
// export_laporan.php
require 'koneksi.php';

// Ambil tanggal dari URL (dikirim dari halaman laporan)
$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// HEADER AGAR BROWSER MENDOWNLOAD SEBAGAI EXCEL
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keuangan_$tgl_awal-sd-$tgl_akhir.xls");
?>

<h3>Laporan Keuangan Toko</h3>
<p>Periode: <?= $tgl_awal; ?> s/d <?= $tgl_akhir; ?></p>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Terjual (Qty)</th>
            <th>Modal Satuan</th>
            <th>Harga Jual</th>
            <th>Laba/Item</th>
            <th>Total Laba</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = mysqli_query($conn, "SELECT transaksi.tanggal, barang.nama_barang, transaksi_detail.qty, barang.harga_beli, barang.harga_jual 
                                    FROM transaksi_detail 
                                    JOIN barang ON transaksi_detail.id_barang = barang.id
                                    JOIN transaksi ON transaksi_detail.id_transaksi = transaksi.id
                                    WHERE DATE(transaksi.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                                    ORDER BY transaksi.tanggal DESC");

        while ($d = mysqli_fetch_assoc($query)) {
            $laba_satuan = $d['harga_jual'] - $d['harga_beli'];
            $total_laba  = $laba_satuan * $d['qty'];
        ?>
        <tr>
            <td><?= $d['tanggal']; ?></td>
            <td><?= $d['nama_barang']; ?></td>
            <td><?= $d['qty']; ?></td>
            <td><?= $d['harga_beli']; ?></td>
            <td><?= $d['harga_jual']; ?></td>
            <td><?= $laba_satuan; ?></td>
            <td><?= $total_laba; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>