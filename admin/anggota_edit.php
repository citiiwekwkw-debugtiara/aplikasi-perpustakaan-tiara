<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$id_anggota = $_GET['id'];
$query = "SELECT * FROM anggota WHERE id_anggota = '$id_anggota'";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $password_new = $_POST['password'];

    if(!empty($password_new)) {
        $pass_hash = md5($password_new);
        $update_query = "UPDATE anggota SET 
                         nama='$nama', 
                         kelas='$kelas', 
                         kontak='$kontak', 
                         password='$pass_hash' 
                         WHERE id_anggota='$id_anggota'";
    } else {
        $update_query = "UPDATE anggota SET 
                         nama='$nama', 
                         kelas='$kelas', 
                         kontak='$kontak' 
                         WHERE id_anggota='$id_anggota'";
    }

    if (mysqli_query($koneksi, $update_query)) {
        header("Location: anggota.php");
        exit;
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Anggota - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h2 style="margin-bottom: 1.5rem;">Edit Anggota</h2>
            
            <?php if(isset($error)): ?>
                <div style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-card" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow); max-width: 600px;">
                <form action="" method="POST">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-input" value="<?php echo $data['nama']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-input" value="<?php echo $data['username']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru (Biarkan kosong jika tidak diubah)</label>
                        <input type="password" name="password" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kelas</label>
                        <input type="text" name="kelas" class="form-input" value="<?php echo $data['kelas']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kontak</label>
                        <input type="text" name="kontak" class="form-input" value="<?php echo $data['kontak']; ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Update Anggota</button>
                    <a href="anggota.php" class="btn" style="background: #ccc; color: #333; margin-left: 0.5rem; text-decoration: none; padding: 0.5rem 1rem; border-radius: 0.3rem;">Batal</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
