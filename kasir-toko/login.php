<?php
session_start();
require 'koneksi.php';

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = md5($_POST['password']); // Enkripsi password inputan dengan MD5

    // Cek di database
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' AND password='$pass'");
    
    if (mysqli_num_rows($cek) > 0) {
        // Jika benar, buat session & masuk ke index
        $_SESSION['login'] = true;
        $_SESSION['username'] = $user;
        echo "<script>alert('Login Berhasil!'); window.location.href='index.php';</script>";
    } else {
        // Jika salah
        echo "<script>alert('Username atau Password Salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kasir Swalayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #343a40; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-login { width: 400px; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.5); }
        .card-header { background: #0d6efd; color: white; text-align: center; padding: 20px; }
    </style>
</head>
<body>

<div class="card card-login bg-white">
    <div class="card-header">
        <h4>KASIR SWALAYAN</h4>
        <small>Silakan Login Terlebih Dahulu</small>
    </div>
    <div class="card-body p-4">
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="admin" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="admin" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 py-2">MASUK</button>
        </form>
    </div>
    <div class="card-footer text-center bg-light text-muted py-3">
        &copy; 2026 Kasir Swalayan
    </div>
</div>

</body>
</html>