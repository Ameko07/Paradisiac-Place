<?php
    /**script pour modifier les prestaion dans la facture **/
    session_start();
    // on vérifie que le client est connecté
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client' || !isset($_SESSION['email_client'])) {
        echo "erreur de connexion";
        exit();
    }
    

    
    // les données des prestations réupérées du formulaire de modification
    $email = $_SESSION['email_client'];
    $id_presta = (int)$_POST['id_presta'];
    $action = $_POST['action'] ?? 'ajouter'; // 'ajouter' ou 'supprimer'
    $path = "../data/reservation.json";

    if ($id_presta === 0) {
        echo "Id de prestation incorrect.";
        exit();
    }

    // les reservations existantes
    $reservations_exist = [];
    if (file_exists($path)) {
        $json = file_get_contents($path);
        $reservations_exist = json_decode($json, true) ?: [];
    }

    $trouve = false;

    foreach ($reservations_exist as &$reservation) {
        if ($reservation['email'] === $email) {
            // on trouve la reservation du client connecté
            
            // on ajoute la prestation choisie à la reservation

            //on vérifie si la prestation a déjà été ajoutée dans la reservation 
            // pour éviter de rajouter plusieur fois 
           
            if (!isset($reservation['prestations'])) {
                $reservation['prestations'] = [];
            }

            // on vérifie si les prestations sont dans un format associatif 
            // ou indexé pour les convertir en indexé si besoin
            if (is_array($reservation['prestations'])) {
                $cles_presta = array_keys($reservation['prestations']);
                $format_assoc = ($cles_presta !== range(0, count($cles_presta) - 1));
                if ($format_assoc) {
                    $reservation['prestations'] = array_values($reservation['prestations']);
                }
            } else {
                $reservation['prestations'] = [];
            }

            //  dans le cas ou on ajoute la prestation, 
            if($action === 'ajouter') { 

                // on vérifie que la presta n'est pas dans la reservation avant de l'ajouter pour éviter les doublons 
                if (!in_array($id_presta, $reservation['prestations'])) {
                    $reservation['prestations'][] = $id_presta;
                
                }

                // dans le cas ou on supprime la prestation,

            } elseif ($action === 'supprimer') {
                // si la prestation est dans la reservatin , on supprime , sinon rien 
                if (in_array($id_presta, $reservation['prestations'])) {
                    $reservation['prestations'] = array_diff($reservation['prestations'], [$id_presta]);
                }
            }
            $trouve = true;
            break; // on sort de la boucle une fois la reservation trouvée
        }

        
    }
    if ($trouve) {
            // on sauvegarde les modifications dans le fichier JSON
            
            if (file_put_contents($path, json_encode($reservations_exist, JSON_PRETTY_PRINT))) {
                echo "success";
            } else {
                echo "error lors de l'écriture du fichier";
            }
        } else {
            echo "Resrvation du client non trouvée.";
        }
?>