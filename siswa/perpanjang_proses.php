<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_transaksi = mysqli_real_escape_string($koneksi, $_GET['id']);
    $id_anggota = $_SESSION['id_anggota'];

    // Fetch transaction and verify ownership + eligibility
    $query = "SELECT * FROM transaksi 
              WHERE id_transaksi = '$id_transaksi' 
              AND id_anggota = '$id_anggota' 
              AND status = 'dipinjam'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) == 1) {
        $transaksi = mysqli_fetch_assoc($result);

        // Only allow extension if hasn't been extended yet
        if ($transaksi['perpanjangan'] >= 1) {
            $_SESSION['error'] = "Peminjaman ini sudah pernah diperpanjang. Perpanjangan hanya diizinkan 1 kali.";
        } else {
            // Extend by 7 days from current due date
            $tanggal_kembali_baru = date('Y-m-d', strtotime($transaksi['tanggal_kembali'] . ' +7 days'));

            $update = "UPDATE transaksi 
                       SET tanggal_kembali = '$tanggal_kembali_baru', perpanjangan = 1 
                       WHERE id_transaksi = '$id_transaksi'";

            if (mysqli_query($koneksi, $update)) {
                $_SESSION['success'] = "Masa peminjaman berhasil diperpanjang! Batas kembali baru: " . date('d M Y', strtotime($tanggal_kembali_baru));
            } else {
                $_SESSION['error'] = "Gagal memperpanjang peminjaman. Silakan coba lagi.";
            }
        }
    } else {
        $_SESSION['error'] = "Transaksi tidak valid atau buku sudah dikembalikan.";
    }
}

header("Location: riwayat.php");
exit;
?>
