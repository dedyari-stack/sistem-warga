<?php
$pageTitle = 'Jenis Pemasukan - Sistem Informasi Manajemen Warga';
$activePage = 'jenis-pemasukan';
include 'inc/data.php';
include 'inc/header.php';
?>

<header class="dashboard-header">
    <h1>Jenis Pemasukan</h1>
    <div class="periode-aktif">
        Kelola kategori pemasukan yang digunakan dalam laporan dan pencatatan keuangan.
    </div>
</header>

<section class="details-box data-warga-box">
    <div class="transaksi-header">
        <h2>Daftar Jenis Pemasukan</h2>
        <button id="btnTambahJenisPemasukan" class="btn-filter" type="button">Tambah Jenis</button>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Pemasukan</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jenisPemasukan as $index => $item): ?>
                    <tr data-id="<?= htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($item['jenis'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item['deskripsi'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-filter btn-icon" type="button" onclick="editJenisPemasukan(this)" title="Edit" aria-label="Edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn-filter btn-icon btn-danger" type="button" onclick="deleteJenisPemasukan(this)" title="Hapus" aria-label="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="modal-overlay" id="jenisPemasukanModal" onclick="closeJenisPemasukanModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 id="jenisPemasukanModalTitle">Tambah Jenis Pemasukan</h2>
            <button class="close-btn" type="button" onclick="closeJenisPemasukanModal()">&times;</button>
        </div>
        <form id="jenisPemasukanForm" onsubmit="saveJenisPemasukan(event)">
            <input type="hidden" id="jenisPemasukanRowIndex" value="-1">
            <label>
                Nama Jenis Pemasukan
                <input type="text" id="jenisPemasukanNama" required>
            </label>
            <label>
                Deskripsi
                <input type="text" id="jenisPemasukanDeskripsi" required>
            </label>
            <div class="modal-actions">
                <button type="button" class="btn-filter" onclick="closeJenisPemasukanModal()">Batal</button>
                <button type="submit" class="btn-filter">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
