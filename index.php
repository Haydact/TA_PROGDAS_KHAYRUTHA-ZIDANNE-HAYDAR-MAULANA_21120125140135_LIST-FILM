<?php
// Pastikan db.json ada
if (!file_exists("db.json")) {
    file_put_contents("db.json", "[]");
}

// Ambil data film dari JSON
$raw  = file_get_contents("db.json");
$data = json_decode($raw, true);

if (!is_array($data)) {
    $data = [];
}

// Reindex 0..n-1
$data = array_values($data);

// Pastikan folder uploads ada
if (!is_dir("uploads")) {
    mkdir("uploads", 0755, true);
}

// Cek apakah user sedang lihat hanya favorite
$show = $_GET['show'] ?? 'all';
$showFavoriteOnly = ($show === 'favorite');

// Kalau mode favorite: filter array
if ($showFavoriteOnly) {
    $data = array_filter($data, function($film){
        return !empty($film['favorite']);
    });
    // setelah filter, reindex lagi supaya foreach pakai index baru (tapi id favorite/hapus harus hati2 kalau mau fix, untuk sederhana kita biarin pakai key asli)
}


?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Film</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        /* Tambahan kecil khusus index */

        .card.favorite{
            border:2px solid #ff5674;
            box-shadow:0 0 20px rgba(255,86,116,0.4);
        }

        .empty{
            padding:40px 0;
            text-align:center;
            color:var(--muted);
            font-size:15px;
        }

        /* Tab filter di header */
        .filter-tabs{
            display:flex;
            gap:10px;
        }
        .filter-tabs a{
            padding:8px 14px;
            border-radius:20px;
            text-decoration:none;
            font-size:13px;
            color:#ddd;
            background:#1c1c1d;
        }
        .filter-tabs a.active{
            background:#ff5674;
            color:white;
            font-weight:600;
        }
    </style>
</head>
<body class="soft-dark">

    <!-- Header -->
    <div class="header">
        <h1>Daftar Film</h1>

        <div class="controls">
            <div class="filter-tabs">
                <a href="index.php" class="<?= !$showFavoriteOnly ? 'active' : '' ?>">Semua</a>
                <a href="index.php?show=favorite" class="<?= $showFavoriteOnly ? 'active' : '' ?>">Favorite</a>
            </div>
        </div>
    </div>

    <!-- Wrap utama -->
    <div class="main-wrap">
        <?php if (empty($data)): ?>
            <div class="empty">
                <?php if ($showFavoriteOnly): ?>
                    Belum ada film yang ditandai sebagai favorite.
                <?php else: ?>
                    Belum ada film. Tambah film baru terlebih dulu.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="cards">
                <?php foreach ($data as $i => $f): ?>
                    <?php
                        $isFav  = $f['favorite'] ?? false;
                        $judul  = htmlspecialchars($f['judul'] ?? 'Untitled');
                        $genre  = htmlspecialchars($f['genre'] ?? '-');
                        $tahun  = htmlspecialchars($f['tahun'] ?? '-');
                        $rating = htmlspecialchars($f['rating'] ?? '-');
                        $poster = $f['poster'] ?? '';

                        // penting: gunakan key asli id-nya dari array awal kalau mau favorite/delete tetap benar
                        // di sini $i masih key dari $data hasil filter. kalau ingin 100% aman:
                        // pakai array_keys
                    ?>
                    <div class="card <?= $isFav ? 'favorite' : '' ?>">
                        <div class="poster">
                            <?php if (!empty($poster) && file_exists("uploads/" . $poster)): ?>
                                <img src="uploads/<?= htmlspecialchars($poster) ?>" alt="poster">
                            <?php else: ?>
                                <div style="width:200px;height:300px;background:#333;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#888;margin:auto;">
                                    No Image
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="title"><?= $judul ?></div>
                        <div class="genre"><?= $genre ?> • <?= $tahun ?></div>
                        <div class="rating"><?= $rating ?></div>

                        <!-- Tombol Favorite -->
                        <a href="favorite.php?id=<?= $i ?>" class="fav-btn <?= $isFav ? 'active' : '' ?>">
                            <?= $isFav ? '❤️ Favorite' : '🤍 Jadikan Favorite' ?>
                        </a>

                        <!-- Tombol Edit / Hapus -->
                        <div class="actions">
                            <a href="edit.php?id=<?= $i ?>" class="edit">Edit</a>
                            <a href="delete.php?id=<?= $i ?>" class="del"
                               onclick="return confirm('Hapus film ini?')">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Floating Add Button -->
    <a class="fab" href="add.php">+</a>

</body>
</html>
