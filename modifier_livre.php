<?php
require 'database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $titre = htmlspecialchars($_POST['titre']);
    $auteur = htmlspecialchars($_POST['auteur']);
    
    $sql = "UPDATE livres SET titre = :titre, auteur = :auteur WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
    $stmt->bindParam(':auteur', $auteur, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo "Livre modifié avec succès.";
    } else {
        echo "Erreur lors de la modification.";
    }
}

// modifier un livre
?>
<form method="POST" action="">
    <input type="hidden" name="id" value="1"> <!-- Remplacez par l'ID du livre -->
    <input type="text" name="titre" placeholder="Nouveau titre">
    <input type="text" name="auteur" placeholder="Nouvel auteur">
    <button type="submit">Modifier</button>
</form>