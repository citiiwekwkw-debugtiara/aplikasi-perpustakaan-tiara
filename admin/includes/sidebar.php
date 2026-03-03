<aside class="sidebar">
    <h3 style="margin-bottom: 2rem; color: var(--primary);">Perpustakaan</h3>
    <nav>
        <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a>
        <a href="buku.php" class="nav-link <?php echo strpos(basename($_SERVER['PHP_SELF']), 'buku') !== false ? 'active' : ''; ?>">Data Buku</a>
        <a href="anggota.php" class="nav-link <?php echo strpos(basename($_SERVER['PHP_SELF']), 'anggota') !== false ? 'active' : ''; ?>">Data Anggota</a>
        <a href="transaksi.php" class="nav-link <?php echo strpos(basename($_SERVER['PHP_SELF']), 'transaksi') !== false ? 'active' : ''; ?>">Transaksi</a>
        <br>
        <a href="../auth/logout.php" class="nav-link" style="color: #dc2626;">Logout</a>
    </nav>
</aside>
