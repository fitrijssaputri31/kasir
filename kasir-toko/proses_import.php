<?php
// proses_import.php
require 'koneksi.php';

if (isset($_POST['import'])) {
    $fileName = $_FILES['file_csv']['name'];
    $fileTmp  = $_FILES['file_csv']['tmp_name'];
    
    // Validasi format file harus .csv
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    if ($ext != 'csv') {
        echo "<script>alert('Format file harus .CSV (Comma Separated Values)!'); window.location.href='index.php';</script>";
        exit();
    }

    // Buka file CSV
    $handle = fopen($fileTmp, "r");
    
    // Lewati baris pertama (Header Judul Kolom)
    fgetcsv($handle);

    $berhasil = 0;
    
    // Looping baca baris per baris
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Struktur CSV: Kolom 0=Kode, 1=Nama, 2=Kategori, 3=Modal, 4=Jual, 5=Stok
        $kode     = $data[0];
        $nama     = $data[1];
        $kategori = $data[2];
        $beli     = $data[3];
        $jual     = $data[4];
        $stok     = $data[5];

        // Cek apakah kode barang sudah ada?
        $cek = mysqli_query($conn, "SELECT kode_barang FROM barang WHERE kode_barang = '$kode'");
        
        if (mysqli_num_rows($cek) == 0) {
            // Jika belum ada, INSERT baru
            $query = "INSERT INTO barang (kode_barang, nama_barang, kategori, harga_beli, harga_jual, stok) 
                      VALUES ('$kode', '$nama', '$kategori', '$beli', '$jual', '$stok')";
            mysqli_query($conn, $query);
            $berhasil++;
        }
    }
    
    fclose($handle);
    echo "<script>alert('Berhasil mengimport $berhasil data barang!'); window.location.href='index.php';</script>";
}
?>