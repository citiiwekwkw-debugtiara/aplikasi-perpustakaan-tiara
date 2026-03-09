<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}

// Fetch latest books
$query_books = "SELECT * FROM buku ORDER BY id_buku DESC LIMIT 6";
$books = mysqli_query($koneksi, $query_books);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .hero { background: linear-gradient(135deg, var(--primary) 0%, #f5d2d2 100%); color: white; padding: 3rem 2rem; border-radius: 1rem; margin-bottom: 2rem; text-align: center; box-shadow: var(--shadow); }
        .book-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 2rem; }
        .book-card { background: white; border-radius: 0.5rem; overflow: hidden; box-shadow: var(--shadow); transition: transform 0.2s; border: 1px solid #f8f7ba; }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
        .book-img { height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .book-img img { width: 100%; height: 100%; object-fit: cover; }
        .book-info { padding: 1rem; }
        .book-title { font-weight: 600; margin-bottom: 0.25rem; display: block; color: var(--text-dark); text-decoration: none; font-size: 1.1rem; }
        .book-author { font-size: 0.9rem; color: #f875aa; margin-bottom: 0.5rem; }
        .book-meta { display: flex; justify-content: space-between; font-size: 0.8rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #abde3c; }
        .stok-badge { padding: 0.2rem 0.5rem; background: #f8f7ba; color: #16a34a; border-radius: 1rem; font-weight: 600; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 2rem;">
        <div class="hero">
            <h1>Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h1>
            <p style="margin-top: 0.5rem; opacity: 0.9; font-size: 1.1rem;">Temukan buku favoritmu dan mulailah membaca.</p>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3>Koleksi Buku Terbaru</h3>
            <a href="buku.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">Lihat Semua &rarr;</a>
        </div>
        
        <div class="book-grid">
            <?php while($b = mysqli_fetch_assoc($books)): ?>
            <div class="book-card">
                <div class="book-img">
                    <?php 
                    $img = $b['gambar'];
                    if($img && $img !== 'default.jpg' && (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0)):
                    ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($b['judul']); ?>">
                    <?php else: ?>
                        <div style="font-size: 3rem;">📖</div>
                    <?php endif; ?>
                </div>
                <div class="book-info">
                    <div class="book-title"><?php echo $b['judul']; ?></div>
                    <div class="book-author"><?php echo $b['penulis']; ?></div>
                    <div class="book-meta">
                        <span><?php echo $b['tahun']; ?></span>
                        <span class="stok-badge">Stok: <?php echo $b['stok']; ?></span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
