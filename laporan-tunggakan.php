<?php
$pageTitle = 'Laporan Tunggakan - Sistem Informasi Manajemen Warga';
$activePage = 'laporan-tunggakan';
include 'inc/data.php';
include 'inc/header.php';

function formatRupiahTunggakan($value) {
    return 'Rp ' . number_format($value, 0, ',', '.');
}

$totalTunggakan = array_sum(array_column($tunggakanList, 'nominal'));
$jumlahWargaMenunggak = count(array_unique(array_column($tunggakanList, 'nama')));
$jumlahTerlambat = count(array_filter($tunggakanList, fn($row) => $row['status'] === 'Terlambat'));
?>

<header class="dashboard-header">
    <h1>Laporan Tunggakan</h1>
    <div class="periode-aktif">Ringkasan tunggakan iuran warga yang belum diselesaikan.</div>
</header>

<section class="finance-cards-grid">
    <div class="card card-piutang">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Total Tunggakan</span>
                <span class="card-value"><?= htmlspecialchars(formatRupiahTunggakan($totalTunggakan), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-file-invoice-dollar"></i></div>
        </div>
    </div>
    <div class="card card-saldo">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Warga Menunggak</span>
                <span class="card-value"><?= $jumlahWargaMenunggak ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="card card-pengeluaran">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Terlambat</span>
                <span class="card-value"><?= $jumlahTerlambat ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
</section>

<section class="details-box transaksi-box">
    <div class="transaksi-header">
        <h2>Rincian Tunggakan</h2>
        <div class="report-actions">
            <button class="btn-filter" type="button" onclick="generateLaporanPdf('Laporan Tunggakan')">
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
                    <th>Nama Warga</th>
                    <th>Alamat</th>
                    <th>Periode</th>
                    <th>Jenis Iuran</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tunggakanList as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['blok'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['periode'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['jenis'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['jatuh_tempo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="type-badge badge-pengeluaran"><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td class="text-right text-danger"><?= htmlspecialchars(formatRupiahTunggakan($row['nominal']), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-right">Total Tunggakan</th>
                    <th class="text-right text-danger"><?= htmlspecialchars(formatRupiahTunggakan($totalTunggakan), ENT_QUOTES, 'UTF-8') ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<?php include 'inc/footer.php'; ?>
