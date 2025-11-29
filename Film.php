<?php
class Film {
    public $judul;
    public $genre;
    public $poster;
    public $favorite;

    public function __construct($judul, $genre, $poster, $favorite = false) {
        $this->judul = $judul;
        $this->genre = $genre;
        $this->poster = $poster;
        $this->favorite = $favorite;
    }
}
?>
