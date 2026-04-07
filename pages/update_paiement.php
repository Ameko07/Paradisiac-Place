<?php
    /**
     * Mise a jour des arrhes et reduction d'une reservation 
     * seul l'admin peut le faire
     **/

    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        exit("Accès refusé, vous n'êtes pas un admin");
    }

    // récupération des données envoyées par AJAX

    $id_res = $_POST['id_res'] ?? null;
    $montant_arrhes = isset($_POST['montant_arrhes']) && $_POST['montant_arrhes'] !== '' ? (float)$_POST['montant_arrhes'] : null;
    // compatibilité entre ancien nom et nouveau nom du champ reduction
    $reduction_brute = $_POST['pourcentage_reduc'] ?? ($_POST['pourcentage_reduction'] ?? null);
    $new_reduction = ($reduction_brute !== null && $reduction_brute !== '') ? (float)$reduction_brute : null;

    // Condition de validation de toutes les données reçues
    // les donnees null recu
    if ($id_res === null || $montant_arrhes === null || $montant_arrhes === '') {
        exit("Données manquantes.");
    }

    // il faut des nombre pour les montants et la reduction
    if (!is_numeric($montant_arrhes)) {
        exit("Montant invalide.");
    }

    // pas de montant d'arrhes négatif et la reduction doit être entre 0 et 100
    if ($montant_arrhes < 0) {
        exit("Le montant des arrhes ne peut pas être négatif.");
    }

    // les resductions autorisées sont 0, 10, 20 et 50% 
    if ($new_reduction !== null) {
        $reductions_autorisees = [0.0, 10.0, 20.0, 50.0];
        if (!in_array($new_reduction, $reductions_autorisees, true)) {
            exit("Réduction invalide. Valeurs autorisées : 0, 10, 20, 50.");
        }
    }

    // récupération des résercation 
    $path_reservation = "../data/reservation.json";

    $json = file_get_contents($path_reservation);
    $reservations = json_decode($json, true) ?: [];

    $trouve = false;

    // mise à jour de la reservation avec le montant des arrhes
    foreach ($reservations as $key => $reservation) {
        if ($reservation['id_res'] == $id_res) {
            // on met à jour le montant des arrhes dans la reservation 
            // si une valeure saisie
            if ($montant_arrhes!== null) {
                $reservations[$key]['arrhes'] = $montant_arrhes;
            }
            if ($new_reduction !== null) {
                $reservations[$key]['reduction'] = $new_reduction;
            }
            
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