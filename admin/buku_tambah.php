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

    // Handle Image Upload
    $gambar = "default.jpg"; // Default value
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/";
        $file_extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $new_filename;
            }
        }
    }

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
                        <label class="form-label">Gambar Sampul</label>
                        <input type="file" name="gambar" class="form-input">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Buku</button>
                    <a href="buku.php" class="btn" style="background: #e2e8f0; color: #475569; margin-left: 0.5rem;">Batal</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
