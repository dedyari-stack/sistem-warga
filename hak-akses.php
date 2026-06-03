<?php
$pageTitle = 'Hak Akses - Sistem Informasi Manajemen Warga';
$activePage = 'hak-akses';
include 'inc/data.php';
include 'inc/header.php';
?>

<header class="dashboard-header">
    <h1>Hak Akses</h1>
    <div class="periode-aktif">
        Kelola pengguna dan peran akses sistem warga.
    </div>
</header>

<section class="details-box data-warga-box">
    <div class="transaksi-header">
        <h2>Daftar Hak Akses</h2>
        <button id="btnTambahHakAkses" class="btn-filter" type="button" onclick="openHakAksesModal()">
            <i class="fas fa-plus"></i> Tambah Hak Akses
        </button>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pengguna</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="hakAksesRows">
                <?php foreach ($accessList as $index => $akses): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($akses['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($akses['username'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($akses['role'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="type-badge <?= $akses['status'] === 'Aktif' ? 'badge-pemasukan' : 'badge-pengeluaran' ?>"><?= htmlspecialchars($akses['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="modal-overlay" id="hakAksesModal" onclick="closeHakAksesModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 id="hakAksesModalTitle">Tambah Hak Akses</h2>
            <button class="close-btn" type="button" onclick="closeHakAksesModal()">&times;</button>
        </div>
        <form id="hakAksesForm" onsubmit="saveHakAkses(event)">
            <label>
                Nama Pengguna
                <input type="text" id="hakAksesNama" required>
            </label>
            <label>
                Username
                <input type="text" id="hakAksesUsername" required>
            </label>
            <label>
                Password
                <input type="password" id="hakAksesPassword" required>
            </label>
            <label>
                Role
                <select id="hakAksesRole" required>
                    <option value="Administrator">Administrator</option>
                    <option value="Ketua RT">Ketua RT</option>
                    <option value="Bendahara">Bendahara</option>
                    <option value="Petugas">Petugas</option>
                </select>
            </label>
            <label>
                Status
                <select id="hakAksesStatus" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </label>
            <div class="modal-actions">
                <button type="button" class="btn-filter" onclick="closeHakAksesModal()">Batal</button>
                <button type="submit" class="btn-filter">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
