<?php
$pageTitle = 'Pemasukan - Sistem Informasi Manajemen Warga';
$activePage = 'pemasukan';
include 'inc/data.php';
include 'inc/header.php';
?>

<header class="dashboard-header">
    <h1>Pemasukan</h1>
    <div class="periode-aktif">
        Kelola catatan pemasukan iuran warga.
    </div>
</header>

<section class="details-box transaksi-box">
    <div class="transaksi-header">
        <h2>Daftar Pemasukan</h2>
        <div class="header-actions">
            <label class="table-search" for="pemasukanSearch">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input type="search" id="pemasukanSearch" placeholder="Cari pemasukan..." aria-label="Cari data pemasukan">
            </label>
            <button class="btn-filter" id="btnTambahPemasukan" onclick="openPemasukanModal()">
                <i class="fas fa-plus"></i> Tambah Pemasukan
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
                    <th>Sumber</th>
                    <th>Keterangan</th>
                    <th class="text-right">Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="pemasukanRows">
                <?php
                $no = 1;
                foreach ($transactions as $t) {
                    if (strtolower($t['jenis']) !== 'pemasukan') continue;
                    echo '<tr data-id="' . htmlspecialchars($t['id'] ?? '', ENT_QUOTES, 'UTF-8') . '">';
                    echo '<td>' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($t['tanggal'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($t['kategori'] ?? $t['jenis'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($t['sumber'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($t['keterangan'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td class="text-right ' . htmlspecialchars($t['class'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($t['jumlah'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>';
                    echo '<div class="action-buttons">';
                    echo '<button class="btn-filter btn-icon" type="button" onclick="editPemasukan(this)" title="Edit" aria-label="Edit"><i class="fas fa-pen"></i></button>';
                    echo '<button class="btn-filter btn-icon btn-danger" type="button" onclick="deletePemasukan(this)" title="Hapus" aria-label="Hapus"><i class="fas fa-trash"></i></button>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="table-pagination" id="pemasukanPagination" aria-label="Navigasi halaman data pemasukan"></div>
</section>

<!-- Modal Tambah Pemasukan -->
<div class="modal-overlay" id="pemasukanModal" onclick="closePemasukanModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 id="pemasukanModalTitle">Tambah Pemasukan</h2>
            <button class="close-btn" type="button" onclick="closePemasukanModal()">&times;</button>
        </div>
        <form id="pemasukanForm" onsubmit="savePemasukan(event)">
            <input type="hidden" id="pmRowIndex" value="-1">
            <label>
                Tanggal
                <input type="date" id="pmTanggal" required>
            </label>
            <label>
                Jenis
                <select id="pmJenis" onchange="updateSumberAutocomplete()" required>
                    <?php foreach ($jenisPemasukan as $item): ?>
                        <option value="<?= htmlspecialchars($item['jenis'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['jenis'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Sumber
                <input type="text" id="pmSumber" required>
                <datalist id="kkOptions">
                    <?php foreach ($wargaList as $warga): ?>
                        <option value="<?= htmlspecialchars($warga['nama'], ENT_QUOTES, 'UTF-8') ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </label>
            <label>
                Keterangan
                <input type="text" id="pmKeterangan" required>
            </label>
            <label>
                Jumlah
                <input type="text" id="pmJumlah" placeholder="+ Rp 50.000" required>
            </label>
            <div class="modal-actions">
                <button type="button" class="btn-filter" onclick="closePemasukanModal()">Batal</button>
                <button type="submit" class="btn-filter">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
