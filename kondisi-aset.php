<?php
$pageTitle = 'Kondisi Aset - Sistem Informasi Manajemen Warga';
$activePage = 'kondisi-aset';
include 'inc/data.php';
include 'inc/header.php';
?>

<header class="dashboard-header">
    <h1>Kondisi Aset</h1>
    <div class="periode-aktif">
        Pantau hasil pemeriksaan, catatan perawatan, dan status tindak lanjut aset warga.
    </div>
</header>

<section class="details-box data-warga-box">
    <div class="transaksi-header">
        <h2>Daftar Kondisi Aset</h2>
        <button id="btnTambahKondisiAset" class="btn-filter" type="button">
            <i class="fas fa-plus"></i> Tambah Kondisi
        </button>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Aset</th>
                    <th>Lokasi</th>
                    <th>Tanggal Cek</th>
                    <th>Kondisi</th>
                    <th>Petugas</th>
                    <th>Catatan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="kondisiAsetRows">
                <?php foreach ($kondisiList as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['aset'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['lokasi'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['tanggal'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="type-badge <?= $row['kondisi'] === 'Baik' ? 'badge-pemasukan' : 'badge-pengeluaran' ?>"><?= htmlspecialchars($row['kondisi'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars($row['petugas'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['catatan'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="type-badge <?= $row['status'] === 'Selesai' ? 'badge-pemasukan' : 'badge-pengeluaran' ?>"><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-filter btn-icon" type="button" onclick="editKondisiAset(this)" title="Edit" aria-label="Edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn-filter btn-icon btn-danger" type="button" onclick="deleteKondisiAset(this)" title="Hapus" aria-label="Hapus">
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

<div class="modal-overlay" id="kondisiAsetModal" onclick="closeKondisiAsetModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 id="kondisiAsetModalTitle">Tambah Kondisi Aset</h2>
            <button class="close-btn" type="button" onclick="closeKondisiAsetModal()">&times;</button>
        </div>
        <form id="kondisiAsetForm" onsubmit="saveKondisiAset(event)">
            <input type="hidden" id="kondisiAsetRowIndex" value="-1">
            <label>
                Nama Aset
                <input type="text" id="kondisiAsetNama" required>
            </label>
            <label>
                Lokasi
                <input type="text" id="kondisiAsetLokasi" required>
            </label>
            <label>
                Tanggal Cek
                <input type="date" id="kondisiAsetTanggal" required>
            </label>
            <label>
                Kondisi
                <select id="kondisiAsetKondisi" required>
                    <option value="Baik">Baik</option>
                    <option value="Perlu Perawatan">Perlu Perawatan</option>
                    <option value="Rusak">Rusak</option>
                </select>
            </label>
            <label>
                Petugas
                <input type="text" id="kondisiAsetPetugas" required>
            </label>
            <label>
                Catatan
                <textarea id="kondisiAsetCatatan" rows="3" required></textarea>
            </label>
            <label>
                Status
                <select id="kondisiAsetStatus" required>
                    <option value="Dipantau">Dipantau</option>
                    <option value="Perbaikan">Perbaikan</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </label>
            <div class="modal-actions">
                <button type="button" class="btn-filter" onclick="closeKondisiAsetModal()">Batal</button>
                <button type="submit" class="btn-filter">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
