/**
 * Fungsi untuk membuka/menutup sub-menu pada sidebar
 * @param {HTMLElement} element - Elemen header sub-menu yang di-klik
 */
function toggleSubmenu(element) {
    const menuItem = element.parentElement;
    const isOpen = menuItem.classList.contains('open');

    document.querySelectorAll('.menu-item.has-submenu.open').forEach((item) => {
        if (item !== menuItem) {
            item.classList.remove('open');
        }
    });

    if (isOpen) {
        menuItem.classList.remove('open');
        return;
    }

    menuItem.classList.toggle('open');
}

/**
 * Fungsi untuk menampilkan/menyembunyikan sidebar utama pada tampilan mobile
 */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
}

/**
 * Fungsi untuk menyembunyikan notifikasi/peringatan (Modul 3.2.4)
 * @param {string} alertId - ID elemen alert yang ingin ditutup
 */
function dismissAlert(alertId) {
    const alertElement = document.getElementById(alertId);
    if (alertElement) {
        alertElement.style.opacity = '0';
        alertElement.style.transition = 'opacity 0.3s ease';
        
        setTimeout(() => {
            alertElement.style.display = 'none';
        }, 300);
    }
}

/**
 * Fungsi simulasi klik filter pada Tabel Transaksi Terkini (Modul 3.2.3)
 */
function toggleFilter() {
    alert("Fitur Filter Periode Aktif (Bulan & Tahun) dalam tahap pengembangan selanjutnya.");
}

function openPemasukanModal() {
    const modal = document.getElementById('pemasukanModal');
    if (!modal) return;

    document.getElementById('pemasukanForm').reset();
    document.getElementById('pemasukanModalTitle').textContent = 'Tambah Pemasukan';
    document.getElementById('pmRowIndex').value = -1;
    document.getElementById('pmRowIndex').dataset.id = '';
    const jenisField = document.getElementById('pmJenis');
    if (jenisField) {
        jenisField.selectedIndex = 0;
    }
    updateSumberAutocomplete();
    modal.classList.add('open');
}

function closePemasukanModal() {
    const modal = document.getElementById('pemasukanModal');
    if (!modal) return;
    modal.classList.remove('open');
}

function openPengeluaranModal() {
    const modal = document.getElementById('pengeluaranModal');
    if (!modal) return;

    document.getElementById('pengeluaranForm').reset();
    document.getElementById('pengeluaranModalTitle').textContent = 'Tambah Pengeluaran';
    document.getElementById('pgRowIndex').value = -1;
    document.getElementById('pgRowIndex').dataset.id = '';
    const jenisField = document.getElementById('pgJenis');
    if (jenisField) {
        jenisField.selectedIndex = 0;
    }
    modal.classList.add('open');
}

function closePengeluaranModal() {
    const modal = document.getElementById('pengeluaranModal');
    if (!modal) return;
    modal.classList.remove('open');
}

function openJenisPemasukanModal() {
    const modal = document.getElementById('jenisPemasukanModal');
    if (!modal) return;

    document.getElementById('jenisPemasukanForm').reset();
    document.getElementById('jenisPemasukanModalTitle').textContent = 'Tambah Jenis Pemasukan';
    document.getElementById('jenisPemasukanRowIndex').value = -1;
    document.getElementById('jenisPemasukanRowIndex').dataset.id = '';
    modal.classList.add('open');
}

function closeJenisPemasukanModal() {
    const modal = document.getElementById('jenisPemasukanModal');
    if (!modal) return;
    modal.classList.remove('open');
}

async function requestJenisPemasukan(payload) {
    const response = await fetch('api/jenis-pemasukan.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    });
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || 'Data jenis pemasukan gagal disimpan.');
    }

    return result;
}

