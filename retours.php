<?php
require 'database.php';
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

// Vérifiez si le paramètre "book_id" est présent dans l'URL
if (isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']); // Sécurisation de l'ID du livre
    $user_id = $_SESSION['user_id'];

    // Vérifiez si ce livre a été emprunté par cet utilisateur
    $sql = "SELECT * FROM emprunts WHERE livre_id = :book_id AND utilisateur_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Supprimez l'entrée dans la table des emprunts
        $sql = "DELETE FROM emprunts WHERE livre_id = :book_id AND utilisateur_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Marquez le livre comme disponible
        $sql = "UPDATE livres SET disponibilite = 1 WHERE id = :book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $stmt->execute();

        echo "Vous avez retourné le livre avec succès !";
    } else {
        echo "Ce livre ne fait pas partie de vos emprunts.";
    }
} else {
    echo "Aucun livre spécifié pour le retour.";
}
?>