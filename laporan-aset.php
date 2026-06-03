<?php
$pageTitle = 'Laporan Aset - Sistem Informasi Manajemen Warga';
$activePage = 'laporan-aset';
include 'inc/data.php';
include 'inc/header.php';

$totalAset = count($assetList);
$totalBaik = array_sum(array_column($assetList, 'baik'));
$totalRusak = array_sum(array_column($assetList, 'rusak'));
$totalAktif = count(array_filter($assetList, fn($row) => $row['status'] === 'Aktif'));
?>

<header class="dashboard-header">
    <h1>Laporan Aset</h1>
    <div class="periode-aktif">Ringkasan inventaris, kondisi, dan status aset warga.</div>
</header>

<section class="finance-cards-grid">
    <div class="card card-saldo">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Jenis Aset</span>
                <span class="card-value"><?= $totalAset ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-boxes"></i></div>
        </div>
    </div>
    <div class="card card-pemasukan">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Unit Baik</span>
                <span class="card-value"><?= $totalBaik ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-check"></i></div>
        </div>
    </div>
    <div class="card card-pengeluaran">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Unit Rusak</span>
                <span class="card-value"><?= $totalRusak ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-tools"></i></div>
        </div>
    </div>
    <div class="card card-piutang">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Aset Aktif</span>
                <span class="card-value"><?= $totalAktif ?></span>
            </div>
            <div class="card-icon"><i class="fas fa-toggle-on"></i></div>
        </div>
    </div>
</section>

<section class="details-box transaksi-box">
    <div class="transaksi-header">
        <h2>Rincian Aset</h2>
        <div class="report-actions">
            <button class="btn-filter" type="button" onclick="generateLaporanPdf('Laporan Aset')">
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
                    <th>Nama Aset</th>
                    <th>Lokasi</th>
                    <th class="text-right">Baik</th>
                    <th class="text-right">Rusak</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assetList as $index => $aset): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($aset['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($aset['lokasi'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-right text-success"><?= htmlspecialchars($aset['baik'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-right text-danger"><?= htmlspecialchars($aset['rusak'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($aset['keterangan'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="type-badge <?= $aset['status'] === 'Aktif' ? 'badge-pemasukan' : 'badge-pengeluaran' ?>"><?= htmlspecialchars($aset['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total Unit</th>
                    <th class="text-right text-success"><?= $totalBaik ?></th>
                    <th class="text-right text-danger"><?= $totalRusak ?></th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<?php include 'inc/footer.php'; ?>
