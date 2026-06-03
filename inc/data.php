<?php
require_once __DIR__ . '/db.php';

function badgeClass($status) {
    return $status === 'Domisili' || $status === 'Aktif' || $status === 'Selesai' || $status === 'Baik'
        ? 'badge-pemasukan'
        : 'badge-pengeluaran';
}

function formatDataRupiah($value) {
    return 'Rp ' . number_format((int) $value, 0, ',', '.');
}

function dbFetchAll($sql) {
    global $pdo, $dbAvailable;
    if (!$dbAvailable || !$pdo) {
        return [];
    }

    return $pdo->query($sql)->fetchAll();
}

$wargaList = [
    ['nama' => 'Bapak Ahmad', 'alamat' => 'Blok A1 No. 12', 'hp' => '08123456789', 'jumlah_anggota' => 4, 'status' => 'Domisili', 'peran_1' => 'Warga', 'peran_2' => null],
    ['nama' => 'Ibu Siti', 'alamat' => 'Blok B2 No. 05', 'hp' => '08123456789', 'jumlah_anggota' => 3, 'status' => 'Kontrak', 'peran_1' => 'Warga', 'peran_2' => null],
    ['nama' => 'Pak Joko', 'alamat' => 'Blok C3 No. 18', 'hp' => '08123456789', 'jumlah_anggota' => 5, 'status' => 'Domisili', 'peran_1' => 'Warga', 'peran_2' => 'Pengurus'],
];

$jenisPemasukan = [
    ['jenis' => 'Iuran Bulanan', 'deskripsi' => 'Iuran wajib RT/RW untuk pemeliharaan fasilitas.'],
    ['jenis' => 'Donasi Kegiatan', 'deskripsi' => 'Sumbangan khusus untuk event komunitas atau kegiatan sosial.'],
    ['jenis' => 'Pendapatan Sewa', 'deskripsi' => 'Pendapatan dari penyewaan fasilitas umum RT/RW.'],
    ['jenis' => 'Penjualan Sampah', 'deskripsi' => 'Pendapatan dari program bank sampah warga.'],
];

$jenisPengeluaran = [
    ['jenis' => 'Gaji Satpam', 'deskripsi' => 'Biaya gaji petugas keamanan RT/RW.'],
    ['jenis' => 'Pembelian Alat', 'deskripsi' => 'Pengeluaran untuk pembelian perlengkapan RT/RW.'],
    ['jenis' => 'Perawatan Fasilitas', 'deskripsi' => 'Biaya pemeliharaan fasilitas umum dan perbaikan kecil.'],
    ['jenis' => 'Donasi Sosial', 'deskripsi' => 'Dana untuk kegiatan sosial atau bantuan darurat.'],
];

$assetList = [
    ['nama' => 'Lapang Olahraga', 'lokasi' => 'RT 01', 'baik' => 1, 'rusak' => 0, 'keterangan' => 'Digunakan untuk olahraga warga', 'status' => 'Aktif'],
    ['nama' => 'Balai Pertemuan', 'lokasi' => 'RT 03', 'baik' => 1, 'rusak' => 0, 'keterangan' => 'Tempat acara rutin RT/RW', 'status' => 'Aktif'],
    ['nama' => 'Pompa Air', 'lokasi' => 'Sumber Air', 'baik' => 0, 'rusak' => 1, 'keterangan' => 'Sedang menunggu suku cadang', 'status' => 'Tidak Aktif'],
    ['nama' => 'Kursi Lipat', 'lokasi' => 'Gudang', 'baik' => 20, 'rusak' => 2, 'keterangan' => 'Tersedia untuk kegiatan sementara', 'status' => 'Aktif'],
];

$kondisiList = [
    ['aset' => 'Balai Pertemuan', 'lokasi' => 'RT 03', 'tanggal' => '2026-05-12', 'kondisi' => 'Baik', 'petugas' => 'Ketua RT', 'catatan' => 'Siap digunakan untuk kegiatan warga.', 'status' => 'Selesai'],
    ['aset' => 'Pompa Air', 'lokasi' => 'Sumber Air', 'tanggal' => '2026-05-14', 'kondisi' => 'Rusak', 'petugas' => 'Petugas Aset', 'catatan' => 'Perlu penggantian suku cadang.', 'status' => 'Perbaikan'],
    ['aset' => 'Kursi Lipat', 'lokasi' => 'Gudang', 'tanggal' => '2026-05-18', 'kondisi' => 'Perlu Perawatan', 'petugas' => 'Bendahara RT', 'catatan' => 'Beberapa kursi perlu dikencangkan bautnya.', 'status' => 'Dipantau'],
];

