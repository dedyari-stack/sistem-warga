<?php
$pageTitle = 'Data Aset - Sistem Informasi Manajemen Warga';
$activePage = 'data-aset';
include 'inc/data.php';
include 'inc/header.php';
?>

<header class="dashboard-header">
    <h1>Data Aset</h1>
    <div class="periode-aktif">
        Kelola data aset Paguyuban termasuk kondisi dan lokasi aset.
    </div>
</header>

<section class="details-box data-warga-box">
    <div class="transaksi-header">
        <h2>Daftar Aset</h2>
        <button id="btnTambahAset" class="btn-filter" onclick="openAsetModal()">
            <i class="fas fa-plus"></i> Tambah Aset
        </button>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Aset</th>
                    <th>Lokasi</th>
                    <th>Baik</th>
                    <th>Rusak</th>
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
                        <td><?= htmlspecialchars($aset['baik'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($aset['rusak'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($aset['keterangan'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="type-badge <?= $aset['status'] === 'Aktif' ? 'badge-pemasukan' : 'badge-pengeluaran' ?>"><?= htmlspecialchars($aset['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="modal-overlay" id="asetModal" onclick="closeAsetModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 id="asetModalTitle">Tambah Aset</h2>
            <button class="close-btn" type="button" onclick="closeAsetModal()">&times;</button>
        </div>
        <form id="asetForm" onsubmit="saveAset(event)">
            <label>
                Nama Aset
                <input type="text" id="asetNama" required>
            </label>
            <label>
                Lokasi
                <input type="text" id="asetLokasi" required>
            </label>
            <label>
                Jumlah Baik
                <input type="number" id="asetBaik" min="0" value="1" required>
            </label>
            <label>
                Jumlah Rusak
                <input type="number" id="asetRusak" min="0" value="0" required>
            </label>
            <label>
                Keterangan
                <input type="text" id="asetKeterangan" required>
            </label>
            <label>
                Status
                <select id="asetStatus">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </label>
            <div class="modal-actions">
                <button type="button" class="btn-filter" onclick="closeAsetModal()">Batal</button>
                <button type="submit" class="btn-filter">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
