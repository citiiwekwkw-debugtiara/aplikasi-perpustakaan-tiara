<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);

    // 1. Try Admin Login
    $query_admin = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $res_admin = mysqli_query($koneksi, $query_admin);

    if (mysqli_num_rows($res_admin) == 1) {
        $row = mysqli_fetch_assoc($res_admin);
        $_SESSION['user_id'] = $row['id_admin'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['role'] = 'admin';
        header("Location: ../admin/index.php");
        exit;
    }

    // 2. Try Anggota (Student) Login
    $query_anggota = "SELECT * FROM anggota WHERE username = '$username' AND password = '$password'";
    $res_anggota = mysqli_query($koneksi, $query_anggota);

    if (mysqli_num_rows($res_anggota) == 1) {
        $row = mysqli_fetch_assoc($res_anggota);
        $_SESSION['user_id'] = $row['id_anggota'];
        $_SESSION['id_anggota'] = $row['id_anggota'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['role'] = 'siswa';
        header("Location: ../siswa/index.php");
        exit;
    }

    // If both fail
    header("Location: ../index.php?error=Username atau Password salah!");
} else {
    header("Location: ../index.php");
}
?>
