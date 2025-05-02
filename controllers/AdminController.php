
<?php
require_once 'models/Admin.php';
require_once '../config/Database.php';
$adminModel = new Admin($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $adminModel->create($_POST['name'], $_POST['email'], $_POST['password']);
    } elseif (isset($_POST['update'])) {
        $adminModel->update($_POST['id'], $_POST['name'], $_POST['email'], $_POST['password']);
    }
    header('Location: index.php?view=admin');
    exit;
}
if (isset($_GET['delete'])) {
    $adminModel->delete($_GET['delete']);
    header('Location: index.php?view=admin');
    exit;
}
$admins = $adminModel->getAll();
include 'views/admin/list.php';
