<?php

Include 'DataBase.php';

// Lire le contenu du fichier JSON
$json_file = 'cities.json';
$json_data = file_get_contents($json_file);

if ($json_data === false) {
    die('Erreur lors de la lecture du fichier JSON.');
}

// Décoder le JSON en tableau associatif
$data = json_decode($json_data, true);

if ($data === null) {
    die('Erreur lors du décodage du JSON.');
}

if (!isset($data['cities'])) {
    die('Le fichier JSON ne contient pas de clé "cities".');
}

// Extraire le tableau des villes
$cities = $data['cities'];

$sql = "INSERT INTO Location (NameLocation, Country, Longitude, Latitude) VALUES (:name, :country, :longitude, :latitude)
        ON DUPLICATE KEY UPDATE Longitude = VALUES(Longitude), Latitude = VALUES(Latitude)";
$stmt = $pdo->prepare($sql);

//Boucle foreach sur toutes les villes de France
foreach ($cities as $city) {
    $name = $city['label'];
    $country = 'France';
    $longitude = (double) $city['longitude'];
    $latitude = (double) $city['latitude'];

    // Imprimer les valeurs pour le débogage
    echo "Nom : $name, Pays : $country, Longitude : $longitude, Latitude : $latitude<br>";

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':country', $country);
    $stmt->bindParam(':longitude', $longitude);
    $stmt->bindParam(':latitude', $latitude);

    try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo 'Erreur lors de l\'insertion des données : ' . $e->getMessage() . '<br>';
    }
}

?>