<?php
session_start(); // Mulai Sesi
require 'koneksi.php';

// CEK APAKAH SUDAH LOGIN?
// Jika belum login, tendang ke halaman login
if (empty($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$pesan_sukses = "";
$pesan_gagal = "";

// --- 1. LOGIKA SIMPAN BARU ---
if (isset($_POST['simpan_barang'])) {
    $kode     = $_POST['kode_barang'];
    $nama     = $_POST['nama_barang'];
    $kategori = $_POST['kategori'];
    $beli     = $_POST['harga_beli'];
    $jual     = $_POST['harga_jual'];
    $stok     = $_POST['stok'];

    // Cek kode barang sudah ada atau belum
    $cek = mysqli_query($conn, "SELECT kode_barang FROM barang WHERE kode_barang = '$kode'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan_gagal = "Kode barang sudah ada!";
    } else {
        $simpan = mysqli_query($conn, "INSERT INTO barang (kode_barang, nama_barang, kategori, harga_beli, harga_jual, stok) VALUES ('$kode', '$nama', '$kategori', '$beli', '$jual', '$stok')");
        if ($simpan) {
            $pesan_sukses = "Data barang berhasil ditambahkan!";
        } else {
            $pesan_gagal = "Gagal menyimpan: " . mysqli_error($conn);
        }
    }
}

// --- 2. LOGIKA UPDATE / EDIT BARANG ---
if (isset($_POST['update_barang'])) {
    $id_edit  = $_POST['id_barang'];
    $nama     = $_POST['nama_barang'];
    $kategori = $_POST['kategori'];
    $beli     = $_POST['harga_beli'];
    $jual     = $_POST['harga_jual'];

    $update = mysqli_query($conn, "UPDATE barang SET nama_barang='$nama', kategori='$kategori', harga_beli='$beli', harga_jual='$jual' WHERE id='$id_edit'");

    if ($update) {
        $pesan_sukses = "Data barang berhasil diperbarui!";
    } else {
        $pesan_gagal = "Gagal mengupdate: " . mysqli_error($conn);
    }
}

// --- 3. LOGIKA HAPUS ---
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $hapus = mysqli_query($conn, "DELETE FROM barang WHERE id = '$id_hapus'");
    if($hapus){
        echo "<script>window.location.href='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Stok - Swalayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="d-flex">
    <div class="sidebar p-3" style="width: 250px;">
        <h4 class="text-center mb-4">Kasir Swalayan</h4>
        <a href="index.php" class="active"><i class="bi bi-box-seam me-2"></i> Stok Barang</a>
        <a href="barang_masuk.php"><i class="bi bi-arrow-down-square me-2"></i> Barang Masuk</a>
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
        <h2>Daftar Stok Barang</h2>

        <?php if ($pesan_sukses) { ?>
            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $pesan_sukses; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>
        
        <?php if ($pesan_gagal) { ?>
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $pesan_gagal; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="bi bi-plus-lg"></i> Tambah Manual
                        </button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalImport">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Import Excel (CSV)
                        </button>
                    </div>
                    <input type="text" class="form-control w-25" placeholder="Cari barang...">
                </div>
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM barang ORDER BY id DESC");
                        
                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='6' class='text-center text-muted'>Belum ada data barang.</td></tr>";
                        }

                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?= $row['kode_barang']; ?></td>
                            <td><?= $row['nama_barang']; ?></td>
                            <td><?= $row['kategori']; ?></td>
                            <td>Rp <?= number_format($row['harga_jual']); ?></td>
                            <td>
                                <?php if($row['stok'] <= 5) { ?>
                                    <span class="badge bg-danger"><?= $row['stok']; ?></span>
                                <?php } else { ?>
                                    <span class="badge bg-success"><?= $row['stok']; ?></span>
                                <?php } ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="index.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus barang ini?')" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Barang: <?= $row['nama_barang']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" method="POST">
                                            <input type="hidden" name="id_barang" value="<?= $row['id']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Kode Barang</label>
                                                <input type="text" class="form-control" value="<?= $row['kode_barang']; ?>" readonly>
                                                <small class="text-muted">Kode barang tidak bisa diubah.</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Barang</label>
                                                <input type="text" class="form-control" name="nama_barang" value="<?= $row['nama_barang']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kategori</label>
                                                <select class="form-select" name="kategori">
                                                    <option value="Makanan" <?= ($row['kategori'] == 'Makanan') ? 'selected' : ''; ?>>Makanan</option>
                                                    <option value="Minuman" <?= ($row['kategori'] == 'Minuman') ? 'selected' : ''; ?>>Minuman</option>
                                                    <option value="Sembako" <?= ($row['kategori'] == 'Sembako') ? 'selected' : ''; ?>>Sembako</option>
                                                    <option value="Alat Tulis" <?= ($row['kategori'] == 'Alat Tulis') ? 'selected' : ''; ?>>Alat Tulis</option>
                                                    <option value="Lainnya" <?= ($row['kategori'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-success">Harga Beli</label>
                                                    <input type="number" class="form-control" name="harga_beli" value="<?= $row['harga_beli']; ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-primary">Harga Jual</label>
                                                    <input type="number" class="form-control" name="harga_jual" value="<?= $row['harga_jual']; ?>">
                                                </div>
                                            </div>
                                            <button type="submit" name="update_barang" class="btn btn-warning w-100">Update Data</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" class="form-control" name="kode_barang" placeholder="Scan / Ketik Kode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang" placeholder="Nama Produk" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori">
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Sembako">Sembako</option>
                            <option value="Alat Tulis">Alat Tulis</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-success">Harga Beli (Modal)</label>
                            <input type="number" class="form-control" name="harga_beli" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Harga Jual</label>
                            <input type="number" class="form-control" name="harga_jual" placeholder="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" class="form-control" name="stok" placeholder="0" required>
                    </div>
                    <button type="submit" name="simpan_barang" class="btn btn-primary w-100">Simpan Barang</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data Barang (CSV)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="proses_import.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Pilih File CSV</label>
                        <input type="file" class="form-control" name="file_csv" required accept=".csv">
                        <small class="text-muted">
                            Format kolom di Excel harus urut: <br>
                            <b>Kode | Nama | Kategori | Modal | Jual | Stok</b>
                        </small>
                    </div>
                    <button type="submit" name="import" class="btn btn-success w-100">Upload & Import</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>