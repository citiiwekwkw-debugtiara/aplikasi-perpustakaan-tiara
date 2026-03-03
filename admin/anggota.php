<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Logic for Deletion
if (isset($_GET['hapus'])) {
    $id_anggota = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota='$id_anggota'");
    header("Location: anggota.php");
    exit;
}

$query = "SELECT * FROM anggota ORDER BY id_anggota DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Anggota - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 0.5rem; overflow: hidden; box-shadow: var(--shadow); }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #b8db80; }
        th { background: #f7f6d3; font-weight: 600; color: var(--text-dark); }
        tr:hover { background: #f8f9fa; }
        .action-links a { margin-right: 0.5rem; text-decoration: none; font-size: 0.9rem; }
        .btn-add { display: inline-block; margin-bottom: 1.5rem; background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 0.3rem; text-decoration: none; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <h2>Data Anggota (Siswa)</h2>
                <a href="anggota_tambah.php" class="btn-add">+ Tambah Anggota</a>
            </header>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Kelas</th>
                        <th>Kontak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['kelas']; ?></td>
                        <td><?php echo $row['kontak']; ?></td>
                        <td class="action-links">
                            <a href="anggota_edit.php?id=<?php echo $row['id_anggota']; ?>" style="color: blue;">Edit</a>
                            <a href="anggota.php?hapus=<?php echo $row['id_anggota']; ?>" onclick="return confirm('Yakin hapus anggota ini?')" style="color: red;">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
