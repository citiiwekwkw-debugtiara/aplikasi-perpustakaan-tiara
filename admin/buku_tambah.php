<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun = $_POST['tahun'];
    $stok = $_POST['stok'];
    $kategori = $_POST['kategori'];

    // Gambar dari URL (tidak ada upload file karena Vercel tidak mendukung penyimpanan lokal)
    $gambar = !empty($_POST['gambar_url']) ? $_POST['gambar_url'] : 'default.jpg';

    $query = "INSERT INTO buku (judul, penulis, penerbit, tahun, stok, kategori, gambar) VALUES ('$judul', '$penulis', '$penerbit', '$tahun', '$stok', '$kategori', '$gambar')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: buku.php");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Buku - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h2 style="margin-bottom: 1.5rem;">Tambah Buku Baru</h2>
            
            <div class="form-card">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">Judul Buku</label>
                        <input type="text" name="judul" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Penulis</label>
                        <input type="text" name="penulis" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Penerbit</label>
                        <input type="text" name="penerbit" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tahun Terbit</label>
                        <input type="number" name="tahun" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="kategori" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL Gambar Sampul (Opsional)</label>
                        <input type="text" name="gambar_url" class="form-input" placeholder="https://contoh.com/gambar.jpg">
                        <small style="color: #94a3b8; font-size: 0.8rem;">Masukkan URL gambar dari internet. Kosongkan jika tidak ada gambar.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Buku</button>
                    <a href="buku.php" class="btn" style="background: #e2e8f0; color: #475569; margin-left: 0.5rem;">Batal</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
