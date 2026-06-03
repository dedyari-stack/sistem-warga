<?php
$pageTitle = 'Laporan Rekap Keuangan - Sistem Informasi Manajemen Warga';
$activePage = 'laporan-rekap-keuangan';
include 'inc/data.php';
include 'inc/header.php';

function formatRekapRupiah($value) {
    return 'Rp ' . number_format((int) $value, 0, ',', '.');
}

function formatRekapTanggal(DateTimeImmutable $date) {
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

function parseRekapTanggal($value) {
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

function getRekapPeriodStart(DateTimeImmutable $date) {
    $monthStart = $date->modify('first day of this month')->setTime(0, 0);
    if ((int) $date->format('j') >= 17) {
        return $monthStart->setDate((int) $date->format('Y'), (int) $date->format('n'), 17);
    }

    $previousMonth = $monthStart->modify('-1 month');
    return $previousMonth->setDate((int) $previousMonth->format('Y'), (int) $previousMonth->format('n'), 17);
}

function getRekapPeriodBounds($periodKey) {
    if (!preg_match('/^\d{4}-\d{2}$/', $periodKey)) {
        $periodKey = getRekapPeriodStart(new DateTimeImmutable('today'))->format('Y-m');
    }

    $start = DateTimeImmutable::createFromFormat('!Y-m-d', $periodKey . '-17');
    if (!$start) {
        $start = getRekapPeriodStart(new DateTimeImmutable('today'));
    }

    $nextMonth = $start->modify('+1 month');
    $end = $nextMonth->setDate((int) $nextMonth->format('Y'), (int) $nextMonth->format('n'), 16);

    return [$start, $end];
}

function buildRekapPeriodOptions($selectedPeriod, $transactions, $dbAvailable, $pdo) {
    $periods = [];
    $currentStart = getRekapPeriodStart(new DateTimeImmutable('today'));

    for ($offset = -12; $offset <= 3; $offset++) {
        $periods[$currentStart->modify($offset . ' month')->format('Y-m')] = true;
    }

    foreach ($transactions as $transaction) {
        $tanggal = parseRekapTanggal($transaction['tanggal'] ?? '');
        if ($tanggal) {
            $periods[getRekapPeriodStart($tanggal)->format('Y-m')] = true;
        }
    }

    if ($dbAvailable && $pdo) {
        $range = $pdo->query('SELECT MIN(tanggal) AS tanggal_awal, MAX(tanggal) AS tanggal_akhir FROM transaksi')->fetch();
        if (!empty($range['tanggal_awal']) && !empty($range['tanggal_akhir'])) {
            $start = getRekapPeriodStart(new DateTimeImmutable($range['tanggal_awal']));
            $end = getRekapPeriodStart(new DateTimeImmutable($range['tanggal_akhir']));
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

function formatRekapPeriodLabel($periodKey) {
    [$start, $end] = getRekapPeriodBounds($periodKey);
    return formatRekapTanggal($start) . ' - ' . formatRekapTanggal($end);
}

function getCustomRekapDate($key) {
    $date = parseRekapTanggal($_GET[$key] ?? '');
    return $date ?: null;
}

function buildFallbackRekap($jenisList, $tipe, $transactions, DateTimeImmutable $periodeMulai, DateTimeImmutable $periodeSelesai) {
    $rekap = [];

    foreach ($jenisList as $item) {
        $nama = $item['jenis'] ?? '';
        if ($nama === '') {
            continue;
        }

        $rekap[$nama] = [
            'jenis' => $nama,
            'deskripsi' => $item['deskripsi'] ?? '-',
            'jumlah_transaksi' => 0,
            'total' => 0,
        ];
    }

    foreach ($transactions as $transaction) {
        if (strtolower($transaction['jenis'] ?? '') !== strtolower($tipe)) {
            continue;
        }

        $tanggal = parseRekapTanggal($transaction['tanggal'] ?? '');
        if (!$tanggal || $tanggal < $periodeMulai || $tanggal > $periodeSelesai) {
            continue;
        }

        $nama = $transaction['kategori'] ?? $tipe;
        if (!isset($rekap[$nama])) {
            $rekap[$nama] = [
                'jenis' => $nama,
                'deskripsi' => '-',
                'jumlah_transaksi' => 0,
                'total' => 0,
            ];
        }

        $rekap[$nama]['jumlah_transaksi']++;
        $rekap[$nama]['total'] += (int) preg_replace('/[^0-9]/', '', $transaction['jumlah'] ?? '0');
    }

    return array_values($rekap);
}

$defaultPeriod = getRekapPeriodStart(new DateTimeImmutable('today'))->format('Y-m');
$selectedPeriod = preg_match('/^\d{4}-\d{2}$/', $_GET['periode'] ?? '') ? $_GET['periode'] : $defaultPeriod;
[$periodeMulai, $periodeSelesai] = getRekapPeriodBounds($selectedPeriod);
$customMulai = getCustomRekapDate('tanggal_mulai');
$customSelesai = getCustomRekapDate('tanggal_selesai');
$isCustomPeriod = $customMulai && $customSelesai;

if ($isCustomPeriod) {
    if ($customMulai > $customSelesai) {
        [$customMulai, $customSelesai] = [$customSelesai, $customMulai];
    }

    $periodeMulai = $customMulai;
    $periodeSelesai = $customSelesai;
}

$periodOptions = buildRekapPeriodOptions($selectedPeriod, $transactions, $dbAvailable, $pdo);
$periodeLabel = formatRekapTanggal($periodeMulai) . ' - ' . formatRekapTanggal($periodeSelesai);

$rekapPemasukan = buildFallbackRekap($jenisPemasukan, 'Pemasukan', $transactions, $periodeMulai, $periodeSelesai);
$rekapPengeluaran = buildFallbackRekap($jenisPengeluaran, 'Pengeluaran', $transactions, $periodeMulai, $periodeSelesai);

if ($dbAvailable && $pdo) {
    $rekapSql = '
        SELECT
            jt.nama AS jenis,
            COALESCE(jt.deskripsi, "-") AS deskripsi,
            COUNT(t.id) AS jumlah_transaksi,
            COALESCE(SUM(t.jumlah), 0) AS total
        FROM jenis_transaksi jt
        LEFT JOIN transaksi t
            ON t.jenis_transaksi_id = jt.id
            AND t.tipe = jt.tipe
            AND t.tanggal BETWEEN :periode_mulai AND :periode_selesai
        WHERE jt.tipe = :tipe
        GROUP BY jt.id, jt.nama, jt.deskripsi
        ORDER BY jt.id ASC
    ';

    $stmt = $pdo->prepare($rekapSql);
    $periodParams = [
        'periode_mulai' => $periodeMulai->format('Y-m-d'),
        'periode_selesai' => $periodeSelesai->format('Y-m-d'),
    ];

    $stmt->execute($periodParams + ['tipe' => 'Pemasukan']);
    $rekapPemasukan = $stmt->fetchAll();

    $stmt->execute($periodParams + ['tipe' => 'Pengeluaran']);
    $rekapPengeluaran = $stmt->fetchAll();
}

$totalPemasukan = array_sum(array_column($rekapPemasukan, 'total'));
$totalPengeluaran = array_sum(array_column($rekapPengeluaran, 'total'));
$saldoAkhir = $totalPemasukan - $totalPengeluaran;
$jumlahJenisPemasukan = count($rekapPemasukan);
$jumlahJenisPengeluaran = count($rekapPengeluaran);
$totalTransaksiPemasukan = array_sum(array_column($rekapPemasukan, 'jumlah_transaksi'));
$totalTransaksiPengeluaran = array_sum(array_column($rekapPengeluaran, 'jumlah_transaksi'));
$jumlahBarisRekap = max($jumlahJenisPemasukan, $jumlahJenisPengeluaran);
?>

<header class="dashboard-header">
    <h1>Laporan Rekap Keuangan</h1>
    <div class="periode-aktif">
        Periode <?= htmlspecialchars($periodeLabel, ENT_QUOTES, 'UTF-8') ?>
    </div>
</header>

<form class="report-filter-bar" method="get" action="laporan-rekap-keuangan.php">
    <label for="periode">
        Periode Cepat
        <select id="periode" name="periode" onchange="document.getElementById('tanggal_mulai').value = ''; document.getElementById('tanggal_selesai').value = '';">
            <?php foreach ($periodOptions as $period): ?>
                <option value="<?= htmlspecialchars($period, ENT_QUOTES, 'UTF-8') ?>" <?= $period === $selectedPeriod ? 'selected' : '' ?>>
                    <?= htmlspecialchars(formatRekapPeriodLabel($period), ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label for="tanggal_mulai">
        Tanggal Mulai
        <input
            type="date"
            id="tanggal_mulai"
            name="tanggal_mulai"
            value="<?= $isCustomPeriod ? htmlspecialchars($periodeMulai->format('Y-m-d'), ENT_QUOTES, 'UTF-8') : '' ?>"
        >
    </label>
    <label for="tanggal_selesai">
        Tanggal Selesai
        <input
            type="date"
            id="tanggal_selesai"
            name="tanggal_selesai"
            value="<?= $isCustomPeriod ? htmlspecialchars($periodeSelesai->format('Y-m-d'), ENT_QUOTES, 'UTF-8') : '' ?>"
        >
    </label>
    <button class="btn-filter" type="submit">
        <i class="fas fa-filter"></i> Terapkan
    </button>
    <?php if ($isCustomPeriod): ?>
        <a class="btn-filter" href="laporan-rekap-keuangan.php?periode=<?= htmlspecialchars($selectedPeriod, ENT_QUOTES, 'UTF-8') ?>">
            <i class="fas fa-calendar"></i> Kembali ke Periode Cepat
        </a>
    <?php endif; ?>
</form>

<section class="finance-cards-grid">
    <div class="card card-pemasukan">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Total Pemasukan</span>
                <span class="card-value"><?= htmlspecialchars(formatRekapRupiah($totalPemasukan), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="card-icon">
                <i class="fas fa-arrow-down"></i>
            </div>
        </div>
        <div class="card-footer"><?= $jumlahJenisPemasukan ?> jenis pemasukan</div>
    </div>
    <div class="card card-pengeluaran">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Total Pengeluaran</span>
                <span class="card-value"><?= htmlspecialchars(formatRekapRupiah($totalPengeluaran), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="card-icon">
                <i class="fas fa-arrow-up"></i>
            </div>
        </div>
        <div class="card-footer"><?= $jumlahJenisPengeluaran ?> jenis pengeluaran</div>
    </div>
    <div class="card card-saldo">
        <div class="card-body">
            <div class="card-info">
                <span class="card-title">Saldo Akhir</span>
                <span class="card-value"><?= htmlspecialchars(formatRekapRupiah($saldoAkhir), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="card-icon">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>
</section>

<section class="details-box transaksi-box">
    <div class="transaksi-header">
        <h2>Rekap Pemasukan dan Pengeluaran</h2>
        <div class="report-actions">
            <button class="btn-filter" type="button" onclick="generateLaporanPdf('Laporan Rekap Keuangan')">
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
                    <th>Jenis Pemasukan</th>
                    <th class="text-right">Jumlah Transaksi</th>
                    <th class="text-right">Total Pemasukan</th>
                    <th>Jenis Pengeluaran</th>
                    <th class="text-right">Jumlah Transaksi</th>
                    <th class="text-right">Total Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($index = 0; $index < $jumlahBarisRekap; $index++): ?>
                    <?php
                    $pemasukan = $rekapPemasukan[$index] ?? null;
                    $pengeluaran = $rekapPengeluaran[$index] ?? null;
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $pemasukan ? htmlspecialchars($pemasukan['jenis'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                        <td class="text-right"><?= $pemasukan ? htmlspecialchars($pemasukan['jumlah_transaksi'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                        <td class="text-right text-success"><?= $pemasukan ? htmlspecialchars(formatRekapRupiah($pemasukan['total']), ENT_QUOTES, 'UTF-8') : '-' ?></td>
                        <td><?= $pengeluaran ? htmlspecialchars($pengeluaran['jenis'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                        <td class="text-right"><?= $pengeluaran ? htmlspecialchars($pengeluaran['jumlah_transaksi'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                        <td class="text-right text-danger"><?= $pengeluaran ? htmlspecialchars(formatRekapRupiah($pengeluaran['total']), ENT_QUOTES, 'UTF-8') : '-' ?></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-right">Total Pemasukan</th>
                    <th class="text-right"><?= htmlspecialchars($totalTransaksiPemasukan, ENT_QUOTES, 'UTF-8') ?></th>
                    <th class="text-right text-success"><?= htmlspecialchars(formatRekapRupiah($totalPemasukan), ENT_QUOTES, 'UTF-8') ?></th>
                    <th class="text-right">Total Pengeluaran</th>
                    <th class="text-right"><?= htmlspecialchars($totalTransaksiPengeluaran, ENT_QUOTES, 'UTF-8') ?></th>
                    <th class="text-right text-danger"><?= htmlspecialchars(formatRekapRupiah($totalPengeluaran), ENT_QUOTES, 'UTF-8') ?></th>
                </tr>
                <tr>
                    <th colspan="6" class="text-right">Saldo Akhir</th>
                    <th class="text-right"><?= htmlspecialchars(formatRekapRupiah($saldoAkhir), ENT_QUOTES, 'UTF-8') ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<?php include 'inc/footer.php'; ?>
