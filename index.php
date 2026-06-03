<?php
$pageTitle = 'Dashboard - Sistem Informasi Manajemen Warga';
$activePage = 'dashboard';
include 'inc/data.php';
include 'inc/header.php';
?>

<header class="dashboard-header">
    <h1>Dashboard</h1>
    <div class="periode-aktif">
        Periode Aktif: <strong>20 April 2026 - 19 Mei 2026</strong>
    </div>
</header>

<section class="notification-section">
    <div class="alert alert-danger" id="alert-tunggakan">
        <i class="fas fa-exclamation-triangle"></i>
        <div class="alert-text">
            <strong>Peringatan:</strong> Terdapat 3 warga (KK) dengan tunggakan iuran keamanan lebih dari 3 bulan. <a href="#">Lihat Detail</a>
        </div>
        <button class="close-btn" onclick="dismissAlert('alert-tunggakan')">&times;</button>
    </div>
    <div class="alert alert-warning" id="alert-sewa">
        <i class="fas fa-clock"></i>
        <div class="alert-text">
            <strong>Notifikasi:</strong> Masa sewa aset "Tenda Hajatan RT" (Penyewa: Bpk. Budi) akan berakhir dalam 5 hari. <a href="#">Lihat Detail</a>
        </div>
        <button class="close-btn" onclick="dismissAlert('alert-sewa')">&times;</button>
    </div>
</section>

<section class="finance-cards-grid">
    <?php foreach ($dashboardCards as $card): ?>
        <div class="card card-<?= htmlspecialchars($card['class'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="card-body">
                <div class="card-info">
                    <span class="card-title"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="card-value"><?= htmlspecialchars($card['value'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="card-icon"><i class="fas <?= htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8') ?>"></i></div>
            </div>
            <?php if (isset($card['footer'])): ?>
                <div class="card-footer"><?= htmlspecialchars($card['footer'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>

<div class="dashboard-details-layout">
    <section class="details-box statistik-box">
        <h2>Statistik Partisipasi Warga</h2>
        <p class="subtitle">Pembayaran Iuran Keamanan Periode Aktif</p>
        <div class="progress-container">
            <div class="progress-header">
                <span class="badge">Berlangsung</span>
                <span class="progress-percentage"><?= htmlspecialchars($stats['persen'], ENT_QUOTES, 'UTF-8') ?>%</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: <?= htmlspecialchars($stats['persen'], ENT_QUOTES, 'UTF-8') ?>%;"></div>
            </div>
        </div>
        <div class="progress-stats">
            <span><strong><?= htmlspecialchars($stats['terbayar'], ENT_QUOTES, 'UTF-8') ?> KK</strong> Sudah Bayar</span>
            <span>Total <strong><?= htmlspecialchars($stats['total'], ENT_QUOTES, 'UTF-8') ?> KK</strong> Aktif</span>
        </div>
    </section>

    <section class="details-box transaksi-box">
        <div class="transaksi-header">
            <h2>Transaksi Terkini</h2>
            <button class="btn-filter" id="btnFilter" onclick="toggleFilter()">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis Transaksi</th>
                        <th>Sumber/Tujuan Dana</th>
                        <th>Keterangan</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody id="transaction-rows">
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['tanggal'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="type-badge <?= htmlspecialchars($transaction['label'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($transaction['jenis'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td><?= htmlspecialchars($transaction['sumber'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($transaction['keterangan'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-right <?= htmlspecialchars($transaction['class'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($transaction['jumlah'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php include 'inc/footer.php'; ?>
