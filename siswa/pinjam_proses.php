<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_buku = mysqli_real_escape_string($koneksi, $_GET['id']);
    $id_anggota = $_SESSION['id_anggota'];
    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime('+7 days'));

    // === CEK BATAS MAKSIMAL 3 PINJAMAN AKTIF ===
    $cek_aktif = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE id_anggota = '$id_anggota' AND status = 'dipinjam'");
    $data_aktif = mysqli_fetch_assoc($cek_aktif);

    if ($data_aktif['total'] >= 3) {
        $_SESSION['error'] = "Batas peminjaman tercapai! Kamu sudah meminjam " . $data_aktif['total'] . " buku. Maksimal peminjaman adalah 3 buku. Kembalikan buku terlebih dahulu untuk bisa meminjam lagi.";
        header("Location: buku.php");
        exit;
    }

    // Check if book exists and stock is available
    $check_buku = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id_buku = '$id_buku'");
    $buku = mysqli_fetch_assoc($check_buku);

    if ($buku && $buku['stok'] > 0) {
        // Start transaction
        mysqli_begin_transaction($koneksi);

        try {
            // Insert into transaksi table
            $query_transaksi = "INSERT INTO transaksi (id_buku, id_anggota, tanggal_pinjam, tanggal_kembali, status, perpanjangan, denda) 
                                VALUES ('$id_buku', '$id_anggota', '$tanggal_pinjam', '$tanggal_kembali', 'dipinjam', 0, 0)";
            mysqli_query($koneksi, $query_transaksi);

            // Update book stock
            $query_update_stok = "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'";
            mysqli_query($koneksi, $query_update_stok);

            // Commit transaction
            mysqli_commit($koneksi);

            $_SESSION['success'] = "Buku berhasil dipinjam! Batas pengembalian: " . date('d M Y', strtotime($tanggal_kembali));
        } catch (Exception $e) {
            // Rollback on error
            mysqli_rollback($koneksi);
            $_SESSION['error'] = "Terjadi kesalahan saat meminjam buku.";
        }
    } else {
        $_SESSION['error'] = "Stok buku habis atau buku tidak ditemukan.";
    }
}

header("Location: riwayat.php");
exit;
?>
