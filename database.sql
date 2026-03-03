-- Database for UKK Perpustakaan (Simplified)

CREATE DATABASE IF NOT EXISTS ukk_perpustakaan;
USE ukk_perpustakaan;

-- Table: Admin (Auth for Admin)
CREATE TABLE admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: Buku (Book Inventory)
CREATE TABLE buku (
    id_buku INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    penerbit VARCHAR(100) NOT NULL,
    tahun INT NOT NULL,
    kategori VARCHAR(50),
    stok INT NOT NULL DEFAULT 0,
    gambar VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: Anggota (Student Profile & Auth)
CREATE TABLE anggota (
    id_anggota INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    kontak VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: Transaksi (Borrowing)
CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_buku INT NOT NULL,
    id_anggota INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE,
    tanggal_dikembalikan DATE,
    status ENUM('dipinjam', 'kembali') NOT NULL DEFAULT 'dipinjam',
    perpanjangan TINYINT NOT NULL DEFAULT 0,  -- 0 = belum diperpanjang, 1 = sudah diperpanjang
    denda INT NOT NULL DEFAULT 0,              -- denda dalam Rupiah (Rp 1.000/hari keterlambatan)
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku) ON DELETE CASCADE,
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota) ON DELETE CASCADE
);

-- Default Data
INSERT INTO admin (username, password, nama) VALUES ('admin', MD5('admin123'), 'Administrator');
INSERT INTO anggota (username, password, nama, kelas, kontak) VALUES ('siswa', MD5('siswa123'), 'Siswa Teladan', 'XII RPL 1', '081234567890');

INSERT INTO buku (judul, penulis, penerbit, tahun, kategori, stok) VALUES 
('Belajar PHP Native', 'Budi Santoso', 'Informatika', 2023, 'Pendidikan', 10),
('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Novel', 5),
('Filosofi Teras', 'Henry Manampiring', 'Kompas', 2018, 'Psikologi', 7);
