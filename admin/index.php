<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Stats
$count_books = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku"))['total'];
$count_members = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM anggota"))['total'];
$count_borrowed = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE status='dipinjam'"))['total'];

// Recent Transactions
$recent = mysqli_query($koneksi, "SELECT transaksi.*, anggota.nama, buku.judul 
                                  FROM transaksi 
                                  JOIN anggota ON transaksi.id_anggota = anggota.id_anggota 
                                  JOIN buku ON transaksi.id_buku = buku.id_buku 
                                  ORDER BY transaksi.id_transaksi DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-container { display: flex; }
        .main-content { flex: 1; padding: 2rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: var(--shadow); border-left: 4px solid var(--primary); }
        .stat-value { font-size: 2rem; font-weight: 700; color: var(--text-dark); }
        .stat-label { color: var(--text-light); font-size: 0.9rem; margin-top: 0.25rem; font-weight: 500; }
        .recent-list { list-style: none; }
        .recent-item { padding: 1rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .recent-item:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="color: var(--text-dark);">Dashboard Admin</h2>
                    <p style="color: var(--text-light);">Selamat datang, <?php echo $_SESSION['nama']; ?></p>
                </div>
                <div style="background: #fff7cd; color: var(--primary); padding: 0.5rem 1rem; border-radius: 2rem; font-size: 0.9rem; font-weight: 600;">
                    <?php echo date('d F Y'); ?>
                </div>
            </header>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $count_books; ?></div>
                    <div class="stat-label">Total Judul Buku</div>
                </div>
                <div class="stat-card" style="border-left-color: #fb9b8f;">
                    <div class="stat-value"><?php echo $count_members; ?></div>
                    <div class="stat-label">Total Anggota</div>
                </div>
                <div class="stat-card" style="border-left-color: #fdc3a1;">
                    <div class="stat-value"><?php echo $count_borrowed; ?></div>
                    <div class="stat-label">Buku Sedang Dipinjam</div>
                </div>
            </div>

            <div class="card" style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
                <h3 style="margin-bottom: 1rem;">Aktivitas Peminjaman Terbaru</h3>
                <?php if(mysqli_num_rows($recent) > 0): ?>
                    <ul class="recent-list">
                        <?php while($r = mysqli_fetch_assoc($recent)): ?>
                        <li class="recent-item">
                            <div>
                                <span style="font-weight: 600; color: var(--text-dark);"><?php echo $r['nama']; ?></span>
                                <span style="color: #64748b; font-size: 0.9rem;"> meminjam </span>
                                <span style="color: var(--primary); font-weight: 500;"><?php echo $r['judul']; ?></span>
                            </div>
                            <span style="font-size: 0.85rem; color: #64748b;"><?php echo $r['tanggal_pinjam']; ?></span>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                    <div style="margin-top: 1rem; text-align: center;">
                        <a href="transaksi.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">Lihat Semua Transaksi &rarr;</a>
                    </div>
                <?php else: ?>
                    <p style="color: var(--text-light);">Belum ada aktivitas transaksi.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
