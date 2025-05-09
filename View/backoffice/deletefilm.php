<?php
include 'C:\xampp\htdocs\crudweb\config.php';
include 'C:\xampp\htdocs\crudweb\Controller\FilmController.php';
include 'C:\xampp\htdocs\crudweb\Controller\HistoriqueController.php';

$controller = new FilmController($pdo);
$historiqueController = new HistoriqueController($pdo);
$id = $_GET['id'] ?? null;

if ($id) {
    $film = $controller->getFilmById($id);
    if ($film && $controller->deletefilm($id)) {
        $historiqueController->addHistorique(
            'suppression',
            $id,
            $film->getTitre(),
            "Film supprimé: " . $film->getTitre()
        );
        header("Location: index.php");
        exit;
    }
}
echo "Failed to delete film.";
?>
