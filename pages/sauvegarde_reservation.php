<?php
    // récupération des données de réservation 
    // des variable pour stocker les données de la reservation 
    $nom = $_POST['nom'] ?? ''; // Utilisation de l'opérateur de coalescence nulle pour éviter les erreurs si la clé n'existe pas
    $email = $_POST['email'] ?? '';
    $date_arriv = $_POST['date_debut'] ?? '';
    $date_depart = $_POST['date_fin'] ?? '';
    $nb_pers = $_POST['nb_pers'] ?? 1; // Par défaut, on suppose qu'il y a au moins une personne
    $choix_chamb = $_POST['chambre_choisie'] ?? null;
    $commentaire = $_POST['message'] ?? '';

    
    // le chemin du fichier de réservation
    $path = "../data/reservation.json";

    

    $reservations = [];
    if (file_exists($path)) {
        $json = file_get_contents($path);
        $reservations = json_decode($json, true) ?: []; // au cas ou le fichier est vide 
    }

    // un nouvel id pour la réservation
    $new_id = 1; // Par défaut, on commence à 1
    if(!empty($reservations)){
        $last_reservation = end($reservations);
        $new_id = ($last_reservation['id_res'] ?? 0) + 1; // Incrémentation de l'id en prenant le dernier id + 1


    }
        
    // un tableau de données pour la réservation 
    $new_resrvation = [

    // affectation Clé valeur pour chaque donnée de la reservation 
        "id_res" => $new_id,
        "nom" => $nom,
        "email" => $email,
        "date_debut" => $date_arriv,
        "date_fin" => $date_depart,
        "nb_pers" => $nb_pers,
        "chambre_choisie" => $choix_chamb,
        "message" => $commentaire,
        "status" => "en attente"


    ];
    

    
    //  on va ajouter la nouvelle réservation dans le tableau des réservations
    $reservation[] = $new_resrvation;


    // Ecriture avec la sécurité flock pour ne pas avoir des conflit (pas vraiment utile car on est sur une seule page de réservation))
    // pas nécéssaire mais je fais quand même 
    
    $flockOp = fopen($path, 'w');
    if ($flockOp) {
        if (flock($flockOp, LOCK_EX)) { // Verrouillage exclusif : permet d'empêcher les autres processus d'écrire en même temps que nous 
            
            fwrite($flockOp, json_encode($liste_reservation,JSON_PRETTY_PRINT)); // Ecriture du tableau MAJ en Json dans le fichier reservation.json
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



 