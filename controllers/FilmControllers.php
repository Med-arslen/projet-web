<?php
include_once __DIR__ . '/../models/film.php'; 


include_once __DIR__ . '/../config/database.php'; 
class FilmController{
   

    public function getTitltPhotoFilm()
{
    $sql = "SELECT title,photo_path FROM film";
    $db = config::getConnexion();
    try {
        $liste = $db->query($sql);
        return $liste;
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}

}