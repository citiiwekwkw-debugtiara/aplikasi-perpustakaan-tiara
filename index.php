<?php
session_start();
if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: siswa/index.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Digital</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-image">
                <div class="login-text">
                    <h2>Perpustakaan Digital</h2>
                    <p>Membaca adalah jendela dunia. Masuk untuk mulai menjelajah.</p>
                </div>
            </div>
            <div class="login-form-container">
                <div style="margin-bottom: 2rem;">
                    <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Selamat Datang Kembali</h1>
                    <p style="color: var(--text-light);">Silakan masuk ke akun Anda</p>
                </div>

                <?php if(isset($_SESSION['success'])): ?>
                    <div style="padding: 1rem; background: #dcfce7; color: #16a34a; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['error'])): ?>
                    <div style="padding: 1rem; background: #fee2e2; color: #dc2626; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="auth/login_process.php" method="POST">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-input" placeholder="Masukkan username" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk Sekarang</button>
                    <div style="text-align: center; margin-top: 1rem;">
                        <p style="font-size: 0.9rem; color: var(--text-light);">Belum punya akun? <a href="registrasi.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Daftar Siswa</a></p>
                    </div>
                    <!-- Hint for UKK: admin/admin123 or siswa/siswa123 -->
                </form>
                
                <div style="margin-top: 2rem; text-align: center; color: var(--text-light); font-size: 0.85rem;">
                    &copy; 2024 UKK RPL Perpustakaan Digital
                </div>
            </div>
        </div>
    </div>
</body>
</html>
