CREATE DATABASE IF NOT EXISTS sistem_warga
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sistem_warga;

CREATE TABLE IF NOT EXISTS warga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    alamat VARCHAR(180) NOT NULL,
    hp VARCHAR(30) NOT NULL,
    jumlah_anggota INT NOT NULL DEFAULT 1,
    status ENUM('Domisili', 'Kontrak', 'Aset') NOT NULL DEFAULT 'Domisili',
    peran_1 ENUM('Warga', 'Pengurus', 'Petugas Input', 'Admin') NOT NULL DEFAULT 'Warga',
    peran_2 ENUM('Warga', 'Pengurus', 'Petugas Input', 'Admin') NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS jenis_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipe ENUM('Pemasukan', 'Pengeluaran') NOT NULL,
    nama VARCHAR(120) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    tipe ENUM('Pemasukan', 'Pengeluaran') NOT NULL,
    jenis_transaksi_id INT NULL,
    sumber VARCHAR(160) NOT NULL,
    keterangan TEXT,
    jumlah DECIMAL(14,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_transaksi_jenis FOREIGN KEY (jenis_transaksi_id) REFERENCES jenis_transaksi(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS aset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(140) NOT NULL,
    lokasi VARCHAR(140) NOT NULL,
    jumlah_baik INT NOT NULL DEFAULT 0,
    jumlah_rusak INT NOT NULL DEFAULT 0,
    keterangan TEXT,
    status ENUM('Aktif', 'Tidak Aktif') NOT NULL DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kondisi_aset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aset_id INT NOT NULL,
    tanggal_cek DATE NOT NULL,
    kondisi ENUM('Baik', 'Perlu Perawatan', 'Rusak') NOT NULL,
    petugas VARCHAR(120) NOT NULL,
    catatan TEXT,
    status ENUM('Dipantau', 'Perbaikan', 'Selesai') NOT NULL DEFAULT 'Dipantau',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_kondisi_aset FOREIGN KEY (aset_id) REFERENCES aset(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sewa_aset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aset_id INT NOT NULL,
    penyewa VARCHAR(140) NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    biaya DECIMAL(14,2) NOT NULL DEFAULT 0,
    status ENUM('Aktif', 'Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sewa_aset FOREIGN KEY (aset_id) REFERENCES aset(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tunggakan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warga_id INT NOT NULL,
    periode VARCHAR(40) NOT NULL,
    jenis_iuran VARCHAR(120) NOT NULL,
    nominal DECIMAL(14,2) NOT NULL,
    jatuh_tempo DATE NOT NULL,
    status ENUM('Belum Bayar', 'Terlambat', 'Lunas') NOT NULL DEFAULT 'Belum Bayar',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tunggakan_warga FOREIGN KEY (warga_id) REFERENCES warga(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    username VARCHAR(80) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Administrator', 'Ketua RT', 'Bendahara', 'Petugas') NOT NULL DEFAULT 'Petugas',
    status ENUM('Aktif', 'Tidak Aktif') NOT NULL DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dashboard_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periode VARCHAR(80) NOT NULL,
    kk_terbayar INT NOT NULL DEFAULT 0,
    kk_total INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO warga (id, nama, alamat, hp, jumlah_anggota, status) VALUES
(1, 'Bapak Ahmad', 'Blok A1 No. 12', '08123456789', 4, 'Domisili'),
(2, 'Ibu Siti', 'Blok B2 No. 05', '08123456789', 3, 'Kontrak'),
(3, 'Pak Joko', 'Blok C3 No. 18', '08123456789', 5, 'Domisili')
ON DUPLICATE KEY UPDATE nama = VALUES(nama), alamat = VALUES(alamat), hp = VALUES(hp), jumlah_anggota = VALUES(jumlah_anggota), status = VALUES(status);

INSERT INTO jenis_transaksi (id, tipe, nama, deskripsi) VALUES
(1, 'Pemasukan', 'Iuran Bulanan', 'Iuran wajib RT/RW untuk pemeliharaan fasilitas.'),
(2, 'Pemasukan', 'Donasi Kegiatan', 'Sumbangan khusus untuk event komunitas atau kegiatan sosial.'),
(3, 'Pemasukan', 'Pendapatan Sewa', 'Pendapatan dari penyewaan fasilitas umum RT/RW.'),
(4, 'Pemasukan', 'Penjualan Sampah', 'Pendapatan dari program bank sampah warga.'),
(5, 'Pengeluaran', 'Gaji Satpam', 'Biaya gaji petugas keamanan RT/RW.'),
(6, 'Pengeluaran', 'Pembelian Alat', 'Pengeluaran untuk pembelian perlengkapan RT/RW.'),
(7, 'Pengeluaran', 'Perawatan Fasilitas', 'Biaya pemeliharaan fasilitas umum dan perbaikan kecil.'),
(8, 'Pengeluaran', 'Donasi Sosial', 'Dana untuk kegiatan sosial atau bantuan darurat.')
ON DUPLICATE KEY UPDATE tipe = VALUES(tipe), nama = VALUES(nama), deskripsi = VALUES(deskripsi);

INSERT INTO transaksi (id, tanggal, tipe, jenis_transaksi_id, sumber, keterangan, jumlah) VALUES
(1, '2026-05-20', 'Pemasukan', 1, 'Bapak Ahmad (Blok A1)', 'Iuran Keamanan', 50000),
(2, '2026-05-19', 'Pengeluaran', 5, 'Pak Joko (Satpam)', 'Gaji Satpam', 2500000),
(3, '2026-05-18', 'Pemasukan', 4, 'Bank Sampah Berkah', 'Penjualan Sampah Warga', 450000)
ON DUPLICATE KEY UPDATE tanggal = VALUES(tanggal), tipe = VALUES(tipe), jenis_transaksi_id = VALUES(jenis_transaksi_id), sumber = VALUES(sumber), keterangan = VALUES(keterangan), jumlah = VALUES(jumlah);

INSERT INTO aset (id, nama, lokasi, jumlah_baik, jumlah_rusak, keterangan, status) VALUES
(1, 'Lapang Olahraga', 'RT 01', 1, 0, 'Digunakan untuk olahraga warga', 'Aktif'),
(2, 'Balai Pertemuan', 'RT 03', 1, 0, 'Tempat acara rutin RT/RW', 'Aktif'),
(3, 'Pompa Air', 'Sumber Air', 0, 1, 'Sedang menunggu suku cadang', 'Tidak Aktif'),
(4, 'Kursi Lipat', 'Gudang', 20, 2, 'Tersedia untuk kegiatan sementara', 'Aktif')
ON DUPLICATE KEY UPDATE nama = VALUES(nama), lokasi = VALUES(lokasi), jumlah_baik = VALUES(jumlah_baik), jumlah_rusak = VALUES(jumlah_rusak), keterangan = VALUES(keterangan), status = VALUES(status);

INSERT INTO kondisi_aset (id, aset_id, tanggal_cek, kondisi, petugas, catatan, status) VALUES
(1, 2, '2026-05-12', 'Baik', 'Ketua RT', 'Siap digunakan untuk kegiatan warga.', 'Selesai'),
(2, 3, '2026-05-14', 'Rusak', 'Petugas Aset', 'Perlu penggantian suku cadang.', 'Perbaikan'),
(3, 4, '2026-05-18', 'Perlu Perawatan', 'Bendahara RT', 'Beberapa kursi perlu dikencangkan bautnya.', 'Dipantau')
ON DUPLICATE KEY UPDATE aset_id = VALUES(aset_id), tanggal_cek = VALUES(tanggal_cek), kondisi = VALUES(kondisi), petugas = VALUES(petugas), catatan = VALUES(catatan), status = VALUES(status);

INSERT INTO sewa_aset (id, aset_id, penyewa, tanggal_mulai, tanggal_selesai, biaya, status) VALUES
(1, 2, 'PKK RT 03', '2026-05-10', '2026-05-10', 200000, 'Selesai'),
(2, 1, 'Panitia Olahraga', '2026-06-01', '2026-06-03', 500000, 'Aktif')
ON DUPLICATE KEY UPDATE aset_id = VALUES(aset_id), penyewa = VALUES(penyewa), tanggal_mulai = VALUES(tanggal_mulai), tanggal_selesai = VALUES(tanggal_selesai), biaya = VALUES(biaya), status = VALUES(status);

INSERT INTO tunggakan (id, warga_id, periode, jenis_iuran, nominal, jatuh_tempo, status) VALUES
(1, 1, 'April 2026', 'Iuran Keamanan', 50000, '2026-04-30', 'Belum Bayar'),
(2, 2, 'Maret 2026', 'Iuran Kebersihan', 75000, '2026-03-31', 'Terlambat'),
(3, 3, 'Mei 2026', 'Iuran Keamanan', 50000, '2026-05-31', 'Belum Bayar')
ON DUPLICATE KEY UPDATE warga_id = VALUES(warga_id), periode = VALUES(periode), jenis_iuran = VALUES(jenis_iuran), nominal = VALUES(nominal), jatuh_tempo = VALUES(jatuh_tempo), status = VALUES(status);

INSERT INTO users (id, nama, username, password_hash, role, status) VALUES
(1, 'Admin Utama', 'admin', '$2y$10$CwTycUXWue0Thq9StjUM0uJ8fcnCr4fiZzwb8TxK7BT5OZtcscbY2', 'Administrator', 'Aktif'),
(2, 'Bendahara RT', 'bendahara', '$2y$10$CwTycUXWue0Thq9StjUM0uJ8fcnCr4fiZzwb8TxK7BT5OZtcscbY2', 'Bendahara', 'Aktif'),
(3, 'Ketua RT', 'ketua_rt', '$2y$10$CwTycUXWue0Thq9StjUM0uJ8fcnCr4fiZzwb8TxK7BT5OZtcscbY2', 'Ketua RT', 'Aktif')
ON DUPLICATE KEY UPDATE nama = VALUES(nama), username = VALUES(username), role = VALUES(role), status = VALUES(status);

INSERT INTO dashboard_stats (id, periode, kk_terbayar, kk_total) VALUES
(1, '20 April 2026 - 19 Mei 2026', 120, 150)
ON DUPLICATE KEY UPDATE periode = VALUES(periode), kk_terbayar = VALUES(kk_terbayar), kk_total = VALUES(kk_total);
