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

    // Check if the transaction belongs to this student and is currently borrowed
    $check_query = "SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi' AND id_anggota = '$id_anggota' AND status = 'dipinjam'";
    $check_result = mysqli_query($koneksi, $check_query);

    if (mysqli_num_rows($check_result) == 1) {
        $transaksi = mysqli_fetch_assoc($check_result);
        $id_buku = $transaksi['id_buku'];
        $tanggal_dikembalikan = date('Y-m-d');

        // === HITUNG DENDA ===
        $denda = 0;
        $tanggal_kembali = $transaksi['tanggal_kembali'];
        if ($tanggal_dikembalikan > $tanggal_kembali) {
            $terlambat = (strtotime($tanggal_dikembalikan) - strtotime($tanggal_kembali)) / 86400;
            $denda = (int)$terlambat * 1000; // Rp 1.000 per hari
        }

        // Update transaction status + denda
        $update_transaksi = "UPDATE transaksi SET 
                             tanggal_dikembalikan = '$tanggal_dikembalikan', 
                             status = 'kembali',
                             denda = '$denda'
                             WHERE id_transaksi = '$id_transaksi'";

        if (mysqli_query($koneksi, $update_transaksi)) {
            // Update book stock
            $update_buku = "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'";
            mysqli_query($koneksi, $update_buku);

            if ($denda > 0) {
                $hari_terlambat = (int)((strtotime($tanggal_dikembalikan) - strtotime($tanggal_kembali)) / 86400);
                $_SESSION['success'] = "Buku berhasil dikembalikan. Terlambat $hari_terlambat hari. Denda: Rp " . number_format($denda, 0, ',', '.');
            } else {
                $_SESSION['success'] = "Buku berhasil dikembalikan tepat waktu. Tidak ada denda.";
            }
        } else {
            $_SESSION['error'] = "Gagal memproses pengembalian.";
        }
    } else {
        $_SESSION['error'] = "Transaksi tidak valid atau buku sudah dikembalikan.";
    }
}

header("Location: riwayat.php");
exit;
?>
