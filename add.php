<?php
require "Film.php";
session_start();

// Jika POST → PROSES DATA
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $judul = $_POST["judul"];
    $genre = $_POST["genre"];

    // Upload poster
    $posterName = time() . "_" . basename($_FILES["poster"]["name"]);
    $target = "uploads/" . $posterName;

    move_uploaded_file($_FILES["poster"]["tmp_name"], $target);

    // Buat objek film baru
    $film = new Film($judul, $genre, $posterName);

    // Simpan ke session
    $_SESSION["films"][] = $film;

    header("Location: index.php");
    exit;
}

// Jika GET → TAMPILKAN FORM
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Film</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Tambah Film Baru</h1>

<form class="form-box" action="add.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="judul" placeholder="Judul Film" required>
    <input type="text" name="genre" placeholder="Genre" required>
    <input type="file" name="poster" required>
    <button type="submit">Tambah Film</button>
</form>

</body>
</html>
