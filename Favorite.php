<?php
// favorite.php
// Toggle status favorite di db.json

// Pastikan db.json ada
if (!file_exists("db.json")) {
    file_put_contents("db.json", "[]");
}

$id = $_GET['id'] ?? null;
if ($id === null) {
    header("Location: index.php");
    exit;
}

$data = json_decode(file_get_contents("db.json"), true);
if (!is_array($data)) {
    $data = [];
}

// safety cast id ke integer
$id = intval($id);

if (isset($data[$id])) {
    // ambil nilai favorite saat ini (default false kalau belum ada)
    $current = $data[$id]['favorite'] ?? false;

    // toggle true/false
    $data[$id]['favorite'] = !$current;

    // simpan kembali ke db.json
    file_put_contents("db.json", json_encode($data, JSON_PRETTY_PRINT));
}

// kembali ke index
header("Location: index.php");
exit;
