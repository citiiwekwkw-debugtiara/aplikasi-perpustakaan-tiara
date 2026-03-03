<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$today = date('Y-m-d');

// Logic for Returning Book (Pengembalian) by admin
if (isset($_GET['kembali'])) {
    $id_transaksi = mysqli_real_escape_string($koneksi, $_GET['kembali']);
    $tgl_kembali_real = $today;

    // Get transaction data
    $q_data = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi'");
    $d_data = mysqli_fetch_assoc($q_data);
    $id_buku = $d_data['id_buku'];

    // Calculate fine
    $denda = 0;
    if ($tgl_kembali_real > $d_data['tanggal_kembali']) {
        $hari = (int)((strtotime($tgl_kembali_real) - strtotime($d_data['tanggal_kembali'])) / 86400);
        $denda = $hari * 1000; // Rp 1.000 per day
    }

    // Update Transaction
    $update = mysqli_query($koneksi, "UPDATE transaksi SET status='kembali', tanggal_dikembalikan='$tgl_kembali_real', denda='$denda' WHERE id_transaksi='$id_transaksi'");

    if ($update) {
        mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku='$id_buku'");
        header("Location: transaksi.php");
        exit;
    }
}

$query = "SELECT transaksi.*, buku.judul, anggota.nama 
          FROM transaksi 
          JOIN buku ON transaksi.id_buku = buku.id_buku 
          JOIN anggota ON transaksi.id_anggota = anggota.id_anggota 
          ORDER BY transaksi.id_transaksi DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Peminjaman - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 0.5rem; overflow: hidden; box-shadow: var(--shadow); }
        th, td { padding: 0.85rem 1rem; text-align: left; border-bottom: 1px solid #a5c89e; font-size: 0.9rem; }
        th { background: #d8e981; font-weight: 600; color: var(--text-dark); }
        tr:hover { background: #f8faf5; }
        tr:last-child td { border-bottom: none; }
        .badge { padding: 0.25rem 0.6rem; border-radius: 1rem; font-size: 0.78rem; font-weight: 600; display: inline-block; }
        .badge-dipinjam  { background: #fef3c7; color: #92400e; }
        .badge-kembali   { background: #dcfce7; color: #16a34a; }
        .badge-terlambat { background: #fee2e2; color: #dc2626; }
        .badge-perpanjang { background: #e0f2fe; color: #0369a1; font-size: 0.7rem; margin-left: 0.3rem; }
        .denda-val { font-weight: 700; color: #dc2626; }
        .denda-ok  { color: #16a34a; }
        .btn-kembali { background: #10b981; color: white; padding: 0.35rem 0.8rem; border-radius: 0.3rem; text-decoration: none; font-size: 0.82rem; white-space: nowrap; }
        .btn-add { display: inline-block; margin-bottom: 1.5rem; background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 0.3rem; text-decoration: none; }
        .tgl-overdue { color: #dc2626; font-weight: 600; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
                <h2>Transaksi Peminjaman</h2>
                <a href="transaksi_tambah.php" class="btn-add">+ Pinjamkan Buku</a>
            </header>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                        $is_overdue = ($row['status'] == 'dipinjam' && $today > $row['tanggal_kembali']);
                        $denda_tampil = 0;
                        if ($is_overdue) {
                            $hari = (int)((strtotime($today) - strtotime($row['tanggal_kembali'])) / 86400);
                            $denda_tampil = $hari * 1000;
                        }
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                        <td>
                            <span class="<?php echo $is_overdue ? 'tgl-overdue' : ''; ?>">
                                <?php echo date('d M Y', strtotime($row['tanggal_kembali'])); ?>
                            </span>
                            <?php if ($row['perpanjangan'] >= 1): ?>
                                <span class="badge badge-perpanjang">+Diperpanjang</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'dipinjam' && $is_overdue): ?>
                                <span class="badge badge-terlambat">⚠ Terlambat</span>
                            <?php elseif($row['status'] == 'dipinjam'): ?>
                                <span class="badge badge-dipinjam">Dipinjam</span>
                            <?php else: ?>
                                <span class="badge badge-kembali">Dikembalikan</span>
                                <div style="font-size:0.75rem; color:#64748b; margin-top:0.2rem;">
                                    <?php echo date('d M Y', strtotime($row['tanggal_dikembalikan'])); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'kembali' && $row['denda'] > 0): ?>
                                <span class="denda-val">Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?></span>
                            <?php elseif($row['status'] == 'kembali'): ?>
                                <span class="denda-ok">Rp 0</span>
                            <?php elseif($is_overdue): ?>
                                <span class="denda-val" title="Estimasi denda hari ini">~Rp <?php echo number_format($denda_tampil, 0, ',', '.'); ?></span>
                            <?php else: ?>
                                <span style="color:#94a3b8;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'dipinjam'): ?>
                                <a href="transaksi.php?kembali=<?php echo $row['id_transaksi']; ?>"
                                   class="btn-kembali"
                                   onclick="return confirm('Proses pengembalian buku ini?<?php echo $is_overdue ? " Terdapat denda keterlambatan." : ""; ?>')">
                                    ↩ Kembalikan
                                </a>
                            <?php else: ?>
                                <span style="color:#94a3b8; font-size:0.85rem;">✓ Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <p style="margin-top:1rem; font-size:0.8rem; color:#64748b;">
                ⏰ Denda: <strong>Rp 1.000/hari</strong> keterlambatan &nbsp;|&nbsp;
                📚 Maks pinjaman: <strong>3 buku</strong> per siswa &nbsp;|&nbsp;
                🔄 Perpanjangan: <strong>1x (+7 hari)</strong>
            </p>
        </main>
    </div>
</body>
</html>
