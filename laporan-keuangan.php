<?php
$pageTitle = 'Laporan Keuangan - Sistem Informasi Manajemen Warga';
$activePage = 'laporan-keuangan';
include 'inc/data.php';
include 'inc/header.php';

function rupiahToNumber($value) {
    $number = preg_replace('/[^0-9-]/', '', $value);
    return $number === '' ? 0 : (int) $number;
}

function formatRupiah($value) {
    return 'Rp ' . number_format($value, 0, ',', '.');
}

function formatLaporanTanggal(DateTimeImmutable $date) {
    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    return $date->format('j') . ' ' . $bulan[(int) $date->format('n')] . ' ' . $date->format('Y');
}

function parseLaporanTanggal($value) {
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return DateTimeImmutable::createFromFormat('!Y-m-d', $value) ?: null;
    }

    $bulan = [
        'januari' => 'January',
        'jan' => 'January',
        'februari' => 'February',
        'feb' => 'February',
        'maret' => 'March',
        'mar' => 'March',
        'april' => 'April',
        'apr' => 'April',
        'mei' => 'May',
        'juni' => 'June',
        'jun' => 'June',
        'juli' => 'July',
        'jul' => 'July',
        'agustus' => 'August',
        'agu' => 'August',
        'september' => 'September',
        'sep' => 'September',
        'oktober' => 'October',
        'okt' => 'October',
        'november' => 'November',
        'nov' => 'November',
        'desember' => 'December',
        'des' => 'December',
    ];

    $normalized = strtolower($value);
    foreach ($bulan as $indonesia => $english) {
        $normalized = preg_replace('/\b' . preg_quote($indonesia, '/') . '\b/u', $english, $normalized);
    }

    $timestamp = strtotime($normalized);
    return $timestamp ? (new DateTimeImmutable())->setTimestamp($timestamp)->setTime(0, 0) : null;
}

function getLaporanPeriodStart(DateTimeImmutable $date) {
    $monthStart = $date->modify('first day of this month')->setTime(0, 0);
    if ((int) $date->format('j') >= 17) {
        return $monthStart->setDate((int) $date->format('Y'), (int) $date->format('n'), 17);
    }

    $previousMonth = $monthStart->modify('-1 month');
    return $previousMonth->setDate((int) $previousMonth->format('Y'), (int) $previousMonth->format('n'), 17);
}

function getLaporanPeriodBounds($periodKey) {
    if (!preg_match('/^\d{4}-\d{2}$/', $periodKey)) {
        $periodKey = getLaporanPeriodStart(new DateTimeImmutable('today'))->format('Y-m');
    }

    $start = DateTimeImmutable::createFromFormat('!Y-m-d', $periodKey . '-17');
    if (!$start) {
        $start = getLaporanPeriodStart(new DateTimeImmutable('today'));
    }

    $nextMonth = $start->modify('+1 month');
    $end = $nextMonth->setDate((int) $nextMonth->format('Y'), (int) $nextMonth->format('n'), 16);

    return [$start, $end];
}

function formatLaporanPeriodLabel($periodKey) {
    [$start, $end] = getLaporanPeriodBounds($periodKey);
    return formatLaporanTanggal($start) . ' - ' . formatLaporanTanggal($end);
}

function buildLaporanPeriodOptions($selectedPeriod, $transactions, $dbAvailable, $pdo) {
    $periods = [];
    $currentStart = getLaporanPeriodStart(new DateTimeImmutable('today'));

    for ($offset = -12; $offset <= 3; $offset++) {
        $periods[$currentStart->modify($offset . ' month')->format('Y-m')] = true;
    }

    foreach ($transactions as $transaction) {
        $tanggal = parseLaporanTanggal($transaction['tanggal'] ?? '');
        if ($tanggal) {
            $periods[getLaporanPeriodStart($tanggal)->format('Y-m')] = true;
        }
    }

    if ($dbAvailable && $pdo) {
        $range = $pdo->query('SELECT MIN(tanggal) AS tanggal_awal, MAX(tanggal) AS tanggal_akhir FROM transaksi')->fetch();
        if (!empty($range['tanggal_awal']) && !empty($range['tanggal_akhir'])) {
            $start = getLaporanPeriodStart(new DateTimeImmutable($range['tanggal_awal']));
            $end = getLaporanPeriodStart(new DateTimeImmutable($range['tanggal_akhir']));
            while ($start <= $end) {
                $periods[$start->format('Y-m')] = true;
                $start = $start->modify('+1 month');
            }
        }
    }

    $periods[$selectedPeriod] = true;
    $keys = array_keys($periods);
    rsort($keys);

    return $keys;
}

