<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../inc/db.php';

function sendJson($payload, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function formatApiRupiah($value) {
    return 'Rp ' . number_format((int) $value, 0, ',', '.');
}

function formatPemasukanRow($id, $tanggal, $jenis, $sumber, $keterangan, $jumlah) {
    return [
        'id' => (int) $id,
        'tanggal' => date('d M Y', strtotime($tanggal)),
        'jenis' => $jenis,
        'sumber' => $sumber,
        'keterangan' => $keterangan,
        'jumlah' => '+ ' . formatApiRupiah(abs($jumlah)),
    ];
}

function getJenisPemasukanId($pdo, $nama) {
    $stmt = $pdo->prepare('SELECT id FROM jenis_transaksi WHERE tipe = "Pemasukan" AND nama = :nama LIMIT 1');
    $stmt->execute(['nama' => $nama]);
    $row = $stmt->fetch();
    return $row ? (int) $row['id'] : null;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Metode tidak diizinkan.'], 405);
}

if (!$dbAvailable || !$pdo) {
    sendJson(['success' => false, 'message' => 'Koneksi database tidak tersedia.'], 500);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    sendJson(['success' => false, 'message' => 'Format data tidak valid.'], 400);
}

$action = $input['action'] ?? '';

try {
    if ($action === 'delete') {
        $id = (int) ($input['id'] ?? 0);
        if ($id < 1) {
            sendJson(['success' => false, 'message' => 'ID pemasukan tidak valid.'], 422);
        }

        $stmt = $pdo->prepare('DELETE FROM transaksi WHERE id = :id AND tipe = "Pemasukan"');
        $stmt->execute(['id' => $id]);
        sendJson(['success' => true]);
    }

    $tanggal = trim($input['tanggal'] ?? '');
    $jenis = trim($input['jenis'] ?? '');
    $sumber = trim($input['sumber'] ?? '');
    $keterangan = trim($input['keterangan'] ?? '');
    $jumlah = (int) preg_replace('/[^0-9-]/', '', $input['jumlah'] ?? '');

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) || $jenis === '' || $sumber === '' || $keterangan === '' || $jumlah <= 0) {
        sendJson(['success' => false, 'message' => 'Lengkapi data pemasukan dengan benar.'], 422);
    }

    $jenisTransaksiId = getJenisPemasukanId($pdo, $jenis);
    if (!$jenisTransaksiId) {
        sendJson(['success' => false, 'message' => 'Jenis pemasukan tidak ditemukan di Data Master.'], 422);
    }

    if ($action === 'create') {
        $stmt = $pdo->prepare(
            'INSERT INTO transaksi (tanggal, tipe, jenis_transaksi_id, sumber, keterangan, jumlah)
             VALUES (:tanggal, "Pemasukan", :jenis_transaksi_id, :sumber, :keterangan, :jumlah)'
        );
        $stmt->execute([
            'tanggal' => $tanggal,
            'jenis_transaksi_id' => $jenisTransaksiId,
            'sumber' => $sumber,
            'keterangan' => $keterangan,
            'jumlah' => $jumlah,
        ]);

        sendJson([
            'success' => true,
            'item' => formatPemasukanRow($pdo->lastInsertId(), $tanggal, $jenis, $sumber, $keterangan, $jumlah),
        ]);
    }

    if ($action === 'update') {
        $id = (int) ($input['id'] ?? 0);
        if ($id < 1) {
            sendJson(['success' => false, 'message' => 'ID pemasukan tidak valid.'], 422);
        }

        $stmt = $pdo->prepare(
            'UPDATE transaksi
             SET tanggal = :tanggal, jenis_transaksi_id = :jenis_transaksi_id, sumber = :sumber, keterangan = :keterangan, jumlah = :jumlah
             WHERE id = :id AND tipe = "Pemasukan"'
        );
        $stmt->execute([
            'id' => $id,
            'tanggal' => $tanggal,
            'jenis_transaksi_id' => $jenisTransaksiId,
            'sumber' => $sumber,
            'keterangan' => $keterangan,
            'jumlah' => $jumlah,
        ]);

        sendJson([
            'success' => true,
            'item' => formatPemasukanRow($id, $tanggal, $jenis, $sumber, $keterangan, $jumlah),
        ]);
    }

    sendJson(['success' => false, 'message' => 'Aksi tidak dikenal.'], 400);
} catch (PDOException $exception) {
    sendJson(['success' => false, 'message' => 'Gagal menyimpan data pemasukan.'], 500);
}
?>
