<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

// Vérifiez si l'utilisateur est administrateur
if ($_SESSION['type_utilisateur'] !== 'administrateur') {
    echo "Accès refusé. Vous n'êtes pas autorisé à accéder à cette page.";
    echo '<br><a href="tableau_de_bord.php">Retour au tableau de bord</a>';
    exit();
}

// Connexion à la base de données
require 'database.php';

// Récupérer tous les utilisateurs
$sql = "SELECT id, email, type_utilisateur FROM utilisateurs";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestion des utilisateurs</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Type d'utilisateur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['type_utilisateur']); ?></td>
                        <td>
                            <?php if ($user['type_utilisateur'] !== 'administrateur'): ?>
                                <form action="changer_type_utilisateur.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                    <select name="nouveau_type">
                                        <option value="utilisateur">Utilisateur</option>
                                        <option value="administrateur">Administrateur</option>
                                    </select>
                                    <button type="submit">Changer</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <a href="tableau_de_bord.php">Retour au tableau de bord</a>
    </div>
</body>
</html>