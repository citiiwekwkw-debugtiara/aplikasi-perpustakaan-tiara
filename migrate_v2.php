<?php
require_once 'config/database.php';

$results = [];

// Add 'denda' column if not exists
$check_denda = mysqli_query($koneksi, "SHOW COLUMNS FROM transaksi LIKE 'denda'");
if (mysqli_num_rows($check_denda) == 0) {
    if (mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN denda INT NOT NULL DEFAULT 0")) {
        $results[] = ["status" => "OK", "msg" => "Kolom 'denda' berhasil ditambahkan."];
    } else {
        $results[] = ["status" => "ERROR", "msg" => "Gagal tambah kolom 'denda': " . mysqli_error($koneksi)];
    }
} else {
    $results[] = ["status" => "SKIP", "msg" => "Kolom 'denda' sudah ada."];
}

// Add 'perpanjangan' column if not exists
$check_perp = mysqli_query($koneksi, "SHOW COLUMNS FROM transaksi LIKE 'perpanjangan'");
if (mysqli_num_rows($check_perp) == 0) {
    if (mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN perpanjangan TINYINT NOT NULL DEFAULT 0")) {
        $results[] = ["status" => "OK", "msg" => "Kolom 'perpanjangan' berhasil ditambahkan."];
    } else {
        $results[] = ["status" => "ERROR", "msg" => "Gagal tambah kolom 'perpanjangan': " . mysqli_error($koneksi)];
    }
} else {
    $results[] = ["status" => "SKIP", "msg" => "Kolom 'perpanjangan' sudah ada."];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Migrasi Database V2</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 4rem auto; padding: 2rem; }
        .ok { color: #16a34a; background: #dcfce7; padding: 0.5rem 1rem; border-radius: 0.3rem; margin-bottom: 0.5rem; }
        .error { color: #dc2626; background: #fee2e2; padding: 0.5rem 1rem; border-radius: 0.3rem; margin-bottom: 0.5rem; }
        .skip { color: #92400e; background: #fef3c7; padding: 0.5rem 1rem; border-radius: 0.3rem; margin-bottom: 0.5rem; }
        h2 { color: #1e293b; }
        a { display: inline-block; margin-top: 1.5rem; background: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.3rem; text-decoration: none; }
    </style>
</head>
<body>
    <h2>Migrasi Database V2 — Fitur Denda & Perpanjangan</h2>
    <?php foreach ($results as $r): ?>
        <div class="<?php echo strtolower($r['status']); ?>">
            <strong>[<?php echo $r['status']; ?>]</strong> <?php echo $r['msg']; ?>
        </div>
    <?php endforeach; ?>
    <a href="admin/transaksi.php">← Kembali ke Admin</a>
</body>
</html>
