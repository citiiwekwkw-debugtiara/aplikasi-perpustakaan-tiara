<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}

$search = "";
if (isset($_GET['cari'])) {
    $search = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $query = "SELECT * FROM buku WHERE judul LIKE '%$search%' OR penulis LIKE '%$search%' OR kategori LIKE '%$search%' ORDER BY id_buku DESC";
} else {
    $query = "SELECT * FROM buku ORDER BY id_buku DESC";
}

$books = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Buku - Perpustakaan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .search-bar { margin-bottom: 2rem; display: flex; gap: 1rem; }
        .search-input { flex: 0.5; padding: 0.75rem; border: 1px solid #edfff0; border-radius: 0.5rem; font-size: 1rem; }
        .book-list { display: flex; flex-direction: column; gap: 1rem; }
        .book-item { background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: var(--shadow); display: flex; align-items: center; gap: 1.5rem; }
        .book-cover { width: 80px; height: 120px; background: #f8f9fa; border-radius: 0.25rem; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .book-cover img { width: 100%; height: 100%; object-fit: cover; }
        .book-details { flex-grow: 1; }
        .book-details p { color: #f875aa; font-size: 0.9rem; }
        .book-status { text-align: right; }
        .available { color: #cdc1ff; font-weight: 600; }
        .unavailable { color: #dc2626; font-weight: 600; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 2rem;">
        <h2 style="margin-bottom: 1.5rem;">Cari Buku</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div style="background: #dcfce7; color: #16a34a; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #bbf7d0;">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div style="background: #ffccea; color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #fecaca;">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="GET" class="search-bar">
            <input type="text" name="cari" class="search-input" placeholder="Cari judul, penulis, atau kategori..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary"> Cari</button>
        </form>

        <div class="book-list">
            <?php if(mysqli_num_rows($books) > 0): ?>
                <?php while($b = mysqli_fetch_assoc($books)): ?>
                <div class="book-item">
                    <div class="book-cover">
                        <?php 
                        $img = $b['gambar'];
                        if($img && $img !== 'default.jpg' && (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0)):
                        ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($b['judul']); ?>">
                        <?php else: ?>
                            <div style="font-size: 2rem;">📖</div>
                        <?php endif; ?>
                    </div>
                    <div class="book-details">
                        <h4><?php echo $b['judul']; ?></h4>
                        <p>Penulis: <?php echo $b['penulis']; ?> | Penerbit: <?php echo $b['penerbit']; ?> (<?php echo $b['tahun']; ?>)</p>
                        <p style="margin-top: 0.25rem;">Kategori: <span style="background: #cdc1ff; color: var(--primary); padding: 0.1rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem;"><?php echo $b['kategori']; ?></span></p>
                    </div>
                    <div class="book-status">
                        <?php if($b['stok'] > 0): ?>
                            <div class="available">Tersedia</div>
                            <small class="text-light"><?php echo $b['stok']; ?> Copy</small>
                            <div style="margin-top: 0.5rem;">
                                <a href="pinjam_proses.php?id=<?php echo $b['id_buku']; ?>" 
                                   class="btn btn-primary btn-sm" 
                                   onclick="return confirm('Apakah Anda yakin ingin meminjam buku ini?')" disabled style="background: #aedefc;">
                                   Pinjam
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="unavailable">Habis</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #d44848ff;">
                    Buku tidak ditemukan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
