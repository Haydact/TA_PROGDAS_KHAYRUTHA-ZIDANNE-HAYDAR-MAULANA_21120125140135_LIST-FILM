<?php
// edit.php
$id = $_GET['id'] ?? null;

// buat db.json jika belum ada
if (!file_exists("db.json")) {
    file_put_contents("db.json", "[]");
}

$data = json_decode(file_get_contents("db.json"), true);

// cast id ke integer untuk menghindari "string index" issues
if ($id === null) {
    echo "<h2 style='color:white; text-align:center;'>ID tidak diberikan. <a href='index.php'>Kembali</a></h2>";
    exit;
}

$id = intval($id);

if (!isset($data[$id])) {
    echo "<h2 style='color:white; text-align:center;'>Film tidak ditemukan. <a href='index.php'>Kembali</a></h2>";
    exit;
}

$film = $data[$id];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data[$id]['judul'] = $_POST['judul'] ?? $data[$id]['judul'];
    $data[$id]['genre'] = $_POST['genre'] ?? $data[$id]['genre'];
    $data[$id]['rating'] = $_POST['rating'] ?? $data[$id]['rating'];

    if (!empty($_FILES['poster']['name'])) {
        $file = $_FILES['poster'];
        $filename = time() . "-" . basename($file['name']);
        move_uploaded_file($file['tmp_name'], "uploads/" . $filename);

        // hapus poster lama bila ada
        if (!empty($data[$id]['poster']) && file_exists("uploads/" . $data[$id]['poster'])) {
            @unlink("uploads/" . $data[$id]['poster']);
        }

        $data[$id]['poster'] = $filename;
    }

    file_put_contents("db.json", json_encode($data, JSON_PRETTY_PRINT));
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Film</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        body { 
            background: #111; 
            margin:0;
            font-family:"Poppins";
        }
        .container { 
            max-width: 950px; 
            margin: 40px auto; 
            background: #1f1f1f; 
            padding: 30px; 
            border-radius: 14px; 
            color: white; 
        }
        .title { font-size: 30px; font-weight: bold; margin-bottom: 25px; }
        .edit-layout { display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 30px; align-items: start; }
        .form-group { display:flex; flex-direction:column; margin-bottom:12px; }
        label { margin-bottom:6px; }
        input, select { padding:10px; background:#333; border:none; color:white; border-radius:6px; }
        .poster-box { text-align:center; }
        .poster-box img { width:250px; height:360px; object-fit:cover; border-radius:10px; border:2px solid #333; }
        .btn-row { margin-top:25px; display:flex; justify-content:space-between; }
        .btn { padding:10px 18px; text-decoration:none; color:white; border-radius:6px; }
        .btn-save { background:green; border:none; cursor:pointer; }
        .btn-cancel { background:purple; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
    </style>
</head>
<body>


<div class="container">
    <div class="title">Edit Film</div>

    <form method="POST" enctype="multipart/form-data">
        <div class="edit-layout">
            <div>
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" value="<?= htmlspecialchars($film['judul']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Genre</label>
                    <select name="genre" required>
                        <?php
                        $genreList = ["Action","Drama","Komedi","Animasi"];
                        foreach ($genreList as $g) {
                            $sel = ($film['genre'] == $g) ? "selected" : "";
                            echo "<option value=\"" . htmlspecialchars($g) . "\" $sel>" . htmlspecialchars($g) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Rating</label>
                    <select name="rating" required>
                        <?php
                        $ratings = ["⭐","⭐⭐","⭐⭐⭐","⭐⭐⭐⭐","⭐⭐⭐⭐⭐"];
                        foreach ($ratings as $r) {
                            $sel = ($film['rating'] == $r) ? "selected" : "";
                            echo "<option value=\"" . htmlspecialchars($r) . "\" $sel>" . htmlspecialchars($r) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Ganti Poster</label>
                    <input type="file" name="poster" accept="image/*">
                </div>
            </div>

            <div class="poster-box">
                <label>Poster Saat Ini:</label><br>
                <img src="uploads/<?= htmlspecialchars($film['poster']) ?>" alt="poster">
            </div>
        </div>

        <div class="btn-row">
            <a href="index.php" class="btn btn-cancel">Batal</a>
            <button type="submit" class="btn btn-save">Simpan</button>
        </div>
    </form>
</div>

</body>
</html>
