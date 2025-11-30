<?php
// delete.php
if (!file_exists("db.json")) {
    // nothing to delete
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if ($id === null) {
    header("Location: index.php");
    exit;
}

$data = json_decode(file_get_contents("db.json"), true);

// safety cast id ke integer
$id = intval($id);

if (isset($data[$id])) {
    // hapus file poster lama jika ada
    if (!empty($data[$id]['poster']) && file_exists("uploads/" . $data[$id]['poster'])) {
        @unlink("uploads/" . $data[$id]['poster']);
    }

    // hapus item
    unset($data[$id]);

    // reindex supaya index berurutan 0..n-1
    $data = array_values($data);

    // simpan kembali
    file_put_contents("db.json", json_encode($data, JSON_PRETTY_PRINT));
}

header("Location: index.php");
exit;
