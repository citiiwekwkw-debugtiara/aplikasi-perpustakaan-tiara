<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);

    $query = "INSERT INTO anggota (nama, username, password, kelas, kontak) 
              VALUES ('$nama', '$username', '$password', '$kelas', '$kontak')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: anggota.php");
        exit;
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Anggota - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h2 style="margin-bottom: 1.5rem;">Tambah Anggota Baru</h2>
            
            <?php if(isset($error)): ?>
                <div style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow); max-width: 600px;">
                <form action="" method="POST">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kelas</label>
                        <input type="text" name="kelas" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kontak (HP)</label>
                        <input type="text" name="kontak" class="form-input" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Simpan Anggota</button>
                    <a href="anggota.php" class="btn" style="background: #ccc; color: #333; margin-left: 0.5rem; text-decoration: none; padding: 0.5rem 1rem; border-radius: 0.3rem;">Batal</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
