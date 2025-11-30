<?php
class Film {
    public $judul;
    public $genre;
    public $poster;
    public $rating;
    public $favorite;
    public $updated_at;

    public function __construct($judul, $genre, $poster, $rating, $favorite=false) {
        $this->judul = $judul;
        $this->genre = $genre;
        $this->poster = $poster;
        $this->rating = $rating;
        $this->favorite = $favorite;
        $this->updated_at = time();
    }
}
