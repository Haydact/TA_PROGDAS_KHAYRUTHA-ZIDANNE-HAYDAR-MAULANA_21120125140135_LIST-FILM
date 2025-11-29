
<?php
require "Film.php";
session_start();

if (!isset($_SESSION["films"])) {
    $_SESSION["films"] = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Film</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<a href="add.php" class="btn">+ Tambah Film Baru</a>

<h1>🎬 Daftar Film yang Sudah Kamu Tonton</h1>

<div class="container">
<?php foreach ($_SESSION["films"] as $id => $film): ?>
    <div class="card">

        <img src="uploads/<?= $film->poster ?>" alt="Poster">

        <h3><?= htmlspecialchars($film->judul) ?></h3>
        <p><?= htmlspecialchars($film->genre) ?></p>

        <?php if ($film->favorite): ?>
            <a class="fav-btn active" href="favorite.php?id=<?= $id ?>">❤️ Favorite</a>
        <?php else: ?>
            <a class="fav-btn" href="favorite.php?id=<?= $id ?>">♡ Favorite</a>
        <?php endif; ?>

    </div>
<?php endforeach; ?>
</div>

</body>
</html>
