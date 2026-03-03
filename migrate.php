<?php
require_once 'config/database.php';

// Disable foreign key checks to allow table drops
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");

mysqli_query($koneksi, "DROP TABLE IF EXISTS users");
mysqli_query($koneksi, "DROP TABLE IF EXISTS admin");
mysqli_query($koneksi, "DROP TABLE IF EXISTS anggota");
mysqli_query($koneksi, "DROP TABLE IF EXISTS transaksi");
mysqli_query($koneksi, "DROP TABLE IF EXISTS buku");

// Re-create tables based on new schema
$queries = [
    "CREATE TABLE admin (
        id_admin INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        nama VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE buku (
        id_buku INT AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(255) NOT NULL,
        penulis VARCHAR(100) NOT NULL,
        penerbit VARCHAR(100) NOT NULL,
        tahun INT NOT NULL,
        kategori VARCHAR(50),
        stok INT NOT NULL DEFAULT 0,
        gambar VARCHAR(255) DEFAULT 'default.jpg',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE anggota (
        id_anggota INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        nama VARCHAR(100) NOT NULL,
        kelas VARCHAR(20) NOT NULL,
        kontak VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE transaksi (
        id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
        id_buku INT NOT NULL,
        id_anggota INT NOT NULL,
        tanggal_pinjam DATE NOT NULL,
        tanggal_kembali DATE,
        tanggal_dikembalikan DATE,
        status ENUM('dipinjam', 'kembali') NOT NULL DEFAULT 'dipinjam',
        FOREIGN KEY (id_buku) REFERENCES buku(id_buku) ON DELETE CASCADE,
        FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota) ON DELETE CASCADE
    )",
    "INSERT INTO admin (username, password, nama) VALUES ('admin', MD5('admin123'), 'Administrator')",
    "INSERT INTO anggota (username, password, nama, kelas, kontak) VALUES ('siswa', MD5('siswa123'), 'Siswa Teladan', 'XII RPL 1', '081234567890')",
    "INSERT INTO buku (judul, penulis, penerbit, tahun, kategori, stok) VALUES 
    ('Belajar PHP Native', 'Budi Santoso', 'Informatika', 2023, 'Pendidikan', 10),
    ('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Novel', 5),
    ('Filosofi Teras', 'Henry Manampiring', 'Kompas', 2018, 'Psikologi', 7)"
];

foreach ($queries as $q) {
    if (!mysqli_query($koneksi, $q)) {
        echo "Error: " . mysqli_error($koneksi) . "\n";
    }
}

mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");
echo "Migration successful!";
?>
