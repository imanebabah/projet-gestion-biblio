<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'administrateur') {
    header("Location: connexion.php");
    exit();
}

require 'database.php';

echo "<h1>Admin Dashboard</h1>";
echo "<h2>Manage Books</h2>";

$sql = "SELECT * FROM livres";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($books as $book) {
    echo "<div class='book'>";
    echo "<p><strong>" . htmlspecialchars($book['titre']) . "</strong> by " . htmlspecialchars($book['auteur']) . "</p>";
    echo "<p><a href='edit_book.php?id=" . $book['id'] . "' class='btn'>Edit</a>";
    echo "<a href='delete_book.php?id=" . $book['id'] . "' class='btn delete'>Delete</a></p>";
    echo "</div>";
}

echo "<p><a href='add_book.php' class='btn add'>Add New Book</a></p>";
?>