<?php
include 'C:\xampp\htdocs\crudweb\config.php';
include 'C:\xampp\htdocs\crudweb\Controller\CommentaireController.php';

$controller = new CommentaireController($pdo);
$id = $_GET['id'] ?? null;

if ($id && $controller->deleteCommentaire($id)) {
    header("Location: commentairegestion.php");
    exit;
} else {
    echo "Failed to delete comment.";
}
?>
