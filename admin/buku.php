<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Logic for Deletion
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku='$id'");
    header("Location: buku.php");
    exit;
}

$query = "SELECT * FROM buku ORDER BY id_buku DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Buku - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 0.5rem; overflow: hidden; box-shadow: var(--shadow); }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #a8df85; }
        th { background: #ffaab8; font-weight: 600; color: var(--text-light); }
        tr:hover { background: #f0ffdf; }
        .action-links a { margin-right: 0.5rem; text-decoration: none; font-size: 0.9rem; }
        .btn-add { display: inline-block; margin-bottom: 1.5rem; background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 0.3rem; text-decoration: none; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <h2>Data Buku</h2>
                <a href="buku_tambah.php" class="btn-add">+ Tambah Buku Baru</a>
            </header>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['judul']; ?></td>
                        <td><?php echo $row['penulis']; ?></td>
                        <td><?php echo $row['penerbit']; ?></td>
                        <td><?php echo $row['tahun']; ?></td>
                        <td><?php echo $row['stok']; ?></td>
                        <td class="action-links">
                            <a href="buku_edit.php?id=<?php echo $row['id_buku']; ?>" style="color: blue;">Edit</a>
                            <a href="buku.php?hapus=<?php echo $row['id_buku']; ?>" onclick="return confirm('Yakin hapus buku ini?')" style="color: red;">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
