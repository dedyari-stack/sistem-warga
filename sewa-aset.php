<?php
$pageTitle = 'Sewa Aset - Sistem Informasi Manajemen Warga';
$activePage = 'sewa-aset';
include 'inc/data.php';
include 'inc/header.php';
?>

<header class="dashboard-header">
    <h1>Sewa Aset</h1>
    <div class="periode-aktif">
        Kelola peminjaman/sewa aset warga, catat penyewa dan periode sewa.
    </div>
</header>

<section class="details-box data-warga-box">
    <div class="transaksi-header">
        <h2>Daftar Sewa Aset</h2>
        <button id="btnTambahSewa" class="btn-filter" type="button" onclick="openSewaModal()">Tambah Sewa</button>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Aset</th>
                    <th>Penyewa</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Biaya</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sewaList as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['aset'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['penyewa'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['mulai'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['selesai'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-right"><?= htmlspecialchars($row['biaya'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="type-badge <?= $row['status'] === 'Aktif' ? 'badge-pemasukan' : 'badge-pengeluaran' ?>"><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="modal-overlay" id="sewaModal" onclick="closeSewaModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2>Tambah Sewa Aset</h2>
            <button class="close-btn" type="button" onclick="closeSewaModal()">&times;</button>
        </div>
        <form id="sewaForm" onsubmit="saveSewa(event)">
            <label>
                Aset
                <input type="text" id="sewaAset" required>
            </label>
            <label>
                Penyewa
                <input type="text" id="sewaPenyewa" required>
            </label>
            <label>
                Tanggal Mulai
                <input type="date" id="sewaMulai" required>
            </label>
            <label>
                Tanggal Selesai
                <input type="date" id="sewaSelesai" required>
            </label>
            <label>
                Biaya
                <input type="text" id="sewaBiaya" required>
            </label>
            <label>
                Status
                <select id="sewaStatus">
                    <option value="Aktif">Aktif</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Dibatalkan">Dibatalkan</option>
                </select>
            </label>
            <div class="modal-actions">
                <button type="button" class="btn-filter" onclick="closeSewaModal()">Batal</button>
                <button type="submit" class="btn-filter">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
