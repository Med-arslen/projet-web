
<h2>Liste des Admins</h2>
<a href="index.php?view=admin&form=new">Ajouter un Admin</a>
<table border="1">
    <tr><th>Nom</th><th>Email</th><th>Actions</th></tr>
    <?php foreach ($admins as $admin): ?>
    <tr>
        <td><?= $admin['name'] ?></td>
        <td><?= $admin['email'] ?></td>
        <td>
            <a href="index.php?view=admin&form=edit&id=<?= $admin['id'] ?>">Modifier</a>
            <a href="index.php?view=admin&delete=<?= $admin['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
