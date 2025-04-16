<?php
include 'C:\xampp\htdocs\backoffice\config.php';
include 'C:\xampp\htdocs\backoffice\Controller\FilmController.php';


$controller = new FilmController($pdo);
$id = $_GET['id'] ?? null;

if ($id && $controller->deletefilm($id)) {
    header("Location: index.php");
    exit;
} else {
    echo "Failed to delete user.";
}
?>
