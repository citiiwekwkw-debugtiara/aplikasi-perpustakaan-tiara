<?php
session_start();
require_once '../config/database.php';

$id = $_GET['id'];
$query = "SELECT * FROM buku WHERE id_buku = '$id'";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun = $_POST['tahun'];
    $stok = $_POST['stok'];
    $kategori = $_POST['kategori'];

    // Handle Image Update
    $gambar = $data['gambar'];
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/";
        $file_extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                // Delete old image if it exists and is not default
                if ($data['gambar'] != 'default.jpg' && file_exists($target_dir . $data['gambar'])) {
                    unlink($target_dir . $data['gambar']);
                }
                $gambar = $new_filename;
            }
        }
    }

    $query_update = "UPDATE buku SET judul='$judul', penulis='$penulis', penerbit='$penerbit', tahun='$tahun', stok='$stok', kategori='$kategori', gambar='$gambar' WHERE id_buku='$id'";
    
    if (mysqli_query($koneksi, $query_update)) {
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
    <title>Edit Buku - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-container { display: flex; }
        .sidebar { width: 250px; background: var(--white); min-height: 100vh; padding: 2rem; border-right: 1px solid #e2e8f0; }
        .main-content { flex: 1; padding: 2rem; }
        .nav-link { display: block; padding: 0.75rem 1rem; color: var(--text-dark); text-decoration: none; margin-bottom: 0.5rem; border-radius: 0.5rem; }
        .nav-link:hover, .nav-link.active { background: var(--bg-light); color: var(--primary); }
        .nav-link.active { font-weight: 600; background: #e0e7ff; }
        
        .form-card { background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow); max-width: 600px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h2 style="margin-bottom: 1.5rem;">Edit Buku</h2>
            
            <div class="form-card">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">Judul Buku</label>
                        <input type="text" name="judul" class="form-input" value="<?php echo $data['judul']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Penulis</label>
                        <input type="text" name="penulis" class="form-input" value="<?php echo $data['penulis']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Penerbit</label>
                        <input type="text" name="penerbit" class="form-input" value="<?php echo $data['penerbit']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tahun Terbit</label>
                        <input type="number" name="tahun" class="form-input" value="<?php echo $data['tahun']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="kategori" class="form-input" value="<?php echo $data['kategori']; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-input" value="<?php echo $data['stok']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gambar Sampul (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="file" name="gambar" class="form-input">
                        <?php if($data['gambar'] != 'default.jpg'): ?>
                            <img src="../assets/img/<?php echo $data['gambar']; ?>" width="100" style="margin-top: 10px; border-radius: 5px;">
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Buku</button>
                    <a href="buku.php" class="btn" style="background: #e2e8f0; color: #475569; margin-left: 0.5rem;">Batal</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