$sewaList = [
    ['aset' => 'Balai Pertemuan', 'penyewa' => 'PKK RT 03', 'mulai' => '2026-05-10', 'selesai' => '2026-05-10', 'biaya' => 'Rp 200.000', 'status' => 'Selesai'],
    ['aset' => 'Lapang Olahraga', 'penyewa' => 'Panitia Olahraga', 'mulai' => '2026-06-01', 'selesai' => '2026-06-03', 'biaya' => 'Rp 500.000', 'status' => 'Aktif'],
];

$tunggakanList = [
    ['nama' => 'Bapak Ahmad', 'blok' => 'Blok A1 No. 12', 'periode' => 'April 2026', 'jenis' => 'Iuran Keamanan', 'nominal' => 50000, 'jatuh_tempo' => '2026-04-30', 'status' => 'Belum Bayar'],
    ['nama' => 'Ibu Siti', 'blok' => 'Blok B2 No. 05', 'periode' => 'Maret 2026', 'jenis' => 'Iuran Kebersihan', 'nominal' => 75000, 'jatuh_tempo' => '2026-03-31', 'status' => 'Terlambat'],
    ['nama' => 'Pak Joko', 'blok' => 'Blok C3 No. 18', 'periode' => 'Mei 2026', 'jenis' => 'Iuran Keamanan', 'nominal' => 50000, 'jatuh_tempo' => '2026-05-31', 'status' => 'Belum Bayar'],
];

$accessList = [
    ['nama' => 'Admin Utama', 'username' => 'admin', 'role' => 'Administrator', 'status' => 'Aktif'],
    ['nama' => 'Bendahara RT', 'username' => 'bendahara', 'role' => 'Bendahara', 'status' => 'Aktif'],
    ['nama' => 'Ketua RT', 'username' => 'ketua_rt', 'role' => 'Ketua RT', 'status' => 'Aktif'],
];

$transactions = [
    ['tanggal' => '20 Mei 2026', 'jenis' => 'Pemasukan', 'kategori' => 'Iuran Bulanan', 'label' => 'badge-pemasukan', 'sumber' => 'Bapak Ahmad (Blok A1)', 'keterangan' => 'Iuran Keamanan', 'jumlah' => '+ Rp 50.000', 'class' => 'text-success'],
    ['tanggal' => '19 Mei 2026', 'jenis' => 'Pengeluaran', 'kategori' => 'Gaji Satpam', 'label' => 'badge-pengeluaran', 'sumber' => 'Pak Joko (Satpam)', 'keterangan' => 'Gaji Satpam', 'jumlah' => '- Rp 2.500.000', 'class' => 'text-danger'],
    ['tanggal' => '18 Mei 2026', 'jenis' => 'Pemasukan', 'kategori' => 'Penjualan Sampah', 'label' => 'badge-pemasukan', 'sumber' => 'Bank Sampah Berkah', 'keterangan' => 'Penjualan Sampah Warga', 'jumlah' => '+ Rp 450.000', 'class' => 'text-success'],
];

$stats = ['terbayar' => 120, 'total' => 150, 'persen' => 80];

