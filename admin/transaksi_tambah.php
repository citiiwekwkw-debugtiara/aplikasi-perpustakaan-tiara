<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$error = '';

// Fetch Books (Only with stock > 0)
$books = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0 ORDER BY judul ASC");

// Fetch Members with their active loan count
$members = mysqli_query($koneksi, "
    SELECT a.*, COUNT(t.id_transaksi) AS pinjaman_aktif
    FROM anggota a
    LEFT JOIN transaksi t ON a.id_anggota = t.id_anggota AND t.status = 'dipinjam'
    GROUP BY a.id_anggota
    ORDER BY a.nama ASC
");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_anggota = mysqli_real_escape_string($koneksi, $_POST['id_anggota']);
    $id_buku    = mysqli_real_escape_string($koneksi, $_POST['id_buku']);
    $tanggal_pinjam  = date('Y-m-d');
    $tanggal_kembali = $_POST['tanggal_kembali'];

    // === CEK BATAS MAKSIMAL 3 PINJAMAN AKTIF per ANGGOTA ===
    $cek_aktif = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE id_anggota = '$id_anggota' AND status = 'dipinjam'");
    $jumlah_aktif = mysqli_fetch_assoc($cek_aktif)['total'];

    if ($jumlah_aktif >= 3) {
        $error = "Anggota ini sudah meminjam $jumlah_aktif buku (batas maksimal 3). Tidak dapat menambahkan peminjaman baru.";
    } else {
        // Check stock
        $cek_stok  = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id_buku = '$id_buku'");
        $stok_now  = mysqli_fetch_assoc($cek_stok)['stok'];

        if ($stok_now > 0) {
            $query = "INSERT INTO transaksi (id_buku, id_anggota, tanggal_pinjam, tanggal_kembali, status, perpanjangan, denda) 
                      VALUES ('$id_buku', '$id_anggota', '$tanggal_pinjam', '$tanggal_kembali', 'dipinjam', 0, 0)";

            if (mysqli_query($koneksi, $query)) {
                mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
                header("Location: transaksi.php");
                exit;
            } else {
                $error = "Error: " . mysqli_error($koneksi);
            }
        } else {
            $error = "Stok buku habis!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Peminjaman - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-card { background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow); max-width: 620px; }
        select { width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.9rem; }
        .badge-limit { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600; margin-left: 0.4rem; }
        .badge-full { background: #fee2e2; color: #dc2626; }
        .badge-ok   { background: #dcfce7; color: #16a34a; }
        .rules-box { background: #eff6ff; border: 1px solid #bfdbfe; padding: 0.9rem 1.2rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: #1e40af; }
        .rules-box li { margin-bottom: 0.25rem; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h2 style="margin-bottom: 1.5rem;">Catat Peminjaman Buku</h2>

            <div class="rules-box">
                <strong>📋 Aturan Peminjaman:</strong>
                <ul style="margin: 0.5rem 0 0 1.2rem; padding: 0;">
                    <li>Maksimal <strong>3 buku</strong> aktif per anggota</li>
                    <li>Batas peminjaman <strong>7 hari</strong></li>
                    <li>Denda <strong>Rp 1.000/hari</strong> keterlambatan</li>
                    <li>Perpanjangan <strong>1 kali (+7 hari)</strong></li>
                </ul>
            </div>

            <?php if($error): ?>
                <div style="background:#fee2e2; color:#dc2626; padding:1rem; border-radius:0.5rem; margin-bottom:1.5rem; border:1px solid #fca5a5;">
                    ❌ <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="form-card">
                <form action="" method="POST">
                    <div class="form-group">
                        <label class="form-label">Anggota Peminjam</label>
                        <select name="id_anggota" required>
                            <option value="">-- Pilih Anggota --</option>
                            <?php while($m = mysqli_fetch_assoc($members)): ?>
                                <?php $full = $m['pinjaman_aktif'] >= 3; ?>
                                <option value="<?php echo $m['id_anggota']; ?>"
                                    <?php echo $full ? 'disabled' : ''; ?>>
                                    <?php echo htmlspecialchars($m['nama']); ?> (<?php echo $m['kelas']; ?>)
                                    — Pinjaman: <?php echo $m['pinjaman_aktif']; ?>/3
                                    <?php echo $full ? '[PENUH]' : ''; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Buku yang Dipinjam</label>
                        <select name="id_buku" required>
                            <option value="">-- Pilih Buku --</option>
                            <?php while($b = mysqli_fetch_assoc($books)): ?>
                                <option value="<?php echo $b['id_buku']; ?>">
                                    <?php echo htmlspecialchars($b['judul']); ?> (Stok: <?php echo $b['stok']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Kembali (Rencana)</label>
                        <input type="date" name="tanggal_kembali" class="form-input" required
                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                               value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top:1rem;">Simpan Peminjaman</button>
                    <a href="transaksi.php" class="btn" style="background:#ccc; color:#333; margin-left:0.5rem; text-decoration:none; padding:0.5rem 1rem; border-radius:0.3rem;">Batal</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
