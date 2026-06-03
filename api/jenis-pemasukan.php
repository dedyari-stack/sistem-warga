<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../inc/db.php';

function sendJson($payload, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
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
            sendJson(['success' => false, 'message' => 'ID jenis pemasukan tidak valid.'], 422);
        }

        $stmt = $pdo->prepare('DELETE FROM jenis_transaksi WHERE id = :id AND tipe = "Pemasukan"');
        $stmt->execute(['id' => $id]);
        sendJson(['success' => true]);
    }

    $nama = trim($input['nama'] ?? '');
    $deskripsi = trim($input['deskripsi'] ?? '');

    if ($nama === '' || $deskripsi === '') {
        sendJson(['success' => false, 'message' => 'Lengkapi data jenis pemasukan.'], 422);
    }

    if ($action === 'create') {
        $stmt = $pdo->prepare(
            'INSERT INTO jenis_transaksi (tipe, nama, deskripsi)
             VALUES ("Pemasukan", :nama, :deskripsi)'
        );
        $stmt->execute([
            'nama' => $nama,
            'deskripsi' => $deskripsi,
        ]);

        sendJson([
            'success' => true,
            'item' => [
                'id' => (int) $pdo->lastInsertId(),
                'jenis' => $nama,
                'deskripsi' => $deskripsi,
            ],
        ]);
    }

    if ($action === 'update') {
        $id = (int) ($input['id'] ?? 0);
        if ($id < 1) {
            sendJson(['success' => false, 'message' => 'ID jenis pemasukan tidak valid.'], 422);
        }

        $stmt = $pdo->prepare(
            'UPDATE jenis_transaksi
             SET nama = :nama, deskripsi = :deskripsi
             WHERE id = :id AND tipe = "Pemasukan"'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'deskripsi' => $deskripsi,
        ]);

        sendJson([
            'success' => true,
            'item' => [
                'id' => $id,
                'jenis' => $nama,
                'deskripsi' => $deskripsi,
            ],
        ]);
    }

    sendJson(['success' => false, 'message' => 'Aksi tidak dikenal.'], 400);
} catch (PDOException $exception) {
    sendJson(['success' => false, 'message' => 'Gagal menyimpan jenis pemasukan.'], 500);
}
?>
