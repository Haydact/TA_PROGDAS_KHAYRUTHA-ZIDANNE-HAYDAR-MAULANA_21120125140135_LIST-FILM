<?php
// add.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $judul = $_POST['judul'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $tahun = $_POST['tahun'] ?? '';
    $rating = $_POST['rating'] ?? '⭐';   // ambil rating dari form, default ⭐

    // Validasi sederhana
    if ($judul === '' || $genre === '' || $tahun === '' || $rating === '') {
        die("Form belum lengkap. <a href='add.php'>Kembali</a>");
    }

    // Pastikan db.json ada
    if (!file_exists("db.json")) {
        file_put_contents("db.json", "[]");
    }

    // Upload poster
    $filename = '';
    if (!empty($_FILES['poster']['name'])) {
        if (!is_dir("uploads")) {
            mkdir("uploads", 0755, true);
        }

        $file = $_FILES['poster'];
        $filename = time() . "-" . basename($file['name']);
        move_uploaded_file($file['tmp_name'], "uploads/" . $filename);
    }

    // Baca data lama
    $data = json_decode(file_get_contents("db.json"), true);
    if (!is_array($data)) {
        $data = [];
    }

    // Tambah film baru
    $data[] = [
        "judul"    => $judul,
        "genre"    => $genre,
        "tahun"    => $tahun,
        "poster"   => $filename,
        "rating"   => $rating,  // <-- pakai rating dari form
        "favorite" => false
    ];

    // Simpan balik
    file_put_contents("db.json", json_encode($data, JSON_PRETTY_PRINT));

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Film</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body class="soft-dark">

<div class="add-container">
    <h2>Tambah Film</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Judul</label>
            <input type="text" name="judul" required>
        </div>

        <div class="form-group">
            <label>Genre</label>
            <select name="genre" required>
                <option value="">-- Pilih Genre --</option>
                <option value="Action">Action</option>
                <option value="Drama">Drama</option>
                <option value="Komedi">Komedi</option>
                <option value="Animasi">Animasi</option>
                <option value="Romance">Romance</option>
                <option value="Horror">Horror</option>
                <option value="Sci-Fi">Sci-Fi</option>
                <option value="Sci-Fi">Fiction</option>
                <option value="Sci-Fi">Adventure</option>
            </select>
        </div>

        <div class="form-group">
            <label>Tahun</label>
            <input type="number" name="tahun" required>
        </div>

        <!-- FIELD RATING BARU -->
        <div class="form-group">
            <label>Rating</label>
            <select name="rating" required>
                <option value="">-- Pilih Rating --</option>
                <option value="⭐">⭐</option>
                <option value="⭐⭐">⭐⭐</option>
                <option value="⭐⭐⭐">⭐⭐⭐</option>
                <option value="⭐⭐⭐⭐">⭐⭐⭐⭐</option>
                <option value="⭐⭐⭐⭐⭐">⭐⭐⭐⭐⭐</option>
            </select>
        </div>

        <div class="form-group">
            <label>Poster</label>
            <input type="file" name="poster" required>
        </div>

        <button type="submit">Tambah</button>
    </form>
</div>

</body>
</html>
