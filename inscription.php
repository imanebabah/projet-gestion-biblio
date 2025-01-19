<?php
require 'database.php'; // Connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $type = 'utilisateur'; // Forcer le type à 'utilisateur'

    // Vérifier si l'email existe déjà
    $sql = "SELECT * FROM utilisateurs WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "Cet email est déjà utilisé. Veuillez en choisir un autre.";
    } else {
        // Insérer le nouvel utilisateur dans la base de données
        $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, type_utilisateur) VALUES (:nom, :email, :mot_de_passe, :type)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);

        if ($stmt->execute()) {
            session_start();
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['nom'] = $nom;
            $_SESSION['type_utilisateur'] = $type;

            echo "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
            header("Location: connexion.html");
            exit();
        } else {
            echo "Erreur lors de la création du compte. Veuillez réessayer.";
        }
    }
}
?>