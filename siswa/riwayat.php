<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}

$id_anggota = $_SESSION['id_anggota'];
$today = date('Y-m-d');

// Count active loans
$q_aktif = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE id_anggota = '$id_anggota' AND status = 'dipinjam'");
$aktif = mysqli_fetch_assoc($q_aktif)['total'];

$query = "SELECT transaksi.*, buku.judul 
          FROM transaksi 
          JOIN buku ON transaksi.id_buku = buku.id_buku 
          WHERE transaksi.id_anggota = '$id_anggota' 
          ORDER BY transaksi.id_transaksi DESC";

$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Peminjaman</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 0.5rem; overflow: hidden; box-shadow: var(--shadow); }
        th, td { padding: 0.85rem 1rem; text-align: left; border-bottom: 1px solid #fd7979; }
        th { background: #ffcdc9; font-weight: 600; color: var(--text-light); }
        tr:last-child td { border-bottom: none; }
        .badge { padding: 0.25rem 0.6rem; border-radius: 1rem; font-size: 0.78rem; font-weight: 600; display: inline-block; }
        .badge-dipinjam  { background: #fef3c7; color: #92400e; }
        .badge-kembali   { background: #dcfce7; color: #16a34a; }
        .badge-terlambat { background: #fee2e2; color: #dc2626; }
        .badge-extended  { background: #e0f2fe; color: #0369a1; font-size: 0.7rem; margin-left: 0.25rem; }
        .denda-val { font-weight: 700; color: #dc2626; }
        .denda-ok  { font-weight: 600; color: #16a34a; }
        .btn-perpanjang { background: #6366f1; color: white; padding: 0.3rem 0.7rem; border-radius: 0.3rem; text-decoration: none; font-size: 0.8rem; margin-bottom: 0.3rem; display: inline-block; }
        .btn-kembalikan { background: #f59e0b; color: white; padding: 0.3rem 0.7rem; border-radius: 0.3rem; text-decoration: none; font-size: 0.8rem; display: inline-block; }
        .info-box { padding: 0.8rem 1.2rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem; }
        .info-limit { background: #fef3c7; border: 1px solid #fcd34d; color: #78350f; }
        .info-limit.full { background: #fee2e2; border: 1px solid #fca5a5; color: #7f1d1d; }
        .info-limit span { font-size: 1.1rem; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 2rem;">
        <h2 style="margin-bottom: 1rem;">Riwayat Peminjaman Saya</h2>

        <!-- Loan limit info bar -->
        <div class="info-box <?php echo $aktif >= 3 ? 'info-limit full' : 'info-limit'; ?>">
            <span><?php echo $aktif >= 3 ? '🚫' : '📚'; ?></span>
            <div>
                <strong>Pinjaman Aktif: <?php echo $aktif; ?>/3 buku</strong>
                <?php if ($aktif >= 3): ?>
                    — Kamu sudah mencapai batas maksimal. Kembalikan buku terlebih dahulu untuk dapat meminjam lagi.
                <?php else: ?>
                    — Kamu masih bisa meminjam <strong><?php echo 3 - $aktif; ?></strong> buku lagi.
                <?php endif; ?>
            </div>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
            <div style="background:#dcfce7;color:#16a34a;padding:1rem;border-radius:0.5rem;margin-bottom:1.5rem;border:1px solid #bbf7d0;">
                ✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div style="background:#fee2e2;color:#dc2626;padding:1rem;border-radius:0.5rem;margin-bottom:1.5rem;border:1px solid #fecaca;">
                ❌ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Tgl Dikembalikan</th>
                    <th>Status</th>
                    <th>Denda</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                        $is_overdue = ($row['status'] == 'dipinjam' && $today > $row['tanggal_kembali']);
                        // Estimate denda for display when still borrowed (not yet returned)
                        $denda_estimasi = 0;
                        if ($is_overdue) {
                            $hari = (int)((strtotime($today) - strtotime($row['tanggal_kembali'])) / 86400);
                            $denda_estimasi = $hari * 1000;
                        }
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                        <td>
                            <?php echo date('d M Y', strtotime($row['tanggal_kembali'])); ?>
                            <?php if ($row['perpanjangan'] >= 1): ?>
                                <span class="badge badge-extended">Diperpanjang</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $row['tanggal_dikembalikan'] ? date('d M Y', strtotime($row['tanggal_dikembalikan'])) : '-'; ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'dipinjam' && $is_overdue): ?>
                                <span class="badge badge-terlambat">⚠ Terlambat</span>
                            <?php elseif($row['status'] == 'dipinjam'): ?>
                                <span class="badge badge-dipinjam">Dipinjam</span>
                            <?php else: ?>
                                <span class="badge badge-kembali">Dikembalikan</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'kembali' && $row['denda'] > 0): ?>
                                <span class="denda-val">Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?></span>
                            <?php elseif($row['status'] == 'kembali'): ?>
                                <span class="denda-ok">Rp 0</span>
                            <?php elseif($is_overdue): ?>
                                <span class="denda-val" title="Estimasi denda jika dikembalikan hari ini">~Rp <?php echo number_format($denda_estimasi, 0, ',', '.'); ?></span>
                            <?php else: ?>
                                <span style="color:#94a3b8;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'dipinjam'): ?>
                                <?php if($row['perpanjangan'] == 0): ?>
                                    <a href="perpanjang_proses.php?id=<?php echo $row['id_transaksi']; ?>"
                                       class="btn-perpanjang"
                                       onclick="return confirm('Perpanjang masa peminjaman buku ini +7 hari?')">
                                       🔄 Perpanjang
                                    </a><br>
                                <?php endif; ?>
                                <a href="kembali_proses.php?id=<?php echo $row['id_transaksi']; ?>"
                                   class="btn-kembalikan"
                                   onclick="return confirm('Kembalikan buku ini sekarang?<?php echo $is_overdue ? " Kamu akan dikenai denda keterlambatan." : ""; ?>')">
                                   ↩ Kembalikan
                                </a>
                            <?php else: ?>
                                <span style="color:#94a3b8; font-size:0.85rem;">✓ Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:2.5rem; color:#64748b;">
                            📖 Belum ada riwayat peminjaman.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <p style="margin-top:1rem; font-size:0.82rem; color:#64748b;">
            ⏰ Denda keterlambatan: <strong>Rp 1.000/hari</strong> &nbsp;|&nbsp; 
            📅 Batas peminjaman: <strong>7 hari</strong> &nbsp;|&nbsp;
            🔄 Perpanjangan: <strong>1 kali (+7 hari)</strong>
        </p>
    </div>
</body>
</html>