function buildJenisPemasukanActionButtons() {
    return `
        <div class="action-buttons">
            <button class="btn-filter btn-icon" type="button" onclick="editJenisPemasukan(this)" title="Edit" aria-label="Edit">
                <i class="fas fa-pen"></i>
            </button>
            <button class="btn-filter btn-icon btn-danger" type="button" onclick="deleteJenisPemasukan(this)" title="Hapus" aria-label="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
}

function buildJenisPemasukanRowCells(rowIndex, item) {
    return `
        <td>${rowIndex}</td>
        <td>${escapeHtml(item.jenis)}</td>
        <td>${escapeHtml(item.deskripsi)}</td>
        <td>${buildJenisPemasukanActionButtons()}</td>
    `;
}

function updateJenisPemasukanRowNumbers() {
    const tbody = document.querySelector('#jenisPemasukanModal') ? document.querySelector('.data-warga-box .data-table tbody') : null;
    if (!tbody) return;

    Array.from(tbody.querySelectorAll('tr')).forEach((row, index) => {
        row.children[0].textContent = index + 1;
    });
}

function editJenisPemasukan(button) {
    const row = button.closest('tr');
    const tbody = document.querySelector('.data-warga-box .data-table tbody');
    if (!row || !tbody) return;

    document.getElementById('jenisPemasukanModalTitle').textContent = 'Edit Jenis Pemasukan';
    document.getElementById('jenisPemasukanRowIndex').value = Array.from(tbody.children).indexOf(row);
    document.getElementById('jenisPemasukanRowIndex').dataset.id = row.dataset.id || '';
    document.getElementById('jenisPemasukanNama').value = row.children[1].textContent.trim();
    document.getElementById('jenisPemasukanDeskripsi').value = row.children[2].textContent.trim();
    document.getElementById('jenisPemasukanModal').classList.add('open');
}

async function saveJenisPemasukan(event) {
    event.preventDefault();

    const rowIndexInput = document.getElementById('jenisPemasukanRowIndex');
    const rowIndex = Number(rowIndexInput.value);
    const nama = document.getElementById('jenisPemasukanNama').value.trim();
    const deskripsi = document.getElementById('jenisPemasukanDeskripsi').value.trim();
    if (!nama || !deskripsi) {
        alert('Lengkapi semua kolom sebelum menyimpan jenis pemasukan.');
        return;
    }

    const tbody = document.querySelector('table.data-table tbody');
    if (!tbody) return;

    try {
        const result = await requestJenisPemasukan({
            action: rowIndex === -1 ? 'create' : 'update',
            id: rowIndexInput.dataset.id || '',
            nama,
            deskripsi
        });
        const item = result.item;

        if (rowIndex === -1) {
            const newIndex = tbody.querySelectorAll('tr').length + 1;
            const row = document.createElement('tr');
            row.dataset.id = item.id;
            row.innerHTML = buildJenisPemasukanRowCells(newIndex, item);
            tbody.appendChild(row);
        } else {
            const row = tbody.querySelectorAll('tr')[rowIndex];
            if (!row) return;
            row.dataset.id = item.id;
            row.innerHTML = buildJenisPemasukanRowCells(rowIndex + 1, item);
        }

        updateJenisPemasukanRowNumbers();
        closeJenisPemasukanModal();
    } catch (error) {
        alert(error.message);
    }
}

async function deleteJenisPemasukan(button) {
    const row = button.closest('tr');
    if (!row) return;

    if (!confirm('Hapus jenis pemasukan ini?')) return;

    try {
        await requestJenisPemasukan({
            action: 'delete',
            id: row.dataset.id || ''
        });
        row.remove();
        updateJenisPemasukanRowNumbers();
    } catch (error) {
        alert(error.message);
    }
}

function openJenisPengeluaranModal() {
    const modal = document.getElementById('jenisPengeluaranModal');
    if (!modal) return;

    document.getElementById('jenisPengeluaranForm').reset();
    modal.classList.add('open');
}

function closeJenisPengeluaranModal() {
    const modal = document.getElementById('jenisPengeluaranModal');
    if (!modal) return;
    modal.classList.remove('open');
}

function saveJenisPengeluaran(event) {
    event.preventDefault();

    const nama = document.getElementById('jenisPengeluaranNama').value.trim();
    const deskripsi = document.getElementById('jenisPengeluaranDeskripsi').value.trim();
    if (!nama || !deskripsi) {
        alert('Lengkapi semua kolom sebelum menyimpan jenis pengeluaran.');
        return;
    }

    const tbody = document.querySelector('table.data-table tbody');
    if (!tbody) return;

    const rowIndex = tbody.querySelectorAll('tr').length + 1;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${rowIndex}</td>
        <td>${nama}</td>
        <td>${deskripsi}</td>
    `;
    tbody.appendChild(row);

    closeJenisPengeluaranModal();
}

