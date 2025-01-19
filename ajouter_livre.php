<?php
session_start();

if (!isset($_SESSION['type_utilisateur']) || $_SESSION['type_utilisateur'] !== 'administrateur') {
    header("Location: connexion.php");
    exit();
}

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $disponibilite = 1; 

    $sql = "INSERT INTO livres (titre, auteur, disponibilite) VALUES (:titre, :auteur, :disponibilite)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':titre', $titre);
    $stmt->bindParam(':auteur', $auteur);
    $stmt->bindParam(':disponibilite', $disponibilite);
    $stmt->execute();

    header("Location: tableau_de_bord.php?message=livre_ajoute");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Livre</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Ajouter un Livre</h1>
    <form method="POST">
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" required>
        <label for="auteur">Auteur :</label>
        <input type="text" id="auteur" name="auteur" required>
        <button type="submit">Ajouter</button>
    </form>
</body>
</html>