if ($dbAvailable) {
    $dbWarga = dbFetchAll('SELECT id, nama, alamat, hp, jumlah_anggota, status, peran_1, peran_2 FROM warga ORDER BY id ASC');
    if ($dbWarga) {
        $wargaList = $dbWarga;
    }

    $dbJenisPemasukan = dbFetchAll('SELECT id, nama AS jenis, deskripsi FROM jenis_transaksi WHERE tipe = "Pemasukan" ORDER BY id ASC');
    if ($dbJenisPemasukan) {
        $jenisPemasukan = $dbJenisPemasukan;
    }

    $dbJenisPengeluaran = dbFetchAll('SELECT nama AS jenis, deskripsi FROM jenis_transaksi WHERE tipe = "Pengeluaran" ORDER BY id ASC');
    if ($dbJenisPengeluaran) {
        $jenisPengeluaran = $dbJenisPengeluaran;
    }

    $dbAset = dbFetchAll('SELECT nama, lokasi, jumlah_baik AS baik, jumlah_rusak AS rusak, keterangan, status FROM aset ORDER BY id ASC');
    if ($dbAset) {
        $assetList = $dbAset;
    }

    $dbKondisi = dbFetchAll('SELECT a.nama AS aset, a.lokasi, k.tanggal_cek AS tanggal, k.kondisi, k.petugas, k.catatan, k.status FROM kondisi_aset k JOIN aset a ON a.id = k.aset_id ORDER BY k.tanggal_cek DESC, k.id DESC');
    if ($dbKondisi) {
        $kondisiList = $dbKondisi;
    }

    $dbSewa = dbFetchAll('SELECT a.nama AS aset, s.penyewa, s.tanggal_mulai AS mulai, s.tanggal_selesai AS selesai, CONCAT("Rp ", REPLACE(FORMAT(s.biaya, 0), ",", ".")) AS biaya, s.status FROM sewa_aset s JOIN aset a ON a.id = s.aset_id ORDER BY s.tanggal_mulai DESC, s.id DESC');
    if ($dbSewa) {
        $sewaList = $dbSewa;
    }

    $dbTunggakan = dbFetchAll('SELECT w.nama, w.alamat AS blok, t.periode, t.jenis_iuran AS jenis, t.nominal, t.jatuh_tempo, t.status FROM tunggakan t JOIN warga w ON w.id = t.warga_id ORDER BY t.jatuh_tempo ASC, t.id ASC');
    if ($dbTunggakan) {
        $tunggakanList = $dbTunggakan;
    }

    $dbAccess = dbFetchAll('SELECT nama, username, role, status FROM users ORDER BY id ASC');
    if ($dbAccess) {
        $accessList = $dbAccess;
    }

    $dbTransactions = dbFetchAll('SELECT t.id, t.tanggal, t.tipe AS jenis, jt.nama AS kategori, t.sumber, t.keterangan, t.jumlah FROM transaksi t LEFT JOIN jenis_transaksi jt ON jt.id = t.jenis_transaksi_id ORDER BY t.tanggal DESC, t.id DESC');
    if ($dbTransactions) {
        $transactions = array_map(function ($row) {
            $isPemasukan = $row['jenis'] === 'Pemasukan';
            return [
                'id' => $row['id'],
                'tanggal' => date('d M Y', strtotime($row['tanggal'])),
                'jenis' => $row['jenis'],
                'kategori' => $row['kategori'] ?: $row['jenis'],
                'label' => $isPemasukan ? 'badge-pemasukan' : 'badge-pengeluaran',
                'sumber' => $row['sumber'],
                'keterangan' => $row['keterangan'],
                'jumlah' => ($isPemasukan ? '+ ' : '- ') . formatDataRupiah(abs($row['jumlah'])),
                'class' => $isPemasukan ? 'text-success' : 'text-danger',
            ];
        }, $dbTransactions);
    }

    $dbStats = dbFetchAll('SELECT kk_terbayar AS terbayar, kk_total AS total FROM dashboard_stats ORDER BY id DESC LIMIT 1');
    if ($dbStats) {
        $stats = $dbStats[0];
        $stats['persen'] = $stats['total'] > 0 ? round(($stats['terbayar'] / $stats['total']) * 100) : 0;
    }
}

$totalPemasukanDashboard = 0;
$totalPengeluaranDashboard = 0;
foreach ($transactions as $transaction) {
    $amount = (int) preg_replace('/[^0-9]/', '', $transaction['jumlah']);
    if (strtolower($transaction['jenis']) === 'pemasukan') {
        $totalPemasukanDashboard += $amount;
    } elseif (strtolower($transaction['jenis']) === 'pengeluaran') {
        $totalPengeluaranDashboard += $amount;
    }
}

$dashboardCards = [
    ['title' => 'Total Pemasukan (Bulan Ini)', 'value' => formatDataRupiah($totalPemasukanDashboard), 'footer' => 'Dihitung dari tabel transaksi', 'class' => 'pemasukan', 'icon' => 'fa-arrow-down'],
    ['title' => 'Total Pengeluaran (Bulan Ini)', 'value' => formatDataRupiah($totalPengeluaranDashboard), 'class' => 'pengeluaran', 'icon' => 'fa-arrow-up'],
    ['title' => 'Saldo Kas Saat Ini', 'value' => formatDataRupiah($totalPemasukanDashboard - $totalPengeluaranDashboard), 'class' => 'saldo', 'icon' => 'fa-wallet'],
    ['title' => 'Total Keseluruhan Piutang', 'value' => formatDataRupiah(array_sum(array_column($tunggakanList, 'nominal'))), 'class' => 'piutang', 'icon' => 'fa-file-invoice-dollar'],
];
?>