function buildPengeluaranActionButtons() {
    return `
        <div class="action-buttons">
            <button class="btn-filter btn-icon" type="button" onclick="editPengeluaran(this)" title="Edit" aria-label="Edit">
                <i class="fas fa-pen"></i>
            </button>
            <button class="btn-filter btn-icon btn-danger" type="button" onclick="deletePengeluaran(this)" title="Hapus" aria-label="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
}

function buildPengeluaranRowCells(rowIndex, data) {
    return `
        <td>${rowIndex}</td>
        <td>${escapeHtml(data.tanggal)}</td>
        <td>${escapeHtml(data.jenis)}</td>
        <td>${escapeHtml(data.tujuan)}</td>
        <td>${escapeHtml(data.keterangan)}</td>
        <td class="text-right text-danger">${escapeHtml(data.jumlah)}</td>
        <td>${buildPengeluaranActionButtons()}</td>
    `;
}

async function requestPengeluaran(payload) {
    const response = await fetch('api/pengeluaran.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    });
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || 'Data pengeluaran gagal disimpan.');
    }

    return result;
}

function editPengeluaran(button) {
    const row = button.closest('tr');
    const tbody = document.getElementById('pengeluaranRows');
    if (!row || !tbody) return;

    const cells = row.children;
    document.getElementById('pengeluaranModalTitle').textContent = 'Edit Pengeluaran';
    document.getElementById('pgRowIndex').value = Array.from(tbody.children).indexOf(row);
    document.getElementById('pgRowIndex').dataset.id = row.dataset.id || '';
    document.getElementById('pgTanggal').value = normalizeDateForInput(cells[1].textContent.trim());
    setSelectValue(document.getElementById('pgJenis'), cells[2].textContent.trim());
    document.getElementById('pgTujuan').value = cells[3].textContent.trim();
    document.getElementById('pgKeterangan').value = cells[4].textContent.trim();
    document.getElementById('pgJumlah').value = cells[5].textContent.trim();

    document.getElementById('pengeluaranModal').classList.add('open');
}

function savePengeluaran(event) {
    event.preventDefault();

    const rowIndexInput = document.getElementById('pgRowIndex');
    const rowIndex = Number(rowIndexInput.value);
    const tanggal = document.getElementById('pgTanggal').value.trim();
    const jenis = document.getElementById('pgJenis').value.trim();
    const tujuan = document.getElementById('pgTujuan').value.trim();
    const keterangan = document.getElementById('pgKeterangan').value.trim();
    const jumlahInput = document.getElementById('pgJumlah').value.trim();
    const jumlah = formatCurrency(jumlahInput);

    if (!tanggal || !jenis || !tujuan || !keterangan || !jumlahInput) {
        alert('Lengkapi semua kolom sebelum menyimpan pengeluaran.');
        return;
    }

    const tbody = document.getElementById('pengeluaranRows');
    if (!tbody) return;

    requestPengeluaran({
        action: rowIndex === -1 ? 'create' : 'update',
        id: rowIndexInput.dataset.id || '',
        tanggal,
        jenis,
        tujuan,
        keterangan,
        jumlah: jumlahInput
    })
        .then((result) => {
            const data = result.item;
            if (rowIndex === -1) {
                const newIndex = tbody.querySelectorAll('tr').length + 1;
                const row = document.createElement('tr');
                row.dataset.id = data.id;
                row.innerHTML = buildPengeluaranRowCells(newIndex, data);
                tbody.appendChild(row);
            } else {
                const row = tbody.querySelectorAll('tr')[rowIndex];
                if (!row) return;
                row.dataset.id = data.id;
                row.innerHTML = buildPengeluaranRowCells(rowIndex + 1, data);
            }

            searchPengeluaranTable(false);
            closePengeluaranModal();
        })
        .catch((error) => {
            alert(error.message);
        });
}

function deletePengeluaran(button) {
    const row = button.closest('tr');
    if (!row) return;

    if (!confirm('Hapus data pengeluaran ini?')) return;

    requestPengeluaran({
        action: 'delete',
        id: row.dataset.id || ''
    })
        .then(() => {
            row.remove();
            searchPengeluaranTable(false);
        })
        .catch((error) => {
            alert(error.message);
        });
}

const pengeluaranPageSize = 10;
let pengeluaranCurrentPage = 1;

function getFilteredPengeluaranRows() {
    const tbody = document.getElementById('pengeluaranRows');
    if (!tbody) return [];

    return Array.from(tbody.querySelectorAll('tr')).filter((row) => row.dataset.searchMatch !== 'false');
}

function searchPengeluaranTable(resetPage = true) {
    const tbody = document.getElementById('pengeluaranRows');
    const searchInput = document.getElementById('pengeluaranSearch');
    if (!tbody || !searchInput) return;

    if (resetPage) {
        pengeluaranCurrentPage = 1;
    }

    const keyword = searchInput.value.trim().toLowerCase();
    Array.from(tbody.querySelectorAll('tr')).forEach((row) => {
        const searchableText = Array.from(row.children)
            .slice(1, 6)
            .map((cell) => cell.textContent.trim().toLowerCase())
            .join(' ');

        row.dataset.searchMatch = keyword === '' || searchableText.includes(keyword) ? 'true' : 'false';
    });

    renderPengeluaranPagination();
}

function renderPengeluaranPagination() {
    const tbody = document.getElementById('pengeluaranRows');
    const pagination = document.getElementById('pengeluaranPagination');
    if (!tbody || !pagination) return;

    const allRows = Array.from(tbody.querySelectorAll('tr'));
    const filteredRows = getFilteredPengeluaranRows();
    const totalPages = Math.max(1, Math.ceil(filteredRows.length / pengeluaranPageSize));

    if (pengeluaranCurrentPage > totalPages) {
        pengeluaranCurrentPage = totalPages;
    }

    const startIndex = (pengeluaranCurrentPage - 1) * pengeluaranPageSize;
    const endIndex = startIndex + pengeluaranPageSize;

    allRows.forEach((row) => {
        row.hidden = true;
    });

    filteredRows.forEach((row, index) => {
        row.children[0].textContent = index + 1;
        row.hidden = index < startIndex || index >= endIndex;
    });

    const startLabel = filteredRows.length === 0 ? 0 : startIndex + 1;
    const endLabel = Math.min(endIndex, filteredRows.length);
    const pageButtons = Array.from({ length: totalPages }, (_, index) => {
        const page = index + 1;
        return `
            <button class="pagination-btn ${page === pengeluaranCurrentPage ? 'active' : ''}" type="button" onclick="goToPengeluaranPage(${page})" aria-label="Halaman ${page}">
                ${page}
            </button>
        `;
    }).join('');

    pagination.innerHTML = `
        <div class="pagination-info">Menampilkan ${startLabel}-${endLabel} dari ${filteredRows.length} data</div>
        <div class="pagination-actions">
            <button class="pagination-btn" type="button" onclick="goToPengeluaranPage(${pengeluaranCurrentPage - 1})" ${pengeluaranCurrentPage === 1 ? 'disabled' : ''} aria-label="Halaman sebelumnya">
                <i class="fas fa-chevron-left"></i>
            </button>
            ${pageButtons}
            <button class="pagination-btn" type="button" onclick="goToPengeluaranPage(${pengeluaranCurrentPage + 1})" ${pengeluaranCurrentPage === totalPages ? 'disabled' : ''} aria-label="Halaman berikutnya">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    `;
}

function goToPengeluaranPage(page) {
    const totalPages = Math.max(1, Math.ceil(getFilteredPengeluaranRows().length / pengeluaranPageSize));
    pengeluaranCurrentPage = Math.min(Math.max(Number(page), 1), totalPages);
    renderPengeluaranPagination();
}

function updateSumberAutocomplete() {
    const jenisField = document.getElementById('pmJenis');
    const sumberField = document.getElementById('pmSumber');

    if (!jenisField || !sumberField) return;

    if (jenisField.value.toLowerCase().includes('iuran')) {
        sumberField.setAttribute('list', 'kkOptions');
    } else {
        sumberField.removeAttribute('list');
    }
}

function formatCurrency(value) {
    const numeric = value.replace(/[^0-9.-]/g, '');
    if (numeric === '' || Number.isNaN(Number(numeric))) {
        return value;
    }
    const amount = Number(numeric);
    return amount.toLocaleString('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).replace('Rp', 'Rp ');
}

function buildPemasukanActionButtons() {
    return `
        <div class="action-buttons">
            <button class="btn-filter btn-icon" type="button" onclick="editPemasukan(this)" title="Edit" aria-label="Edit">
                <i class="fas fa-pen"></i>
            </button>
            <button class="btn-filter btn-icon btn-danger" type="button" onclick="deletePemasukan(this)" title="Hapus" aria-label="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
}

function buildPemasukanRowCells(rowIndex, data) {
    return `
        <td>${rowIndex}</td>
        <td>${escapeHtml(data.tanggal)}</td>
        <td>${escapeHtml(data.jenis)}</td>
        <td>${escapeHtml(data.sumber)}</td>
        <td>${escapeHtml(data.keterangan)}</td>
        <td class="text-right text-success">${escapeHtml(data.jumlah)}</td>
        <td>${buildPemasukanActionButtons()}</td>
    `;
}

