
<?php
if (isset($_GET['id'])) {
    $admin = $adminModel->getById($_GET['id']);
    $action = "update";
} else {
    $admin = ['id' => '', 'name' => '', 'email' => '', 'password' => ''];
    $action = "create";
}
?>
<form method="POST">
    <input type="hidden" name="id" value="<?= $admin['id'] ?>">
    <label>Nom:</label><input type="text" name="name" value="<?= $admin['name'] ?>"><br>
    <label>Email:</label><input type="email" name="email" value="<?= $admin['email'] ?>"><br>
    <label>Mot de passe:</label><input type="password" name="password"><br>
    <button type="submit" name="<?= $action ?>"><?= ucfirst($action) ?></button>
</form>
