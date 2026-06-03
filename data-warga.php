<?php
$pageTitle = 'Data Warga (KK) - Sistem Informasi Manajemen Warga';
$activePage = 'data-warga';
include 'inc/data.php';
include 'inc/header.php';
?>

<header class="dashboard-header">
    <h1>Data Warga (KK)</h1>
    <div class="periode-aktif">
        Lihat daftar lengkap data warga dan status iuran.
    </div>
</header>

<section class="details-box data-warga-box">
    <div class="transaksi-header">
        <h2>Data Warga (KK)</h2>
        <div class="header-actions">
            <label class="table-search" for="wargaSearch">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input type="search" id="wargaSearch" placeholder="Cari data warga..." aria-label="Cari data warga">
            </label>
            <button class="btn-filter" id="btnTambahData" onclick="addWarga()">
                <i class="fas fa-plus"></i> Tambah Data
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kepala Keluarga</th>
                    <th>Alamat</th>
                    <th>No. HP Aktif</th>
                    <th>Jumlah Anggota Keluarga</th>
                    <th>Status Warga</th>
                    <th>Peran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($wargaList as $index => $warga): ?>
                    <tr data-id="<?= htmlspecialchars($warga['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($warga['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($warga['alamat'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($warga['hp'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($warga['jumlah_anggota'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="type-badge <?= badgeClass($warga['status']) ?>"><?= htmlspecialchars($warga['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td>
                            <div class="role-badges">
                                <span class="type-badge badge-neutral"><?= htmlspecialchars($warga['peran_1'] ?? 'Warga', ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if (!empty($warga['peran_2'])): ?>
                                    <span class="type-badge badge-neutral"><?= htmlspecialchars($warga['peran_2'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-filter btn-icon" type="button" onclick="editWarga(this)" title="Edit" aria-label="Edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn-filter btn-icon btn-danger" type="button" onclick="deleteWarga(this)" title="Hapus" aria-label="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="table-pagination" id="wargaPagination" aria-label="Navigasi halaman data warga"></div>
</section>

<div class="modal-overlay" id="editModalOverlay" onclick="closeEditModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 id="editModalTitle">Edit Data Warga</h2>
            <button class="close-btn" type="button" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editWargaForm" onsubmit="saveWarga(event)">
            <input type="hidden" id="editRowIndex" value="-1">
            <label>
                Nama Kepala Keluarga
                <input type="text" id="editNama" required>
            </label>
            <label>
                Alamat
                <input type="text" id="editAlamat" required>
            </label>
            <label>
                No. HP Aktif
                <input type="text" id="editHp" required>
            </label>
            <label>
                Jumlah Anggota Keluarga
                <input type="number" id="editJumlahAnggota" min="1" value="1" required>
            </label>
            <label>
                Status Warga
                <select id="editStatus">
                    <option>Domisili</option>
                    <option>Kontrak</option>
                    <option>Aset</option>
                </select>
            </label>
            <label>
                Peran Utama
                <select id="editPeran1" required>
                    <option>Warga</option>
                    <option>Pengurus</option>
                    <option>Petugas Input</option>
                    <option>Admin</option>
                </select>
            </label>
            <label>
                Peran Tambahan
                <select id="editPeran2">
                    <option value="">Tidak ada</option>
                    <option>Warga</option>
                    <option>Pengurus</option>
                    <option>Petugas Input</option>
                    <option>Admin</option>
                </select>
            </label>
            <div class="modal-actions">
                <button type="button" class="btn-filter" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-filter">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