async function requestPemasukan(payload) {
    const response = await fetch('api/pemasukan.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    });
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || 'Data pemasukan gagal disimpan.');
    }

    return result;
}

function normalizeDateForInput(value) {
    if (/^\d{4}-\d{2}-\d{2}$/.test(value)) return value;

    const parsedDate = new Date(value);
    if (Number.isNaN(parsedDate.getTime())) return '';

    const year = parsedDate.getFullYear();
    const month = String(parsedDate.getMonth() + 1).padStart(2, '0');
    const day = String(parsedDate.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function setSelectValue(selectElement, value) {
    if (!selectElement) return;

    const hasOption = Array.from(selectElement.options).some((option) => option.value === value);
    if (!hasOption && value) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        selectElement.appendChild(option);
    }

    selectElement.value = value;
}

function editPemasukan(button) {
    const row = button.closest('tr');
    const tbody = document.getElementById('pemasukanRows');
    if (!row || !tbody) return;

    const cells = row.children;
    document.getElementById('pemasukanModalTitle').textContent = 'Edit Pemasukan';
    document.getElementById('pmRowIndex').value = Array.from(tbody.children).indexOf(row);
    document.getElementById('pmRowIndex').dataset.id = row.dataset.id || '';
    document.getElementById('pmTanggal').value = normalizeDateForInput(cells[1].textContent.trim());
    setSelectValue(document.getElementById('pmJenis'), cells[2].textContent.trim());
    document.getElementById('pmSumber').value = cells[3].textContent.trim();
    document.getElementById('pmKeterangan').value = cells[4].textContent.trim();
    document.getElementById('pmJumlah').value = cells[5].textContent.trim();
    updateSumberAutocomplete();

    document.getElementById('pemasukanModal').classList.add('open');
}

function savePemasukan(event) {
    event.preventDefault();

    const rowIndexInput = document.getElementById('pmRowIndex');
    const rowIndex = Number(rowIndexInput.value);
    const tanggal = document.getElementById('pmTanggal').value.trim();
    const jenis = document.getElementById('pmJenis').value.trim();
    const sumber = document.getElementById('pmSumber').value.trim();
    const keterangan = document.getElementById('pmKeterangan').value.trim();
    const jumlahInput = document.getElementById('pmJumlah').value.trim();
    const jumlah = formatCurrency(jumlahInput);

    if (!tanggal || !jenis || !sumber || !keterangan || !jumlahInput) {
        alert('Lengkapi semua kolom sebelum menyimpan pemasukan.');
        return;
    }

    const tbody = document.getElementById('pemasukanRows');
    if (!tbody) return;

    const payload = {
        action: rowIndex === -1 ? 'create' : 'update',
        id: rowIndexInput.dataset.id || '',
        tanggal,
        jenis,
        sumber,
        keterangan,
        jumlah: jumlahInput
    };

    requestPemasukan(payload)
        .then((result) => {
            const data = result.item;
            if (rowIndex === -1) {
        const newIndex = tbody.querySelectorAll('tr').length + 1;
        const row = document.createElement('tr');
                row.dataset.id = data.id;
        row.innerHTML = buildPemasukanRowCells(newIndex, data);
        tbody.appendChild(row);
    } else {
        const row = tbody.querySelectorAll('tr')[rowIndex];
        if (!row) return;
                row.dataset.id = data.id;
        row.innerHTML = buildPemasukanRowCells(rowIndex + 1, data);
    }

    searchPemasukanTable(false);
    closePemasukanModal();
        })
        .catch((error) => {
            alert(error.message);
        });
}

function deletePemasukan(button) {
    const row = button.closest('tr');
    if (!row) return;

    if (!confirm('Hapus data pemasukan ini?')) return;

    requestPemasukan({
        action: 'delete',
        id: row.dataset.id || ''
    })
        .then(() => {
            row.remove();
            searchPemasukanTable(false);
        })
        .catch((error) => {
            alert(error.message);
        });
}

const pemasukanPageSize = 10;
let pemasukanCurrentPage = 1;

function getFilteredPemasukanRows() {
    const tbody = document.getElementById('pemasukanRows');
    if (!tbody) return [];

    return Array.from(tbody.querySelectorAll('tr')).filter((row) => row.dataset.searchMatch !== 'false');
}

function searchPemasukanTable(resetPage = true) {
    const tbody = document.getElementById('pemasukanRows');
    const searchInput = document.getElementById('pemasukanSearch');
    if (!tbody || !searchInput) return;

    if (resetPage) {
        pemasukanCurrentPage = 1;
    }

    const keyword = searchInput.value.trim().toLowerCase();
    Array.from(tbody.querySelectorAll('tr')).forEach((row) => {
        const searchableText = Array.from(row.children)
            .slice(1, 6)
            .map((cell) => cell.textContent.trim().toLowerCase())
            .join(' ');

        row.dataset.searchMatch = keyword === '' || searchableText.includes(keyword) ? 'true' : 'false';
    });

    renderPemasukanPagination();
}

function renderPemasukanPagination() {
    const tbody = document.getElementById('pemasukanRows');
    const pagination = document.getElementById('pemasukanPagination');
    if (!tbody || !pagination) return;

    const allRows = Array.from(tbody.querySelectorAll('tr'));
    const filteredRows = getFilteredPemasukanRows();
    const totalPages = Math.max(1, Math.ceil(filteredRows.length / pemasukanPageSize));

    if (pemasukanCurrentPage > totalPages) {
        pemasukanCurrentPage = totalPages;
    }

    const startIndex = (pemasukanCurrentPage - 1) * pemasukanPageSize;
    const endIndex = startIndex + pemasukanPageSize;

    allRows.forEach((row) => {
        row.hidden = true;
    });

    filteredRows.forEach((row, index) => {
        row.children[0].textContent = index + 1;
        row.hidden = index < startIndex || index >= endIndex;
    });

    const startLabel = filteredRows.length === 0 ? 0 : startIndex + 1;
    const endLabel = Math.min(endIndex, filteredRows.length);
    const pageButtons = Array.from({ length: totalPages }, (_, index) => {
        const page = index + 1;
        return `
            <button class="pagination-btn ${page === pemasukanCurrentPage ? 'active' : ''}" type="button" onclick="goToPemasukanPage(${page})" aria-label="Halaman ${page}">
                ${page}
            </button>
        `;
    }).join('');

    pagination.innerHTML = `
        <div class="pagination-info">Menampilkan ${startLabel}-${endLabel} dari ${filteredRows.length} data</div>
        <div class="pagination-actions">
            <button class="pagination-btn" type="button" onclick="goToPemasukanPage(${pemasukanCurrentPage - 1})" ${pemasukanCurrentPage === 1 ? 'disabled' : ''} aria-label="Halaman sebelumnya">
                <i class="fas fa-chevron-left"></i>
            </button>
            ${pageButtons}
            <button class="pagination-btn" type="button" onclick="goToPemasukanPage(${pemasukanCurrentPage + 1})" ${pemasukanCurrentPage === totalPages ? 'disabled' : ''} aria-label="Halaman berikutnya">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    `;
}

function goToPemasukanPage(page) {
    const totalPages = Math.max(1, Math.ceil(getFilteredPemasukanRows().length / pemasukanPageSize));
    pemasukanCurrentPage = Math.min(Math.max(Number(page), 1), totalPages);
    renderPemasukanPagination();
}

function addWarga() {
    document.getElementById('editModalTitle').textContent = 'Tambah Data Warga';
    document.getElementById('editRowIndex').value = -1;
    document.getElementById('editRowIndex').dataset.id = '';
    document.getElementById('editNama').value = '';
    document.getElementById('editAlamat').value = '';
    document.getElementById('editHp').value = '';
    document.getElementById('editJumlahAnggota').value = 1;
    document.getElementById('editStatus').value = 'Domisili';
    document.getElementById('editPeran1').value = 'Warga';
    document.getElementById('editPeran2').value = '';
    document.getElementById('editModalOverlay').classList.add('open');
}

function buildWargaActionButtons() {
    return `
        <div class="action-buttons">
            <button class="btn-filter btn-icon" type="button" onclick="editWarga(this)" title="Edit" aria-label="Edit">
                <i class="fas fa-pen"></i>
            </button>
            <button class="btn-filter btn-icon btn-danger" type="button" onclick="deleteWarga(this)" title="Hapus" aria-label="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
}

const wargaPageSize = 10;
let wargaCurrentPage = 1;

function updateWargaRowNumbers() {
    const tbody = document.querySelector('.data-warga-box .data-table tbody');
    if (!tbody) return;

    renderWargaPagination();
}

function getFilteredWargaRows() {
    const tbody = document.querySelector('.data-warga-box .data-table tbody');
    if (!tbody) return [];

    return Array.from(tbody.querySelectorAll('tr')).filter((row) => row.dataset.searchMatch !== 'false');
}

function searchWargaTable(resetPage = true) {
    const tbody = document.querySelector('.data-warga-box .data-table tbody');
    const searchInput = document.getElementById('wargaSearch');
    if (!tbody || !searchInput) return;

    if (resetPage) {
        wargaCurrentPage = 1;
    }
    const keyword = searchInput.value.trim().toLowerCase();

    Array.from(tbody.querySelectorAll('tr')).forEach((row) => {
        const searchableText = Array.from(row.children)
            .slice(1, 7)
            .map((cell) => cell.textContent.trim().toLowerCase())
            .join(' ');

        row.dataset.searchMatch = keyword === '' || searchableText.includes(keyword) ? 'true' : 'false';
    });

    renderWargaPagination();
}

function renderWargaPagination() {
    const tbody = document.querySelector('.data-warga-box .data-table tbody');
    const pagination = document.getElementById('wargaPagination');
    if (!tbody || !pagination) return;

    const allRows = Array.from(tbody.querySelectorAll('tr'));
    const filteredRows = getFilteredWargaRows();
    const totalPages = Math.max(1, Math.ceil(filteredRows.length / wargaPageSize));

    if (wargaCurrentPage > totalPages) {
        wargaCurrentPage = totalPages;
    }

    const startIndex = (wargaCurrentPage - 1) * wargaPageSize;
    const endIndex = startIndex + wargaPageSize;

    allRows.forEach((row) => {
        row.hidden = true;
    });

    filteredRows.forEach((row, index) => {
        row.children[0].textContent = index + 1;
        row.hidden = index < startIndex || index >= endIndex;
    });

    const startLabel = filteredRows.length === 0 ? 0 : startIndex + 1;
    const endLabel = Math.min(endIndex, filteredRows.length);

    const pageButtons = Array.from({ length: totalPages }, (_, index) => {
        const page = index + 1;
        return `
            <button class="pagination-btn ${page === wargaCurrentPage ? 'active' : ''}" type="button" onclick="goToWargaPage(${page})" aria-label="Halaman ${page}">
                ${page}
            </button>
        `;
    }).join('');

    pagination.innerHTML = `
        <div class="pagination-info">Menampilkan ${startLabel}-${endLabel} dari ${filteredRows.length} data</div>
        <div class="pagination-actions">
            <button class="pagination-btn" type="button" onclick="goToWargaPage(${wargaCurrentPage - 1})" ${wargaCurrentPage === 1 ? 'disabled' : ''} aria-label="Halaman sebelumnya">
                <i class="fas fa-chevron-left"></i>
            </button>
            ${pageButtons}
            <button class="pagination-btn" type="button" onclick="goToWargaPage(${wargaCurrentPage + 1})" ${wargaCurrentPage === totalPages ? 'disabled' : ''} aria-label="Halaman berikutnya">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    `;
}

function goToWargaPage(page) {
    const totalPages = Math.max(1, Math.ceil(getFilteredWargaRows().length / wargaPageSize));
    wargaCurrentPage = Math.min(Math.max(Number(page), 1), totalPages);
    renderWargaPagination();
}

function editWarga(button) {
    const row = button.closest('tr');
    const tbody = document.querySelector('.data-warga-box .data-table tbody');
    if (!row || !tbody) return;

    const cells = row.children;
    document.getElementById('editModalTitle').textContent = 'Edit Data Warga';
    document.getElementById('editRowIndex').value = Array.from(tbody.children).indexOf(row);
    document.getElementById('editRowIndex').dataset.id = row.dataset.id || '';
    document.getElementById('editNama').value = cells[1].textContent.trim();
    document.getElementById('editAlamat').value = cells[2].textContent.trim();
    document.getElementById('editHp').value = cells[3].textContent.trim();
    document.getElementById('editJumlahAnggota').value = cells[4].textContent.trim();
    document.getElementById('editStatus').value = cells[5].textContent.trim();
    const peranValues = Array.from(cells[6].querySelectorAll('.type-badge')).map((badge) => badge.textContent.trim());
    document.getElementById('editPeran1').value = peranValues[0] || 'Warga';
    document.getElementById('editPeran2').value = peranValues[1] || '';

    document.getElementById('editModalOverlay').classList.add('open');
}

function closeEditModal() {
    document.getElementById('editModalOverlay').classList.remove('open');
}

async function requestWarga(payload) {
    const response = await fetch('api/warga.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    });
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || 'Data warga gagal disimpan.');
    }

    return result;
}

function getWargaBadgeClass(status) {
    return status === 'Domisili' ? 'badge-pemasukan' : 'badge-pengeluaran';
}

function buildWargaRoleBadges(data) {
    const roles = [data.peran_1 || 'Warga', data.peran_2].filter(Boolean);
    return `
        <div class="role-badges">
            ${roles.map((role) => `<span class="type-badge badge-neutral">${escapeHtml(role)}</span>`).join('')}
        </div>
    `;
}

function buildWargaRowCells(rowIndex, data) {
    return `
        <td>${rowIndex}</td>
        <td>${escapeHtml(data.nama)}</td>
        <td>${escapeHtml(data.alamat)}</td>
        <td>${escapeHtml(data.hp)}</td>
        <td>${escapeHtml(data.jumlah_anggota)}</td>
        <td><span class="type-badge ${getWargaBadgeClass(data.status)}">${escapeHtml(data.status)}</span></td>
        <td>${buildWargaRoleBadges(data)}</td>
        <td>${buildWargaActionButtons()}</td>
    `;
}

async function saveWarga(event) {
    event.preventDefault();

    const rowIndexInput = document.getElementById('editRowIndex');
    const rowIndex = Number(rowIndexInput.value);
    const nama = document.getElementById('editNama').value.trim();
    const alamat = document.getElementById('editAlamat').value.trim();
    const hp = document.getElementById('editHp').value.trim();
    const jumlahAnggota = document.getElementById('editJumlahAnggota').value.trim();
    const status = document.getElementById('editStatus').value;
    const peran1 = document.getElementById('editPeran1').value;
    const peran2 = document.getElementById('editPeran2').value;
    const tbody = document.querySelector('.data-warga-box .data-table tbody');

    if (!nama || !alamat || !hp || !jumlahAnggota || Number(jumlahAnggota) < 1) {
        alert('Lengkapi data warga dan pastikan jumlah anggota keluarga minimal 1.');
        return;
    }

    if (peran2 && peran1 === peran2) {
        alert('Peran utama dan peran tambahan tidak boleh sama.');
        return;
    }

    const payload = {
        action: rowIndex === -1 ? 'create' : 'update',
        id: rowIndexInput.dataset.id || '',
        nama,
        alamat,
        hp,
        jumlah_anggota: Number(jumlahAnggota),
        status,
        peran_1: peran1,
        peran_2: peran2
    };

    try {
        const result = await requestWarga(payload);
        const data = result.warga;

        if (rowIndex === -1) {
            const newIndex = tbody.querySelectorAll('tr').length + 1;
            const row = document.createElement('tr');
            row.dataset.id = data.id;
            row.innerHTML = buildWargaRowCells(newIndex, data);
            tbody.appendChild(row);
            searchWargaTable(false);
        } else {
            const rows = tbody.querySelectorAll('tr');
            const row = rows[rowIndex];
            if (!row) return;

            row.dataset.id = data.id;
            row.innerHTML = buildWargaRowCells(rowIndex + 1, data);
            searchWargaTable(false);
        }

        updateWargaRowNumbers();
        closeEditModal();
    } catch (error) {
        alert(error.message);
    }
}

async function deleteWarga(button) {
    const row = button.closest('tr');
    if (!row) return;

    if (!confirm('Hapus data warga ini?')) return;

    try {
        await requestWarga({
            action: 'delete',
            id: row.dataset.id || ''
        });
        row.remove();
        updateWargaRowNumbers();
    } catch (error) {
        alert(error.message);
    }
}

function openAsetModal() {
    const modal = document.getElementById('asetModal');
    if (!modal) return;

    document.getElementById('asetForm').reset();
    document.getElementById('asetStatus').value = 'Aktif';
    modal.classList.add('open');
}

function closeAsetModal() {
    const modal = document.getElementById('asetModal');
    if (!modal) return;
    modal.classList.remove('open');
}

function saveAset(event) {
    event.preventDefault();

    const nama = document.getElementById('asetNama').value.trim();
    const lokasi = document.getElementById('asetLokasi').value.trim();
    const baikValue = document.getElementById('asetBaik').value.trim();
    const rusakValue = document.getElementById('asetRusak').value.trim();
    const keterangan = document.getElementById('asetKeterangan').value.trim();
    const status = document.getElementById('asetStatus').value;

    if (!nama || !lokasi || baikValue === '' || rusakValue === '' || !keterangan || !status) {
        alert('Lengkapi semua kolom sebelum menyimpan aset.');
        return;
    }

    const baik = Number(baikValue);
    const rusak = Number(rusakValue);
    if (Number.isNaN(baik) || Number.isNaN(rusak) || baik < 0 || rusak < 0) {
        alert('Kolom Baik dan Rusak harus angka positif.');
        return;
    }

    const tbody = document.querySelector('table.data-table tbody');
    if (!tbody) return;

    const rowIndex = tbody.querySelectorAll('tr').length + 1;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${rowIndex}</td>
        <td>${nama}</td>
        <td>${lokasi}</td>
        <td>${baik}</td>
        <td>${rusak}</td>
        <td>${keterangan}</td>
        <td><span class="type-badge ${status === 'Aktif' ? 'badge-pemasukan' : 'badge-pengeluaran'}">${status}</span></td>
    `;
    tbody.appendChild(row);

    closeAsetModal();
}

function openSewaModal() {
    const modal = document.getElementById('sewaModal');
    if (!modal) return;

    document.getElementById('sewaForm').reset();
    document.getElementById('sewaStatus').value = 'Aktif';
    modal.classList.add('open');
}

function closeSewaModal() {
    const modal = document.getElementById('sewaModal');
    if (!modal) return;
    modal.classList.remove('open');
}

function saveSewa(event) {
    event.preventDefault();

    const aset = document.getElementById('sewaAset').value.trim();
    const penyewa = document.getElementById('sewaPenyewa').value.trim();
    const mulai = document.getElementById('sewaMulai').value;
    const selesai = document.getElementById('sewaSelesai').value;
    const biaya = document.getElementById('sewaBiaya').value.trim();
    const status = document.getElementById('sewaStatus').value;

    if (!aset || !penyewa || !mulai || !selesai || !biaya) {
        alert('Lengkapi semua kolom sebelum menyimpan sewa.');
        return;
    }

    const tbody = document.querySelector('table.data-table tbody');
    if (!tbody) return;

    const rowIndex = tbody.querySelectorAll('tr').length + 1;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${rowIndex}</td>
        <td>${aset}</td>
        <td>${penyewa}</td>
        <td>${mulai}</td>
        <td>${selesai}</td>
        <td class="text-right">${biaya}</td>
        <td><span class="type-badge ${status === 'Aktif' ? 'badge-pemasukan' : 'badge-pengeluaran'}">${status}</span></td>
    `;
    tbody.appendChild(row);

    closeSewaModal();
}

function openHakAksesModal() {
    const modal = document.getElementById('hakAksesModal');
    if (!modal) return;

    document.getElementById('hakAksesForm').reset();
    document.getElementById('hakAksesRole').value = 'Administrator';
    document.getElementById('hakAksesStatus').value = 'Aktif';
    modal.classList.add('open');
}

function closeHakAksesModal() {
    const modal = document.getElementById('hakAksesModal');
    if (!modal) return;
    modal.classList.remove('open');
}

function saveHakAkses(event) {
    event.preventDefault();

    const nama = document.getElementById('hakAksesNama').value.trim();
    const username = document.getElementById('hakAksesUsername').value.trim();
    const password = document.getElementById('hakAksesPassword').value.trim();
    const role = document.getElementById('hakAksesRole').value;
    const status = document.getElementById('hakAksesStatus').value;

    if (!nama || !username || !password || !role || !status) {
        alert('Lengkapi semua kolom sebelum menyimpan hak akses.');
        return;
    }

    const tbody = document.getElementById('hakAksesRows');
    if (!tbody) return;

    const rowIndex = tbody.querySelectorAll('tr').length + 1;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${rowIndex}</td>
        <td>${nama}</td>
        <td>${username}</td>
        <td>${role}</td>
        <td><span class="type-badge ${status === 'Aktif' ? 'badge-pemasukan' : 'badge-pengeluaran'}">${status}</span></td>
    `;
    tbody.appendChild(row);

    closeHakAksesModal();
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function getKondisiBadgeClass(value) {
    return value === 'Baik' || value === 'Selesai' ? 'badge-pemasukan' : 'badge-pengeluaran';
}

function openKondisiAsetModal() {
    const modal = document.getElementById('kondisiAsetModal');
    if (!modal) return;

    document.getElementById('kondisiAsetForm').reset();
    document.getElementById('kondisiAsetRowIndex').value = -1;
    document.getElementById('kondisiAsetModalTitle').textContent = 'Tambah Kondisi Aset';
    document.getElementById('kondisiAsetKondisi').value = 'Baik';
    document.getElementById('kondisiAsetStatus').value = 'Dipantau';
    modal.classList.add('open');
}

function closeKondisiAsetModal() {
    const modal = document.getElementById('kondisiAsetModal');
    if (!modal) return;
    modal.classList.remove('open');
}

function buildKondisiAsetActionButtons() {
    return `
        <div class="action-buttons">
            <button class="btn-filter btn-icon" type="button" onclick="editKondisiAset(this)" title="Edit" aria-label="Edit">
                <i class="fas fa-pen"></i>
            </button>
            <button class="btn-filter btn-icon btn-danger" type="button" onclick="deleteKondisiAset(this)" title="Hapus" aria-label="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
}

function buildKondisiAsetRow(rowIndex, data) {
    return `
        <td>${rowIndex}</td>
        <td>${escapeHtml(data.nama)}</td>
        <td>${escapeHtml(data.lokasi)}</td>
        <td>${escapeHtml(data.tanggal)}</td>
        <td><span class="type-badge ${getKondisiBadgeClass(data.kondisi)}">${escapeHtml(data.kondisi)}</span></td>
        <td>${escapeHtml(data.petugas)}</td>
        <td>${escapeHtml(data.catatan)}</td>
        <td><span class="type-badge ${getKondisiBadgeClass(data.status)}">${escapeHtml(data.status)}</span></td>
        <td>${buildKondisiAsetActionButtons()}</td>
    `;
}

function updateKondisiAsetRowNumbers() {
    const tbody = document.getElementById('kondisiAsetRows');
    if (!tbody) return;

    Array.from(tbody.querySelectorAll('tr')).forEach((item, index) => {
        item.children[0].textContent = index + 1;
    });
}

function getKondisiAsetFormData() {
    return {
        nama: document.getElementById('kondisiAsetNama').value.trim(),
        lokasi: document.getElementById('kondisiAsetLokasi').value.trim(),
        tanggal: document.getElementById('kondisiAsetTanggal').value,
        kondisi: document.getElementById('kondisiAsetKondisi').value,
        petugas: document.getElementById('kondisiAsetPetugas').value.trim(),
        catatan: document.getElementById('kondisiAsetCatatan').value.trim(),
        status: document.getElementById('kondisiAsetStatus').value
    };
}

function saveKondisiAset(event) {
    event.preventDefault();

    const data = getKondisiAsetFormData();
    if (!data.nama || !data.lokasi || !data.tanggal || !data.kondisi || !data.petugas || !data.catatan || !data.status) {
        alert('Lengkapi semua kolom sebelum menyimpan kondisi aset.');
        return;
    }

    const tbody = document.getElementById('kondisiAsetRows');
    if (!tbody) return;

    const rowIndex = Number(document.getElementById('kondisiAsetRowIndex').value);
    if (rowIndex === -1) {
        const newIndex = tbody.querySelectorAll('tr').length + 1;
        const row = document.createElement('tr');
        row.innerHTML = buildKondisiAsetRow(newIndex, data);
        tbody.appendChild(row);
    } else {
        const row = tbody.querySelectorAll('tr')[rowIndex];
        if (!row) return;
        row.innerHTML = buildKondisiAsetRow(rowIndex + 1, data);
    }

    updateKondisiAsetRowNumbers();
    closeKondisiAsetModal();
}

function editKondisiAset(button) {
    const row = button.closest('tr');
    const tbody = document.getElementById('kondisiAsetRows');
    const modal = document.getElementById('kondisiAsetModal');
    if (!row || !tbody || !modal) return;

    const cells = row.children;
    document.getElementById('kondisiAsetModalTitle').textContent = 'Edit Kondisi Aset';
    document.getElementById('kondisiAsetRowIndex').value = Array.from(tbody.children).indexOf(row);
    document.getElementById('kondisiAsetNama').value = cells[1].textContent.trim();
    document.getElementById('kondisiAsetLokasi').value = cells[2].textContent.trim();
    document.getElementById('kondisiAsetTanggal').value = cells[3].textContent.trim();
    document.getElementById('kondisiAsetKondisi').value = cells[4].textContent.trim();
    document.getElementById('kondisiAsetPetugas').value = cells[5].textContent.trim();
    document.getElementById('kondisiAsetCatatan').value = cells[6].textContent.trim();
    document.getElementById('kondisiAsetStatus').value = cells[7].textContent.trim();
    modal.classList.add('open');
}

function deleteKondisiAset(button) {
    const row = button.closest('tr');
    const tbody = document.getElementById('kondisiAsetRows');
    if (!row || !tbody) return;

    if (!confirm('Hapus data kondisi aset ini?')) return;

    row.remove();
    updateKondisiAsetRowNumbers();
}

function generateLaporanPdf(reportTitle = 'Laporan') {
    const originalTitle = document.title;
    const date = new Date().toISOString().slice(0, 10);

    document.title = `${reportTitle} ${date}`;
    window.print();

    setTimeout(() => {
        document.title = originalTitle;
    }, 500);
}

function generateLaporanKeuanganPdf() {
    generateLaporanPdf('Laporan Keuangan');
}

// Inisialisasi Logika Saat DOM Selesai Dimuat
document.addEventListener("DOMContentLoaded", function() {
    console.log("Sistem Informasi Manajemen Warga - Navigasi Dashboard Terbuka.");

    const addAsetButton = document.getElementById('btnTambahAset');
    if (addAsetButton) {
        addAsetButton.addEventListener('click', openAsetModal);
    }

    const addJenisPemasukanButton = document.getElementById('btnTambahJenisPemasukan');
    if (addJenisPemasukanButton) {
        addJenisPemasukanButton.addEventListener('click', openJenisPemasukanModal);
    }

    const addJenisPengeluaranButton = document.getElementById('btnTambahJenisPengeluaran');
    if (addJenisPengeluaranButton) {
        addJenisPengeluaranButton.addEventListener('click', openJenisPengeluaranModal);
    }

    const addSewaButton = document.getElementById('btnTambahSewa');
    if (addSewaButton) {
        addSewaButton.addEventListener('click', openSewaModal);
    }

    const addHakAksesButton = document.getElementById('btnTambahHakAkses');
    if (addHakAksesButton) {
        addHakAksesButton.addEventListener('click', openHakAksesModal);
    }

    const addKondisiAsetButton = document.getElementById('btnTambahKondisiAset');
    if (addKondisiAsetButton) {
        addKondisiAsetButton.addEventListener('click', openKondisiAsetModal);
    }

    const wargaSearch = document.getElementById('wargaSearch');
    if (wargaSearch) {
        wargaSearch.addEventListener('input', searchWargaTable);
        wargaSearch.addEventListener('search', searchWargaTable);
        searchWargaTable();
    }

    const pemasukanSearch = document.getElementById('pemasukanSearch');
    if (pemasukanSearch) {
        pemasukanSearch.addEventListener('input', searchPemasukanTable);
        pemasukanSearch.addEventListener('search', searchPemasukanTable);
        searchPemasukanTable();
    }

    const pengeluaranSearch = document.getElementById('pengeluaranSearch');
    if (pengeluaranSearch) {
        pengeluaranSearch.addEventListener('input', searchPengeluaranTable);
        pengeluaranSearch.addEventListener('search', searchPengeluaranTable);
        searchPengeluaranTable();
    }
});
