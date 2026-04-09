
<?php
    /** 
        * Page de traitement de la validation des réservations 
        * une page dédier à l'admin 
    **/

    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        exit ("Accès refusé, vous n'êtes pas un admin");
    }



    // récupération des données de la reservation par AJAX
    
    $idValider = $_POST['id']??null; // id de la réservation à valider

    
    $mail = $_POST['mail'] ?? ''; // mail du client pour vérifier s'il est déjà membre
    $action = $_POST['action'] ?? ''; //valider ou refuser

    $path_reservation = "../data/reservation.json";
    $path_users = "../data/users.json";
// lecture du fichier 
    $json = file_get_contents($path_reservation);
    $reservations = json_decode($json, true) ?: []; // au cas ou le fichier est vide 
    // variable boolean pour savoir si on a trouvé la réservation à valider
    $trouve = false;
    
    // variable temp pour sauvegarder les info de la reservation 
    $reserv_info = null;

    
    // une petite condition
    if ($idValider!==null){
        
        // parcour des réservations pour trouver celle à valider
        foreach ($reservations as $key => $reservation) {
            // si on trouve
            if ($reservation['id_res'] == $idValider) {

                if ($action === 'valider') {
                    // on change le status de la réservation en "validé"
                    $reservations[$key]['status'] ='validé';
                    
                }else if ($action === 'refuser') {
                    // on change le status de la réservation en "refusé"
                    $reservations[$key]['status'] ='refusé';
                }
                
                $reserv_info = $reservations[$key];
                $trouve = true; // on a trouvé la réservation à valider
                break; // on termine lq boucle 
               }
            }
            
        }
            // si on a trouvé la réservation à valider
            // on la sauvegarde dans le fichier JSON
        if ($trouve  ) {
            
                if ($action === 'valider') {    
                    // on vérifie si le mail du client existe déjà dans le fichier JSON des users
                    $json_users = file_get_contents($path_users);
                    $users = json_decode($json_users, true) ?: []; // au cas ou le fichier est vide 
                    $existe =false;
                    $mdp_genere = '';
                    // on vérifie si le mail du client existe déjà dans le fichier JSON des users

                    // mise à jour de existe
                    foreach ($users as $user) {
                        if ($user['email'] === $mail) {
                            $existe = true;
                            $mdp_genere = $user['mdp'] ?? '';
                            break;
                        }
                    }

                // sinon on ajoute le nouvel utilisateur dans le fichier JSON des users
                if (!$existe) {
                    $next_id = 1;
                    
                    // et si le json est vide ? 
                    if(!empty($users)){
                        $last_user = end($users);

                        // création d'id pour le nouvel utilisateur
                        // si le fichier JSON des users n'est pas vide, on prend l'id du dernier utilisateur et on ajoute 1 pour créer l'id du nouvel utilisateur
                        $next_id = ($last_user['id_c']  ?: 0) + 1; 
                    }

                    // generation d'un mot de passe aleatoire 
                    $alphabet = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789";
                    $longueur_mdp = 10;
                    for ($i = 0; $i < $longueur_mdp; $i++) {
                        $mdp_genere .= $alphabet[random_int(0, strlen($alphabet) - 1)];
                    }

                    $nouveau_client = array(
                        'id_c' => $next_id,
                        'nom' => $reserv_info['nom'],
                        'email' => $reserv_info['email'],
                        'mdp' => $mdp_genere,
                        'role' => 'client',
                        'id_res' => $idValider,
                        'prestation' => [],
                        'arrhes' =>0,
                        'reduction' => 0
                    );

                    $users[] = $nouveau_client; // ajout du nouvel utilisateur dans le tableau des users
                    file_put_contents($path_users, json_encode($users, JSON_PRETTY_PRINT)); // sauvegarde du tableau des users dans le fichier JSON
                }
            }





            $flockOp = fopen($path_reservation, 'w');

            // ici on fait du locking pour éviter les problèmes de concurrence si plusieurs admins valident en même temps
            // on a qu'un seul admin mais on peut imaginer que dans le futur il y en aura plusieurs
            if ($flockOp) {

                // on écrit les données dans le fichier JSON
                if (flock($flockOp, LOCK_EX)) {
                    fwrite($flockOp, json_encode($reservations, JSON_PRETTY_PRINT));
                    flock($flockOp, LOCK_UN);
                    // si l'action est de valider la réservation, 
                    // on retourne un JSON avec le status de la validation, 
                    // le mail du client, le mot de passe généré et si le compte existait déjà ou pas
                    if ($action === 'valider') {
                        echo json_encode([
                            'status' => 'success',
                            'action' => 'valider',
                            'nom' => $reserv_info['nom'] ?? '',
                            'email' => $mail ?: ($reserv_info['email'] ?? ''),
                            'password' => $mdp_genere,
                            'compteExistant' => $existe
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'success',
                            'action' => 'refuser',
                            'nom' => $reserv_info['nom'] ?? '',
                            'email' => $mail ?: ($reserv_info['email'] ?? '')
                        ]);
                    }
                } else {
                    echo "Fichier occupé.";
                }
                fclose($flockOp);
            } else {
                echo "Impossible d'ouvrir le fichier.";
            }

        } else {
            echo "Réservation non trouvée.";
        }
    exit;

?>




