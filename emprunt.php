<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

require 'database.php';

if (isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']); // Récupérer l'ID du livre
    $user_id = $_SESSION['user_id'];

    // Vérifier si le livre est disponible
    $sql = "SELECT disponibilite FROM livres WHERE id = :book_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book && $book['disponibilite'] == 1) {
        // Marquer le livre comme emprunté
        $sql = "UPDATE livres SET disponibilite = 0 WHERE id = :book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $stmt->execute();

        // Ajouter une entrée dans la table des emprunts
        $sql = "INSERT INTO emprunts (utilisateur_id, livre_id, date_emprunt) VALUES (:user_id, :book_id, CURDATE())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $stmt->execute();

        echo "Livre emprunté avec succès !";
    } else {
        echo "Ce livre n'est pas disponible.";
    }
} else {
    echo "Aucun livre spécifié.";
}
if ($book && $book['disponibilite'] == 1) {
    // Marquer le livre comme emprunté
    $sql = "UPDATE livres SET disponibilite = 0 WHERE id = :book_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->execute();

    // Calculer la date de retour prévue (14 jours après la date actuelle)
    $date_retour = date('Y-m-d', strtotime('+14 days'));

    // Ajouter une entrée dans la table des emprunts
    $sql = "INSERT INTO emprunts (utilisateur_id, livre_id, date_emprunt, date_retour) 
            VALUES (:user_id, :book_id, CURDATE(), :date_retour)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->bindParam(':date_retour', $date_retour, PDO::PARAM_STR);
    $stmt->execute();

    echo "Date de retour prévue : " . $date_retour;
} else {
    echo "Ce livre n'est pas disponible.";
}