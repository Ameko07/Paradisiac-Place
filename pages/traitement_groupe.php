<?php
    /** script permettant de traiter les groupes d'activiter**/

    
    // TODO : ajouter un bouton pour revenir à la page d'accueil de l'admin liste des activités


    session_start();
    // uniquement les admins peuvent accéder à cette page
     if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        header('Location: ../index.php');
        exit ("Accès refusé, vous n'êtes pas un admin");
    }
    
    // les donnees du fonmulaire d'organisation de groupe
    $id_activite = $_POST['id_activite'] ?? '';
    $nom_act = $_POST['nom_activite'] ?? '';
    // tableau des id de reservation des clients selectionnés
    $clients = $_POST['clients'] ?? []; 
    // nom de l'animateur
    $animateur = $_POST['animateur'] ?? ''; 


    // est ce que tout est bien remplis ?
    if (!$id_activite || empty($clients) || !$animateur) {
        exit ("Veuillez remplir tous les champs du formulaire.");
    }

    $path_actPrevues = "../data/activite_prevue.json";
    if (!file_exists($path_actPrevues)) {
        // créer le fichier s'il n'existe pas
        file_put_contents($path_actPrevues, json_encode([])); 
    }

    $json = file_get_contents($path_actPrevues);
    $activites_prevues = json_decode($json, true) ?: []; 


    // creation d'une variable de nouveau groupe 
    $nouveau_groupe = array(
        'id_groupe' => time(), // j' utilise le timestamp comme id unique pour le groupe
        'id_activite' => $id_activite,
        'nom_activite' => $nom_act,
        'animateur' => $animateur,
        'participants' => $clients,
        // date de creation du goupe pour pouvoir faire un trie par date de création si besoin
        'date_creation' => date('Y-m-d H:i:s'),
        'message' => []
    );

    // liste des activites prévues pour ajouter le nouveau groupe
    $activites_prevues[] = $nouveau_groupe;

    // sauvegarde dans le fichier
    $f = fopen($path_actPrevues, 'w');
    if ($f){
        if(flock($f, LOCK_EX)){
            // on écrit les données dans le fichier JSON
            fwrite($f, json_encode($activites_prevues, JSON_PRETTY_PRINT));
            // on libère le verrou
            flock($f, LOCK_UN);
            ob_clean();
            echo "success";
        }else{
            echo "Fichier occupé.";
        }
        fclose($f);

    } else {
        echo "Impossible d'ouvrir le fichier.";
    }
    exit;
?>