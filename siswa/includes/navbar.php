<nav class="navbar">
    <div style="display: flex; align-items: center;">
        <h3 style="color: var(--primary); margin-right: 2rem;">Perpustakaan Digital</h3>
        <div class="nav-items-left">
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Beranda</a>
            <a href="buku.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'buku.php' ? 'active' : ''; ?>">Daftar Buku</a>
            <a href="riwayat.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'riwayat.php' ? 'active' : ''; ?>">Riwayat Peminjaman</a>
        </div>
    </div>
    <div class="nav-items-right" style="display: flex; align-items: center;">
        <span style="margin-right: 1rem; color: #64748b;">Halo, <?php echo $_SESSION['nama']; ?></span>
        <a href="../auth/logout.php" class="btn btn-primary" style="color: white; padding: 0.5rem 1rem;">Logout</a>
    </div>
</nav>

<style>
    .navbar { background: var(--white); padding: 1rem 2rem; box-shadow: var(--shadow); display: flex; justify-content: space-between; align-items: center; }
    .nav-items-left a { margin-right: 1.5rem; text-decoration: none; color: var(--text-dark); font-weight: 500; }
    .nav-items-left a:hover, .nav-items-left a.active { color: var(--primary); }
    .nav-items-left a.active { font-weight: 600; }
</style>
