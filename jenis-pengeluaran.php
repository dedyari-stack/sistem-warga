<?php
$pageTitle = 'Jenis Pengeluaran - Sistem Informasi Manajemen Warga';
$activePage = 'jenis-pengeluaran';
include 'inc/data.php';
include 'inc/header.php';
?>

<header class="dashboard-header">
    <h1>Jenis Pengeluaran</h1>
    <div class="periode-aktif">
        Kelola kategori pengeluaran yang digunakan dalam pencatatan keuangan RT/RW.
    </div>
</header>

<section class="details-box data-warga-box">
    <div class="transaksi-header">
        <h2>Daftar Jenis Pengeluaran</h2>
        <button id="btnTambahJenisPengeluaran" class="btn-filter" type="button">Tambah Jenis</button>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Pengeluaran</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jenisPengeluaran as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($item['jenis'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item['deskripsi'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="modal-overlay" id="jenisPengeluaranModal" onclick="closeJenisPengeluaranModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2>Tambah Jenis Pengeluaran</h2>
            <button class="close-btn" type="button" onclick="closeJenisPengeluaranModal()">&times;</button>
        </div>
        <form id="jenisPengeluaranForm" onsubmit="saveJenisPengeluaran(event)">
            <label>
                Nama Jenis Pengeluaran
                <input type="text" id="jenisPengeluaranNama" required>
            </label>
            <label>
                Deskripsi
                <input type="text" id="jenisPengeluaranDeskripsi" required>
            </label>
            <div class="modal-actions">
                <button type="button" class="btn-filter" onclick="closeJenisPengeluaranModal()">Batal</button>
                <button type="submit" class="btn-filter">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
