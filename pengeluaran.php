<?php
$pageTitle = 'Pengeluaran - Sistem Informasi Manajemen Warga';
$activePage = 'pengeluaran';
include 'inc/data.php';
include 'inc/header.php';
?>

<header class="dashboard-header">
    <h1>Pengeluaran</h1>
    <div class="periode-aktif">
        Kelola catatan pengeluaran kas RT/RW.
    </div>
</header>

<section class="details-box transaksi-box">
    <div class="transaksi-header">
        <h2>Daftar Pengeluaran</h2>
        <div class="header-actions">
            <label class="table-search" for="pengeluaranSearch">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input type="search" id="pengeluaranSearch" placeholder="Cari pengeluaran..." aria-label="Cari data pengeluaran">
            </label>
            <button class="btn-filter" id="btnTambahPengeluaran" onclick="openPengeluaranModal()">
                <i class="fas fa-plus"></i> Tambah Pengeluaran
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
                    <th>Tujuan</th>
                    <th>Keterangan</th>
                    <th class="text-right">Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="pengeluaranRows">
                <?php
                $no = 1;
                foreach ($transactions as $t) {
                    if (strtolower($t['jenis']) !== 'pengeluaran') continue;
                    echo '<tr data-id="' . htmlspecialchars($t['id'] ?? '', ENT_QUOTES, 'UTF-8') . '">';
                    echo '<td>' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($t['tanggal'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($t['kategori'] ?? $t['jenis'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($t['sumber'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($t['keterangan'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td class="text-right text-danger">' . htmlspecialchars($t['jumlah'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>';
                    echo '<div class="action-buttons">';
                    echo '<button class="btn-filter btn-icon" type="button" onclick="editPengeluaran(this)" title="Edit" aria-label="Edit"><i class="fas fa-pen"></i></button>';
                    echo '<button class="btn-filter btn-icon btn-danger" type="button" onclick="deletePengeluaran(this)" title="Hapus" aria-label="Hapus"><i class="fas fa-trash"></i></button>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="table-pagination" id="pengeluaranPagination" aria-label="Navigasi halaman data pengeluaran"></div>
</section>

<div class="modal-overlay" id="pengeluaranModal" onclick="closePengeluaranModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 id="pengeluaranModalTitle">Tambah Pengeluaran</h2>
            <button class="close-btn" type="button" onclick="closePengeluaranModal()">&times;</button>
        </div>
        <form id="pengeluaranForm" onsubmit="savePengeluaran(event)">
            <input type="hidden" id="pgRowIndex" value="-1">
            <label>
                Tanggal
                <input type="date" id="pgTanggal" required>
            </label>
            <label>
                Jenis
                <select id="pgJenis" required>
                    <?php foreach ($jenisPengeluaran as $item): ?>
                        <option value="<?= htmlspecialchars($item['jenis'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['jenis'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Tujuan
                <input type="text" id="pgTujuan" required>
            </label>
            <label>
                Keterangan
                <input type="text" id="pgKeterangan" required>
            </label>
            <label>
                Jumlah
                <input type="text" id="pgJumlah" placeholder="- Rp 1.000.000" required>
            </label>
            <div class="modal-actions">
                <button type="button" class="btn-filter" onclick="closePengeluaranModal()">Batal</button>
                <button type="submit" class="btn-filter">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
