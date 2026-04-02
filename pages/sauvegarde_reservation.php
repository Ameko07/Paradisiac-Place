<?php
    // récupération des données de réservation 
    // des variable pour stocker les données de la reservation 
    $deb = new DateTime($_POST['date_debut']); // date de début
    $depart = new DateTime($_POST['date_fin']); // date de fin

    if ($deb > $depart) {
        echo "La date de début doit être antérieure à la date de fin.";
        exit;
    }
    $nom = $_POST['nom'] ?? ''; // Utilisation de l'opérateur de coalescence nulle pour éviter les erreurs si la clé n'existe pas
    $email = $_POST['email'] ?? '';
    $date_arriv = $_POST['date_debut'] ?? '';
    $date_depart = $_POST['date_fin'] ?? '';
    $nb_pers = (int)($_POST['nb_pers'] ?? 1); // Par défaut, on suppose qu'il y a au moins une personne
    $choix_chamb = (int)($_POST['chambre_choisie'] ?? null);
    $commentaire = $_POST['message'] ?? '';


    
    // le chemin du fichier de réservation
    $path = "../data/reservation.json";

    

    $reservations_exist = [];
    if (file_exists($path)) {
        $json = file_get_contents($path);
        $reservations_exist = json_decode($json, true) ?: []; // au cas ou le fichier est vide 
    }

    // un nouvel id pour la réservation
    $new_id = 1; // Par défaut, on commence à 1
    if(!empty($reservations_exist)){
        $last_reservation = end($reservations_exist); // Récupérer la dernière réservation du tableau
        $new_id = ($last_reservation['id_res'] ?? 0) + 1; // Incrémentation de l'id en prenant le dernier id + 1


    }
    
    // Liste des préstations choisies

    $presations_choisies = isset($_POST['prestations'])
     ? array_map('intval', $_POST['prestations']) : [];
    // un tableau de données pour la réservation 
    // affectation Clé valeur pour chaque donnée de la reservation 

    $new_resrvation = [

    
        "id_res" => $new_id,
        "nom" => $nom,
        "email" => $email,
        "date_debut" => $date_arriv,
        "date_fin" => $date_depart,
        "nb_pers" => (int)$nb_pers,
        "chambre_choisie" => (int)$choix_chamb,
        "prestations" => $presations_choisies,
        "message" => $commentaire,
        "reduction" => 0,
        "arrhes" => 0,
        "status" => "en attente"


    ];
    

    
    //  on va ajouter la nouvelle réservation dans le tableau des réservations
    $reservations_exist[] = $new_resrvation;


    // Ecriture avec la sécurité flock pour ne pas avoir des conflit (pas vraiment utile car on est sur une seule page de réservation))
    // pas nécéssaire mais je fais quand même 
    
    $flockOp = fopen($path, 'w');
    if ($flockOp) {
        if (flock($flockOp, LOCK_EX)) { // Verrouillage exclusif : permet d'empêcher les autres processus d'écrire en même temps que nous 
            $donnees_finales = json_encode($reservations_exist, JSON_PRETTY_PRINT); // Encodage du tableau de réservations en JSON avec une mise en forme lisible
            fwrite($flockOp, $donnees_finales); // Ecriture du tableau MAJ en Json dans le fichier reservation.json
            flock($flockOp, LOCK_UN); // Libération : les autres processus peuvent de nouveaux écrire

            // nettoyage des caractères de sortie pour éviter les erreurs dans le main.js lors de la réception de la réponse
            ob_clean(); 

            echo "success";
            fclose($flockOp); // Fermeture du fichier
            exit; // Terminer le script pour éviter tout autre output
        } else {
            echo "Fichier occupé.";
        }
        fclose($flockOp); // Fermeture du fichier
    } else {
        echo "Impossible d'ouvrir le fichier.";
    }



 