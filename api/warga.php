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
            sendJson(['success' => false, 'message' => 'ID warga tidak valid.'], 422);
        }

        $stmt = $pdo->prepare('DELETE FROM warga WHERE id = :id');
        $stmt->execute(['id' => $id]);
        sendJson(['success' => true]);
    }

    $nama = trim($input['nama'] ?? '');
    $alamat = trim($input['alamat'] ?? '');
    $hp = trim($input['hp'] ?? '');
    $jumlahAnggota = (int) ($input['jumlah_anggota'] ?? 0);
    $status = $input['status'] ?? '';
    $peranOptions = ['Warga', 'Pengurus', 'Petugas Input', 'Admin'];
    $peran1 = $input['peran_1'] ?? 'Warga';
    $peran2 = trim($input['peran_2'] ?? '');
    $peran2 = $peran2 === '' ? null : $peran2;

    if (
        $nama === '' ||
        $alamat === '' ||
        $hp === '' ||
        $jumlahAnggota < 1 ||
        !in_array($status, ['Domisili', 'Kontrak', 'Aset'], true) ||
        !in_array($peran1, $peranOptions, true) ||
        ($peran2 !== null && !in_array($peran2, $peranOptions, true)) ||
        ($peran2 !== null && $peran1 === $peran2)
    ) {
        sendJson(['success' => false, 'message' => 'Lengkapi data warga dengan benar.'], 422);
    }

    if ($action === 'create') {
        $stmt = $pdo->prepare(
            'INSERT INTO warga (nama, alamat, hp, jumlah_anggota, status, peran_1, peran_2)
             VALUES (:nama, :alamat, :hp, :jumlah_anggota, :status, :peran_1, :peran_2)'
        );
        $stmt->execute([
            'nama' => $nama,
            'alamat' => $alamat,
            'hp' => $hp,
            'jumlah_anggota' => $jumlahAnggota,
            'status' => $status,
            'peran_1' => $peran1,
            'peran_2' => $peran2,
        ]);

        sendJson([
            'success' => true,
            'warga' => [
                'id' => (int) $pdo->lastInsertId(),
                'nama' => $nama,
                'alamat' => $alamat,
                'hp' => $hp,
                'jumlah_anggota' => $jumlahAnggota,
                'status' => $status,
                'peran_1' => $peran1,
                'peran_2' => $peran2,
            ],
        ]);
    }

    if ($action === 'update') {
        $id = (int) ($input['id'] ?? 0);
        if ($id < 1) {
            sendJson(['success' => false, 'message' => 'ID warga tidak valid.'], 422);
        }

        $stmt = $pdo->prepare(
            'UPDATE warga
             SET nama = :nama, alamat = :alamat, hp = :hp, jumlah_anggota = :jumlah_anggota, status = :status, peran_1 = :peran_1, peran_2 = :peran_2
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'alamat' => $alamat,
            'hp' => $hp,
            'jumlah_anggota' => $jumlahAnggota,
            'status' => $status,
            'peran_1' => $peran1,
            'peran_2' => $peran2,
        ]);

        sendJson([
            'success' => true,
            'warga' => [
                'id' => $id,
                'nama' => $nama,
                'alamat' => $alamat,
                'hp' => $hp,
                'jumlah_anggota' => $jumlahAnggota,
                'status' => $status,
                'peran_1' => $peran1,
                'peran_2' => $peran2,
            ],
        ]);
    }

    sendJson(['success' => false, 'message' => 'Aksi tidak dikenal.'], 400);
} catch (PDOException $exception) {
    sendJson(['success' => false, 'message' => 'Gagal menyimpan data warga.'], 500);
}
?>
