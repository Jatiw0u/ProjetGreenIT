<?php

include 'DataBase.php';
require '../vendor/autoload.php';

// Vérifier si la table electrical_demand est vide
$sql_check = "SELECT COUNT(*) FROM electrical_demand";
$stmt_check = $pdo->query($sql_check);
$row_count = $stmt_check->fetchColumn();

// Récupérer les coordonnées de chaque ville
$sql_locations = "SELECT IdLocation, Latitude, Longitude FROM location";
$stmt_locations = $pdo->query($sql_locations);
$locations = $stmt_locations->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour faire une requête à l'API et récupérer les données
function getElectricityData($lat, $lon) {

    $url = "https://api.electricitymap.org/v3/power-breakdown/latest?lat={-12.790231903}&lon={45.194781407}&nocache=" . time(); // Ajouter un paramètre pour éviter le cache
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

    $url = "https://api.electricitymap.org/v3/power-breakdown/latest?lat={$lat}&lon={$lon}&nocache=" . time(); // Ajouter un paramètre pour éviter le cache
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

// Préparer la requête d'insertion
$sql_insert = "INSERT INTO electrical_demand (DateTimeDemand, Id_Location, ValueNuclear, ValueGeothermal, ValueBiomass, ValueCoal, ValueWind, ValueSolar, ValueHydro, ValueGas, ValueOil, ValueUnknown, ValueHydroDischarge, ValueBatteryDischarge)
               VALUES (:datetime, :id_location, :nuclear, :geothermal, :biomass, :coal, :wind, :solar, :hydro, :gas, :oil, :unknown, :hydro_discharge, :battery_discharge)";
$stmt_insert = $pdo->prepare($sql_insert);

// Si la table est vide ou non
if ($row_count > 0) {
    // Si la table n'est pas vide, comparer les dates avant d'ajouter les données
    foreach ($locations as $location) {
        $id_location = $location['IdLocation'];
        $lat = $location['Latitude'];
        $lon = $location['Longitude'];

        // Récupérer la dernière date enregistrée pour cette ville
        $sql_last_datetime = "SELECT DateTimeDemand FROM electrical_demand WHERE Id_Location = :id_location ORDER BY DateTimeDemand DESC LIMIT 1";
        $stmt_last_datetime = $pdo->prepare($sql_last_datetime);
        $stmt_last_datetime->bindValue(':id_location', $id_location);
        $stmt_last_datetime->execute();
        $last_datetime = $stmt_last_datetime->fetchColumn();

        // Récupérer les données de l'API
        $data = getElectricityData($lat, $lon);

        // Ajouter un log pour vérifier la réponse de l'API
        echo "Données pour {$id_location} ({$lat}, {$lon}) : " . json_encode($data) . "<br>";

        if ($data && isset($data['powerConsumptionBreakdown'])) {
            // Conversion de la date et heure au format compatible MySQL
            $datetime = (new DateTime($data['datetime']))->format('Y-m-d H:i:s');

            // Vérifier si la date est différente de la dernière date enregistrée
            if ($datetime != $last_datetime) {
                $consumption = $data['powerConsumptionBreakdown'];

                // Lier les paramètres et exécuter la requête d'insertion
                $stmt_insert->bindParam(':datetime', $datetime);
                $stmt_insert->bindParam(':id_location', $id_location);
                $stmt_insert->bindParam(':nuclear', $consumption['nuclear']);
                $stmt_insert->bindParam(':geothermal', $consumption['geothermal']);
                $stmt_insert->bindParam(':biomass', $consumption['biomass']);
                $stmt_insert->bindParam(':coal', $consumption['coal']);
                $stmt_insert->bindParam(':wind', $consumption['wind']);
                $stmt_insert->bindParam(':solar', $consumption['solar']);
                $stmt_insert->bindParam(':hydro', $consumption['hydro']);
                $stmt_insert->bindParam(':gas', $consumption['gas']);
                $stmt_insert->bindParam(':oil', $consumption['oil']);
                $stmt_insert->bindParam(':unknown', $consumption['unknown']);
                $stmt_insert->bindParam(':hydro_discharge', $consumption['hydro discharge']);
                $stmt_insert->bindParam(':battery_discharge', $consumption['battery discharge']);

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
        $data = getElectricityData($lat, $lon);

        // Ajouter un log pour vérifier la réponse de l'API
        echo "Données pour {$id_location} ({$lat}, {$lon}) : " . json_encode($data) . "<br>";

        if ($data && isset($data['powerConsumptionBreakdown'])) {
            // Conversion de la date et heure au format compatible MySQL
            $datetime = (new DateTime($data['datetime']))->format('Y-m-d H:i:s');
            $consumption = $data['powerConsumptionBreakdown'];

            // Lier les paramètres et exécuter la requête d'insertion
            $stmt_insert->bindParam(':datetime', $datetime);
            $stmt_insert->bindParam(':id_location', $id_location);
            $stmt_insert->bindParam(':nuclear', $consumption['nuclear']);
            $stmt_insert->bindParam(':geothermal', $consumption['geothermal']);
            $stmt_insert->bindParam(':biomass', $consumption['biomass']);
            $stmt_insert->bindParam(':coal', $consumption['coal']);
            $stmt_insert->bindParam(':wind', $consumption['wind']);
            $stmt_insert->bindParam(':solar', $consumption['solar']);
            $stmt_insert->bindParam(':hydro', $consumption['hydro']);
            $stmt_insert->bindParam(':gas', $consumption['gas']);
            $stmt_insert->bindParam(':oil', $consumption['oil']);
            $stmt_insert->bindParam(':unknown', $consumption['unknown']);
            $stmt_insert->bindParam(':hydro_discharge', $consumption['hydro discharge']);
            $stmt_insert->bindParam(':battery_discharge', $consumption['battery discharge']);

            try {
                $stmt_insert->execute();
            } catch (PDOException $e) {
                echo 'Erreur lors de l\'insertion des données : ' . $e->getMessage() . '<br>';
            }
        }

        // Ajouter un délai entre les requêtes pour éviter les problèmes de taux de requêtes ou de mise en cache
        sleep(1); // Délai de 10 secondes entre chaque requête
    }

    echo 'Insertion des données terminée avec succès.';
}

?>