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

/* ========== PERSIAPAN KOLOM created_at (untuk film lama) ========== */
$changed = false;
foreach ($data as $k => $film) {
    if (!isset($film['created_at'])) {
        // isi kira-kira waktu upload, supaya tidak 0 semua
        $data[$k]['created_at'] = time() - (count($data) - $k) * 10;
        $changed = true;
    }
}
if ($changed) {
    file_put_contents("db.json", json_encode($data, JSON_PRETTY_PRINT));
}

/* ========== SORTING ========== */

// ambil pilihan sort dari GET
$sort = $_GET['sort'] ?? 'newest';

// helper: ubah rating "⭐⭐⭐" jadi angka 3
function rating_value($r) {
    $r = $r ?? '';
    return mb_substr_count($r, '⭐');
}

// urutkan data
usort($data, function($a, $b) use ($sort) {
    $createdA = $a['created_at'] ?? 0;
    $createdB = $b['created_at'] ?? 0;
    $ratingA  = rating_value($a['rating'] ?? '');
    $ratingB  = rating_value($b['rating'] ?? '');

    switch ($sort) {
        case 'oldest':
            // paling lama dulu
            return $createdA <=> $createdB;

        case 'rating_desc':
            // rating terbaik dulu
            if ($ratingA == $ratingB) {
                return $createdB <=> $createdA; // kalau sama, pakai terbaru
            }
            return $ratingB <=> $ratingA;

        case 'rating_asc':
            // rating terendah dulu
            if ($ratingA == $ratingB) {
                return $createdB <=> $createdA;
            }
            return $ratingA <=> $ratingB;

        case 'newest':
        default:
            // terbaru dulu
            return $createdB <=> $createdA;
    }
});

/* ========= FILTER FAVORITE ========= */
$show = $_GET['show'] ?? 'all';
$showFavoriteOnly = ($show === 'favorite');

// Kalau mode favorite: filter array
if ($showFavoriteOnly) {
    $data = array_filter($data, function($film){
        return !empty($film['favorite']);
    });
    // (kalau mau super aman soal ID favorite/delete,
    // bisa pakai key asli dari data awal, tapi untuk sederhana kita pakai ini dulu)
}

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Film</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
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

        /* dropdown sort */
        .sort-select{
            padding:8px 14px;
            border-radius:999px;
            border:none;
            background:#1a1a1b;
            color:#ddd;
            font-size:13px;
            margin-left:12px;
        }
        .sort-select:focus{
            outline:none;
            box-shadow:0 0 0 2px #ff5674;
        }

        .controls{
            display:flex;
            align-items:center;
        }
    </style>
</head>
<body class="soft-dark">

    <!-- Header -->
    <div class="header">
        <h1>Daftar Film</h1>

        <div class="controls">
            <div class="filter-tabs">
                <a href="index.php?show=all&sort=<?= htmlspecialchars($sort) ?>" class="<?= !$showFavoriteOnly ? 'active' : '' ?>">Semua</a>
                <a href="index.php?show=favorite&sort=<?= htmlspecialchars($sort) ?>" class="<?= $showFavoriteOnly ? 'active' : '' ?>">Favorite</a>
            </div>

            <!-- Dropdown urutkan -->
            <form method="GET">
                <input type="hidden" name="show" value="<?= htmlspecialchars($show) ?>">
                <select name="sort" class="sort-select" onchange="this.form.submit()">
                    <option value="newest"      <?= ($sort=='newest'?'selected':'') ?>>Terbaru</option>
                    <option value="oldest"      <?= ($sort=='oldest'?'selected':'') ?>>Terlama</option>
                    <option value="rating_desc" <?= ($sort=='rating_desc'?'selected':'') ?>>Rating terbaik</option>
                    <option value="rating_asc"  <?= ($sort=='rating_asc'?'selected':'') ?>>Rating terendah</option>
                </select>
            </form>
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
