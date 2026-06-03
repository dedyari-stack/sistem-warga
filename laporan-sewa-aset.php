<?php
$pageTitle = 'Laporan Sewa Aset - Sistem Informasi Manajemen Warga';
$activePage = 'laporan-sewa-aset';
include 'inc/data.php';
include 'inc/header.php';

function rupiahSewaToNumber($value) {
    $number = preg_replace('/[^0-9-]/', '', $value);
    return $number === '' ? 0 : (int) $number;
}

function formatRupiahSewa($value) {
    return 'Rp ' . number_format($value, 0, ',', '.');
}

$totalSewa = count($sewaList);
$totalPendapatan = array_sum(array_map(fn($row) => rupiahSewaToNumber($row['biaya']), $sewaList));
$sewaAktif = count(array_filter($sewaList, fn($row) => $row['status'] === 'Aktif'));
$sewaSelesai = count(array_filter($sewaList, fn($row) => $row['status'] === 'Selesai'));
?>

<header class="dashboard-header">
    <h1>Laporan Sewa Aset</h1>
    <div class="periode-aktif">Ringkasan transaksi penyewaan aset warga.</div>
</header>

<section class="finance-cards-grid">
    <div class="card card-saldo">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Total Sewa</span>
                <span class="card-value"><?= $totalSewa ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-clipboard-list"></i></div>
        </div>
    </div>
    <div class="card card-pemasukan">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Total Pendapatan</span>
                <span class="card-value"><?= htmlspecialchars(formatRupiahSewa($totalPendapatan), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-arrow-down"></i></div>
        </div>
    </div>
    <div class="card card-piutang">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Sewa Aktif</span>
                <span class="card-value"><?= $sewaAktif ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
        </div>
    </div>
    <div class="card card-pengeluaran">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Selesai</span>
                <span class="card-value"><?= $sewaSelesai ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
</section>

<section class="details-box transaksi-box">
    <div class="transaksi-header">
        <h2>Rincian Sewa Aset</h2>
        <div class="report-actions">
            <button class="btn-filter" type="button" onclick="generateLaporanPdf('Laporan Sewa Aset')">
                <i class="fas fa-file-pdf"></i> Generate PDF
            </button>
            <button class="btn-filter" type="button" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Aset</th>
                    <th>Penyewa</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>
                    <th class="text-right">Biaya</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sewaList as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['aset'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['penyewa'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['mulai'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['selesai'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="type-badge <?= $row['status'] === 'Aktif' ? 'badge-pemasukan' : 'badge-pengeluaran' ?>"><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td class="text-right text-success"><?= htmlspecialchars($row['biaya'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-right">Total Pendapatan Sewa</th>
                    <th class="text-right text-success"><?= htmlspecialchars(formatRupiahSewa($totalPendapatan), ENT_QUOTES, 'UTF-8') ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<?php include 'inc/footer.php'; ?>
