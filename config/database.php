<?php
// Baca dari environment variables (Vercel/Aiven) atau fallback ke nilai lokal XAMPP
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'ukk_perpustakaan';
$port = (int)(getenv('DB_PORT') ?: 3306);

$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Aktifkan SSL jika bukan localhost (untuk Aiven)
if ($host !== 'localhost' && $host !== '127.0.0.1') {
    mysqli_ssl_set($koneksi, null, null, null, null, null);
    mysqli_options($koneksi, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
}
?>