function getCustomLaporanDate($key) {
    $date = parseLaporanTanggal($_GET[$key] ?? '');
    return $date ?: null;
}

$defaultPeriod = getLaporanPeriodStart(new DateTimeImmutable('today'))->format('Y-m');
$selectedPeriod = preg_match('/^\d{4}-\d{2}$/', $_GET['periode'] ?? '') ? $_GET['periode'] : $defaultPeriod;
[$periodeMulai, $periodeSelesai] = getLaporanPeriodBounds($selectedPeriod);
$customMulai = getCustomLaporanDate('tanggal_mulai');
$customSelesai = getCustomLaporanDate('tanggal_selesai');
$isCustomPeriod = $customMulai && $customSelesai;

if ($isCustomPeriod) {
    if ($customMulai > $customSelesai) {
        [$customMulai, $customSelesai] = [$customSelesai, $customMulai];
    }

    $periodeMulai = $customMulai;
    $periodeSelesai = $customSelesai;
}

$periodOptions = buildLaporanPeriodOptions($selectedPeriod, $transactions, $dbAvailable, $pdo);
$periodeLabel = formatLaporanTanggal($periodeMulai) . ' - ' . formatLaporanTanggal($periodeSelesai);
$filteredTransactions = array_values(array_filter($transactions, function ($transaction) use ($periodeMulai, $periodeSelesai) {
    $tanggal = parseLaporanTanggal($transaction['tanggal'] ?? '');
    return $tanggal && $tanggal >= $periodeMulai && $tanggal <= $periodeSelesai;
}));

$totalPemasukan = 0;
$totalPengeluaran = 0;

foreach ($filteredTransactions as $transaction) {
    $amount = abs(rupiahToNumber($transaction['jumlah']));
    if (strtolower($transaction['jenis']) === 'pemasukan') {
        $totalPemasukan += $amount;
    }
    if (strtolower($transaction['jenis']) === 'pengeluaran') {
        $totalPengeluaran += $amount;
    }
}

$saldoAkhir = $totalPemasukan - $totalPengeluaran;
?>

<header class="dashboard-header">
    <h1>Laporan Keuangan</h1>
    <div class="periode-aktif">
        Ringkasan pemasukan, pengeluaran, dan saldo kas warga.
    </div>
</header>

<section class="finance-cards-grid">
    <div class="card card-pemasukan">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Total Pemasukan</span>
                <span class="card-value"><?= htmlspecialchars(formatRupiah($totalPemasukan), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="card-icon">
                <i class="fas fa-arrow-down"></i>
            </div>
        </div>
    </div>
    <div class="card card-pengeluaran">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Total Pengeluaran</span>
                <span class="card-value"><?= htmlspecialchars(formatRupiah($totalPengeluaran), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="card-icon">
                <i class="fas fa-arrow-up"></i>
            </div>
        </div>
    </div>
    <div class="card card-saldo">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Saldo Akhir</span>
                <span class="card-value"><?= htmlspecialchars(formatRupiah($saldoAkhir), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="card-icon">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>
</section>

<section class="details-box transaksi-box">
    <div class="transaksi-header">
        <h2>Rincian Transaksi</h2>
        <div class="report-actions">
            <button class="btn-filter" type="button" onclick="generateLaporanPdf('Laporan Keuangan')">
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
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Sumber/Tujuan</th>
                    <th>Keterangan</th>
                    <th class="text-right">Pemasukan</th>
                    <th class="text-right">Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $index => $transaction): ?>
                    <?php
                    $isPemasukan = strtolower($transaction['jenis']) === 'pemasukan';
                    $amount = formatRupiah(abs(rupiahToNumber($transaction['jumlah'])));
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($transaction['tanggal'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="type-badge <?= $isPemasukan ? 'badge-pemasukan' : 'badge-pengeluaran' ?>">
                                <?= htmlspecialchars($transaction['jenis'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($transaction['sumber'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($transaction['keterangan'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-right text-success"><?= $isPemasukan ? htmlspecialchars($amount, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                        <td class="text-right text-danger"><?= $isPemasukan ? '-' : htmlspecialchars($amount, ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">Total</th>
                    <th class="text-right text-success"><?= htmlspecialchars(formatRupiah($totalPemasukan), ENT_QUOTES, 'UTF-8') ?></th>
                    <th class="text-right text-danger"><?= htmlspecialchars(formatRupiah($totalPengeluaran), ENT_QUOTES, 'UTF-8') ?></th>
                </tr>
                <tr>
                    <th colspan="6" class="text-right">Saldo Akhir</th>
                    <th class="text-right"><?= htmlspecialchars(formatRupiah($saldoAkhir), ENT_QUOTES, 'UTF-8') ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<?php include 'inc/footer.php'; ?>
