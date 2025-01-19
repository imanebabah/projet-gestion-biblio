<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['type_utilisateur'] !== 'administrateur') {
    header("Location: connexion.php");
    exit();
}

require 'database.php';

// Récupérer les données du formulaire
$id = $_POST['id'];
$nouveau_type = $_POST['nouveau_type'];

// Mettre à jour le type d'utilisateur dans la base de données
$sql = "UPDATE utilisateurs SET type_utilisateur = :nouveau_type WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':nouveau_type', $nouveau_type, PDO::PARAM_STR);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

header("Location: gestion_utilisateurs.php");
exit();