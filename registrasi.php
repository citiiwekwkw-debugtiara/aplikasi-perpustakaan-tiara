<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);

    $query = "INSERT INTO anggota (nama, username, password, kelas, kontak) 
              VALUES ('$nama', '$username', '$password', '$kelas', '$kontak')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header("Location: index.php");
        exit;
    } else {
        $error = "Username sudah digunakan atau terjadi kesalahan: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Siswa - Perpustakaan Digital</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .login-wrapper { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f8f9fa; }
        .reg-card { background: white; padding: 2.5rem; border-radius: 1rem; box-shadow: var(--shadow); width: 100%; max-width: 500px; }
        .form-title { text-align: center; margin-bottom: 2rem; }
        .alert-error { background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #fecaca; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="reg-card">
            <div class="form-title">
                <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Daftar Akun Siswa</h1>
                <p style="color: var(--text-light);">Lengkapi data diri Anda untuk meminjam buku</p>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-input" placeholder="Nama Lengkap" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" placeholder="Pilih username" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Masukkan password" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas" class="form-input" placeholder="Contoh: XII RPL 1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kontak (HP)</label>
                    <input type="text" name="kontak" class="form-input" placeholder="Nomor Telepon" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Daftar Sekarang</button>
                
                <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-light);">
                    Sudah punya akun? <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">Masuk di sini</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
