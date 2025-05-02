
<?php
if (isset($_GET['id'])) {
    $user = $userModel->getById($_GET['id']);
    $action = "update";
} else {
    $user = ['id' => '', 'admin_id' => '', 'name' => '', 'email' => '', 'password' => ''];
    $action = "create";
}
?>
<form method="POST">
    <input type="hidden" name="id" value="<?= $user['id'] ?>">
    <label>ID Admin:</label><input type="text" name="admin_id" value="<?= $user['admin_id'] ?>"><br>
    <label>Nom:</label><input type="text" name="name" value="<?= $user['name'] ?>"><br>
    <label>Email:</label><input type="email" name="email" value="<?= $user['email'] ?>"><br>
    <label>Mot de passe:</label><input type="password" name="password"><br>
    <button type="submit" name="<?= $action ?>"><?= ucfirst($action) ?></button>
</form>
