<?php
session_start();
require 'database.php'; 

// Vérifiez si l'utilisateur est connecté
// Toujours charger depuis la base pour éviter les erreurs
$sql = "SELECT nom, type_utilisateur FROM utilisateurs WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

try {
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['nom'] = trim($user['nom']);
        $_SESSION['type_utilisateur'] = trim($user['type_utilisateur']);
    } else {
        echo "Erreur : Aucun utilisateur trouvé avec cet ID.";
        exit();
    }
} catch (PDOException $e) {
    echo "Erreur lors de la requête : " . $e->getMessage();
    exit();
}

// Charger les informations de l'utilisateur dans la session si non déjà chargé
if (!isset($_SESSION['type_utilisateur'])) {
    $sql = "SELECT nom, type_utilisateur FROM utilisateurs WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

    try {
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['type_utilisateur'] = $user['type_utilisateur'];
        } else {
            echo "Erreur : Aucun utilisateur trouvé avec cet ID.";
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la requête : " . $e->getMessage();
        exit();
    }
}

// Affichage du tableau de bord
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Bibliothèque</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="tableau_de_bord.css">
</head>
<body>
    <!-- En-tête -->
    <header>
        <div class="container">
            <h1>Gestion de Bibliothèque</h1>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="connexion.php">Connexion</a></li>
            <li><a href="deconnexion.php">Se déconnecter</a></li>
        </ul>
    </nav>

    <!-- Contenu principal -->
    <main class="container">
        <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['nom']); ?> !</h2>

        <!-- Section admin -->
        <?php if ($_SESSION['type_utilisateur'] === 'administrateur'): ?>
    <h3>Options d'administration</h3>
    <ul>
        <li><a href="ajouter_livre.php">Ajouter un livre</a></li>
        <li><a href="modifier_livre.php">Modifier un livre</a></li>
        <li><a href="supprimer_livre.php">Supprimer un livre</a></li>
        <li><a href="gestion_utilisateurs.php">Gérer les utilisateurs</a></li>
    </ul>
<?php endif; ?>

        <!-- Section des livres -->
        <section>
    <h3>Livres disponibles</h3>
    <ul>
        <?php
        // Récupérer les livres et leurs emprunteurs actuels
        $sql = "
            SELECT livres.id, livres.titre, livres.auteur, livres.disponibilite, emprunts.utilisateur_id
            FROM livres
            LEFT JOIN emprunts ON livres.id = emprunts.livre_id AND emprunts.date_retour IS NULL
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($books) {
            foreach ($books as $book) {
                echo "<li>";
                echo htmlspecialchars($book['titre']) . " par " . htmlspecialchars($book['auteur']);
                
                // Livre disponible
                if ($book['disponibilite'] == 1 && !$book['utilisateur_id']) {
                    echo " <a href='emprunt.php?book_id=" . $book['id'] . "' class='btn emprunter'>Emprunter</a>";
                }
                // Livre emprunté par l'utilisateur connecté
                elseif ($book['utilisateur_id'] == $_SESSION['user_id']) {
                    echo " <a href='retours.php?book_id=" . $book['id'] . "' class='btn retourner'>Retourner</a>";
                }
                // Livre emprunté par un autre utilisateur
                else {
                    echo " (Indisponible)";
                }

                echo "</li>";
            }
        } else {
            echo "<p>Aucun livre disponible pour le moment.</p>";
        }
        ?>
    </ul>
</section>
<section>
<h3>Historique de vos emprunts</h3>
<table>
    <thead>
        <tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Date d'emprunt</th>
            <th>Date de retour prévue</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT livres.titre, livres.auteur, emprunts.date_emprunt, emprunts.date_retour
                FROM emprunts
                JOIN livres ON emprunts.livre_id = livres.id
                WHERE emprunts.utilisateur_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($emprunts) {
            
            foreach ($emprunts as $emprunt) {
                $titre = htmlspecialchars($emprunt['titre'] ?? 'Non spécifié');
                $auteur = htmlspecialchars($emprunt['auteur'] ?? 'Non spécifié');
                $date_emprunt = htmlspecialchars($emprunt['date_emprunt'] ?? 'Non spécifié');
                $date_retour = htmlspecialchars($emprunt['date_retour'] ?? 'Non spécifié');

                echo "<tr>";
                echo "<td>" . $titre . "</td>";
                echo "<td>" . $auteur . "</td>";
                echo "<td>" . $date_emprunt . "</td>";
                echo "<td>" . $date_retour . "</td>";
                echo "</tr>";
            }

        } else {
            echo "<tr><td colspan='4'>Aucun emprunt trouvé.</td></tr>";
        }
        ?>
    </tbody>
</table>
</section>
<section>
<h3>Emprunts en retard</h3>
<table>
    <thead>
        <tr>
            <th>Utilisateur</th>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Date de retour prévue</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT utilisateurs.nom AS utilisateur, livres.titre, livres.auteur, emprunts.date_retour
                FROM emprunts
                JOIN livres ON emprunts.livre_id = livres.id
                JOIN utilisateurs ON emprunts.utilisateur_id = utilisateurs.id
                WHERE emprunts.date_retour < CURDATE() AND livres.disponibilite = 0";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $emprunts_en_retard = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($emprunts_en_retard) {
            foreach ($emprunts_en_retard as $retard) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($retard['utilisateur']) . "</td>";
                echo "<td>" . htmlspecialchars($retard['titre']) . "</td>";
                echo "<td>" . htmlspecialchars($retard['auteur']) . "</td>";
                echo "<td>" . htmlspecialchars($retard['date_retour']) . "</td>";
                echo "<td>En retard</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucun emprunt en retard.</td></tr>";
        }
        ?>
    </tbody>
</table>
</section>
    </main>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2025 Bibliothèque. Tous droits réservés.</p>
    </footer>
</body>
</html>