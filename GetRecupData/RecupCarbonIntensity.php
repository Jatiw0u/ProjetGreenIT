<?php

include 'DataBase.php';
require '../vendor/autoload.php';

// Vérifier si la table carbone_intensity est vide
$sql_check = "SELECT COUNT(*) FROM carbone_intensity";
$stmt_check = $pdo->query($sql_check);
$row_count = $stmt_check->fetchColumn();

// Récupérer les coordonnées de chaque ville
$sql_locations = "SELECT IdLocation, Latitude, Longitude FROM location";
$stmt_locations = $pdo->query($sql_locations);
$locations = $stmt_locations->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour faire une requête à l'API et récupérer les données de carbon intensity
function getCarbonIntensityData($lat, $lon) {
    $url = "https://api.electricitymap.org/v3/carbon-intensity/latest?lat={$lat}&lon={$lon}&nocache=" . uniqid(); // Ajouter un paramètre pour éviter le cache
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Cache-Control: no-cache',
        'Pragma: no-cache',
        'Cache-Control: no-store',
        'Expires: 0',
        'Random-Header: ' . uniqid()  // Ajouter un en-tête aléatoire pour éviter le cache
    ));
    curl_setopt($ch, CURLOPT_COOKIEFILE, '');  // Désactiver l'utilisation des cookies
    curl_setopt($ch, CURLOPT_COOKIEJAR, '');   // Désactiver le stockage des cookies

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Erreur cURL : ' . curl_error($ch) . '<br>';
        return null;
    }

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode != 200) {
        echo "Erreur HTTP : {$httpcode} pour la requête {$url}<br>";
        return null;
    }

    curl_close($ch);
    return json_decode($response, true);
}

// Préparer la requête d'insertion pour la table carbone_intensity
$sql_insert = "INSERT INTO carbone_intensity (IdLocation, Value, DateTimeIntensity, EmissionFactorType)
               VALUES (:id_location, :value, :datetime, :emission_factor_type)";
$stmt_insert = $pdo->prepare($sql_insert);

// Si la table est vide ou non
if ($row_count > 0) {
    // Si la table n'est pas vide, comparer les dates avant d'ajouter les données
    foreach ($locations as $location) {
        $id_location = $location['IdLocation'];
        $lat = $location['Latitude'];
        $lon = $location['Longitude'];

        // Récupérer la dernière date enregistrée pour cette ville
        $sql_last_datetime = "SELECT DateTimeIntensity FROM carbone_intensity WHERE IdLocation = :id_location ORDER BY DateTimeIntensity DESC LIMIT 1";
        $stmt_last_datetime = $pdo->prepare($sql_last_datetime);
        $stmt_last_datetime->bindValue(':id_location', $id_location);
        $stmt_last_datetime->execute();
        $last_datetime = $stmt_last_datetime->fetchColumn();

        // Récupérer les données de l'API
        $data = getCarbonIntensityData($lat, $lon);

        // Ajouter un log pour vérifier la réponse de l'API
        echo "Données pour {$id_location} ({$lat}, {$lon}) : " . json_encode($data) . "<br>";

        if ($data && isset($data['carbonIntensity'])) {
            // Conversion de la date et heure au format compatible MySQL
            $datetime = (new DateTime($data['datetime']))->format('Y-m-d H:i:s');

            // Vérifier si la date est différente de la dernière date enregistrée
            if ($datetime != $last_datetime) {
                $value = $data['carbonIntensity'];
                $emission_factor_type = $data['emissionFactorType'];

                // Lier les paramètres et exécuter la requête d'insertion
                $stmt_insert->bindParam(':id_location', $id_location);
                $stmt_insert->bindParam(':value', $value);
                $stmt_insert->bindParam(':datetime', $datetime);
                $stmt_insert->bindParam(':emission_factor_type', $emission_factor_type);

                try {
                    $stmt_insert->execute();
                } catch (PDOException $e) {
                    echo 'Erreur lors de l\'insertion des données : ' . $e->getMessage() . '<br>';
                }
            }
        }

        // Ajouter un délai entre les requêtes pour éviter les problèmes de taux de requêtes ou de mise en cache
        sleep(1); // Délai de 1 seconde entre chaque requête
    }

    echo 'Insertion des données terminée avec succès.';
} else {
    // Si la table est vide, insérer toutes les données sans vérification
    foreach ($locations as $location) {
        $id_location = $location['IdLocation'];
        $lat = $location['Latitude'];
        $lon = $location['Longitude'];

        // Récupérer les données de l'API
        $data = getCarbonIntensityData($lat, $lon);

        // Ajouter un log pour vérifier la réponse de l'API
        echo "Données pour {$id_location} ({$lat}, {$lon}) : " . json_encode($data) . "<br>";

        if ($data && isset($data['carbonIntensity'])) {
            // Conversion de la date et heure au format compatible MySQL
            $datetime = (new DateTime($data['datetime']))->format('Y-m-d H:i:s');
            $value = $data['carbonIntensity'];
            $emission_factor_type = $data['emissionFactorType'];

            // Lier les paramètres et exécuter la requête d'insertion
            $stmt_insert->bindParam(':id_location', $id_location);
            $stmt_insert->bindParam(':value', $value);
            $stmt_insert->bindParam(':datetime', $datetime);
            $stmt_insert->bindParam(':emission_factor_type', $emission_factor_type);

            try {
                $stmt_insert->execute();
            } catch (PDOException $e) {
                echo 'Erreur lors de l\'insertion des données : ' . $e->getMessage() . '<br>';
            }
        }

        // Ajouter un délai entre les requêtes pour éviter les problèmes de taux de requêtes ou de mise en cache
        sleep(1); // Délai de 1 seconde entre chaque requête
    }

    echo 'Insertion des données terminée avec succès.';
}

?>