<?php

// Configuration de la base de données
$dsn = 'mysql:host=localhost;dbname=projetgreenit;charset=utf8';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur lors de la connexion à la base de données : ' . $e->getMessage());
}

?>