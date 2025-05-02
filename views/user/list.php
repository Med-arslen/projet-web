
<h2>Liste des Utilisateurs</h2>
<a href="index.php?view=user&form=new">Ajouter un Utilisateur</a>
<table border="1">
    <tr><th>Nom</th><th>Email</th><th>Actions</th></tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= $user['name'] ?></td>
        <td><?= $user['email'] ?></td>
        <td>
            <a href="index.php?view=user&form=edit&id=<?= $user['id'] ?>">Modifier</a>
            <a href="index.php?view=user&delete=<?= $user['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
