<?php
    /**
     * Mise a jour des arrhes d'une reservation
     * seul l'admin peut le faire
     **/

    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        exit("Accès refusé, vous n'êtes pas un admin");
    }

    // récupération des données envoyées par AJAX

    $id_res = $_POST['id_res'] ?? null;
    $montant_arrhes = $_POST['montant_arrhes'] ?? null;

    // validation des données
    if ($id_res === null || $montant_arrhes === null || $montant_arrhes === '') {
        exit("Données manquantes.");
    }

    if (!is_numeric($montant_arrhes)) {
        exit("Montant invalide.");
    }

    // conversion en float et validation du montant
    $montant_arrhes = (float)$montant_arrhes;
    if ($montant_arrhes < 0) {
        exit("Le montant des arrhes ne peut pas être négatif.");
    }

    // récupération des résercation 
    $path_reservation = "../data/reservation.json";

    $json = file_get_contents($path_reservation);
    $reservations = json_decode($json, true) ?: [];

    $trouve = false;

    // mise à jour de la reservation avec le montant des arrhes
    foreach ($reservations as $key => $reservation) {
        if ($reservation['id_res'] == $id_res) {
            $reservations[$key]['arrhes'] = $montant_arrhes;
            $trouve = true;
            break;
        }
    }


    // si on trouve pas c'est qu'il y a un problème avec l'id de la reservation
    if (!$trouve) {
        exit("Réservation introuvable.");
    }

    // ouverture du fichier en mode écriture pour mettre à jour les données
    $flockOp = fopen($path_reservation, 'w');
    // on a qu'un seul admin mais on suppose que plusieurs peuvent faire des modifications en même temps, donc on utilise un verrou pour éviter les conflits d'écriture
    if ($flockOp) {
        if (flock($flockOp, LOCK_EX)) {
            fwrite($flockOp, json_encode($reservations, JSON_PRETTY_PRINT));
            flock($flockOp, LOCK_UN);
            echo "success";
        } else {
            echo "Fichier occupé.";
        }
        fclose($flockOp);
    } else {
        echo "Impossible d'ouvrir le fichier.";
    }

    exit;
?>