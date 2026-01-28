<?php
session_start();
require 'koneksi.php';

// CEK LOGIN
if (empty($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['add'])) {
    $id = $_GET['add'];
    $cek = mysqli_query($conn, "SELECT stok FROM barang WHERE id='$id'");
    $data = mysqli_fetch_assoc($cek);
    $qty_now = isset($_SESSION['keranjang'][$id]) ? $_SESSION['keranjang'][$id] : 0;

    if (($qty_now + 1) > $data['stok']) {
        echo "<script>alert('Stok tidak cukup!'); window.location.href='kasir.php';</script>";
        exit();
    } else {
        $_SESSION['keranjang'][$id] = $qty_now + 1;
    }
    header("Location: kasir.php");
    exit();
}

if (isset($_GET['hapus'])) {
    unset($_SESSION['keranjang'][$_GET['hapus']]);
    header("Location: kasir.php");
    exit();
}

if (isset($_GET['reset'])) {
    unset($_SESSION['keranjang']);
    header("Location: kasir.php");
    exit();
}

if (isset($_POST['bayar'])) {
    $metode = $_POST['metode'];
    $total  = $_POST['total_hidden'];
    if ($total > 0 && !empty($_SESSION['keranjang'])) {
        $no_struk = "INV-" . date("YmdHis");
        $tgl = date("Y-m-d H:i:s");
        mysqli_query($conn, "INSERT INTO transaksi (no_struk, tanggal, total_bayar, metode) VALUES ('$no_struk', '$tgl', '$total', '$metode')");
        $id_trx = mysqli_insert_id($conn);

        foreach ($_SESSION['keranjang'] as $id_barang => $qty) {
            $ambil = mysqli_query($conn, "SELECT harga_jual FROM barang WHERE id='$id_barang'");
            $b = mysqli_fetch_assoc($ambil);
            $subtotal = $b['harga_jual'] * $qty;
            mysqli_query($conn, "INSERT INTO transaksi_detail (id_transaksi, id_barang, qty, subtotal) VALUES ('$id_trx', '$id_barang', '$qty', '$subtotal')");
            mysqli_query($conn, "UPDATE barang SET stok = stok - $qty WHERE id='$id_barang'");
        }
        unset($_SESSION['keranjang']);
        echo "<script>alert('Sukses!'); window.location.href='struk.php?id=$id_trx';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mode Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="kasir-mode">

<nav class="navbar navbar-dark bg-primary px-4">
    <a class="navbar-brand" href="index.php"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
    <span class="text-white">Kasir: <b>Admin</b></span>
</nav>

<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-md-8 scroll-area">
            <form action="" method="GET" class="mb-3">
                <div class="input-group input-group-lg">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="cari" class="form-control" placeholder="Scan Barcode / Cari..." autofocus>
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>

            <div class="list-group">
                <?php
                $where = "";
                if (isset($_GET['cari'])) {
                    $cari = $_GET['cari'];
                    $cek = mysqli_query($conn, "SELECT * FROM barang WHERE kode_barang = '$cari'");
                    if (mysqli_num_rows($cek) == 1) {
                        $h = mysqli_fetch_assoc($cek);
                        echo "<script>window.location.href='kasir.php?add=$h[id]';</script>";
                        exit();
                    } else {
                        $where = "WHERE nama_barang LIKE '%$cari%' OR kode_barang LIKE '%$cari%'";
                    }
                }
                $query = mysqli_query($conn, "SELECT * FROM barang $where ORDER BY kategori ASC, nama_barang ASC");
                $kat_now = "";
                while ($brg = mysqli_fetch_assoc($query)) {
                    if ($brg['kategori'] != $kat_now) {
                        echo "<div class='category-header'><i class='bi bi-tag-fill me-2'></i>$brg[kategori]</div>";
                        $kat_now = $brg['kategori'];
                    }
                ?>
                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $brg['stok']<=0?'stok-habis':''; ?>" onclick="window.location.href='kasir.php?add=<?= $brg['id']; ?>'">
                    <div><h6 class="mb-0 fw-bold"><?= $brg['nama_barang']; ?></h6><small>Stok: <?= $brg['stok']; ?></small></div>
                    <span class="badge bg-primary">Rp <?= number_format($brg['harga_jual']); ?></span>
                </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm" style="height: 90vh !important;">
                <div class="card-header bg-white fw-bold d-flex justify-content-between">
                    <span>Keranjang</span><a href="kasir.php?reset=true" class="btn btn-sm btn-danger">Reset</a>
                </div>
                <div class="card-body cart-area bg-light">
                    <table class="table table-sm table-borderless">
                        <?php
                        $grand_total = 0;
                        if (!empty($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $id => $qty) {
                                $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id='$id'"));
                                $sub = $d['harga_jual'] * $qty;
                                $grand_total += $sub;
                                echo "<tr><td>$d[nama_barang]</td><td class='text-center'>$qty</td><td class='text-end'>".number_format($sub)."</td><td><a href='kasir.php?hapus=$id' class='text-danger'>x</a></td></tr>";
                            }
                        }
                        ?>
                    </table>
                </div>
                <div class="card-footer bg-white pb-3">
                    <div class="d-flex justify-content-between mb-3"><h4>Total:</h4><h3 class="text-primary fw-bold">Rp <?= number_format($grand_total); ?></h3></div>
                    <button class="btn btn-success w-100 btn-lg" data-bs-toggle="modal" data-bs-target="#modalBayar">BAYAR</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h2 class="text-primary mb-4">Rp <?= number_format($grand_total); ?></h2>
                <div class="row">
                    <div class="col-6"><form method="POST"><input type="hidden" name="total_hidden" value="<?= $grand_total; ?>"><input type="hidden" name="metode" value="CASH"><button type="submit" name="bayar" class="btn btn-outline-primary w-100 py-4">TUNAI</button></form></div>
                    <div class="col-6"><form method="POST"><input type="hidden" name="total_hidden" value="<?= $grand_total; ?>"><input type="hidden" name="metode" value="QRIS"><button type="submit" name="bayar" class="btn btn-outline-success w-100 py-4">QRIS</button></form></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>