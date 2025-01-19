<?php
session_start();
if ($_SESSION['user_type'] !== 'administrateur') {
    header("Location: tableau_de_bord.php");
    exit();
}
require 'database.php';

// Supprimer le livre
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    $sql = "DELETE FROM livres WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $book_id, PDO::PARAM_INT);
    $stmt->execute();

    echo "Livre supprimé avec succès !";
    header("Location: tableau_de_bord.php");
    exit();
}

// Afficher les livres disponibles pour suppression
$sql = "SELECT * FROM livres";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Supprimer un livre</h2>
<?php if (!empty($books)): ?>
    <ul>
        <?php foreach ($books as $book): ?>
            <li>
                <?php echo htmlspecialchars($book['titre']) . " par " . htmlspecialchars($book['auteur']); ?>
                <a href="supprimer_livre.php?id=<?php echo $book['id']; ?>">Supprimer</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Aucun livre trouvé.</p>
<?php endif; ?>
<a href="tableau_de_bord.php">Retour au tableau de bord</a>
<link rel="stylesheet" href="styles.css">
<div class="container">
    <h2>Supprimer un livre</h2>
    <?php if (!empty($books)): ?>
        <ul>
            <?php foreach ($books as $book): ?>
                <li>
                    <?php echo htmlspecialchars($book['titre']) . " par " . htmlspecialchars($book['auteur']); ?>
                    <a href="supprimer_livre.php?id=<?php echo $book['id']; ?>">Supprimer</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun livre trouvé.</p>
    <?php endif; ?>
    <a href="tableau_de_bord.php">Retour au tableau de bord</a>
</div>