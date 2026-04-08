<?php
    /** script permettant de traiter les groupes d'activiter par l'admin**/



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
    // message partagé de l'activité
    $message_activite = trim($_POST['message_activite'] ?? '');

    // nettoyage anti doublon des participants cochés
    $clients = array_values(array_unique(array_map('strval', $clients)));


    // est ce que tout est bien remplis ?
    if (!$id_activite || empty($clients) || !$animateur) {
        exit ("Veuillez remplir tous les champs du formulaire.");
    }

    // règles métier simples 
    $regles_activites = [
        1 => ['min' => 1, 'max' => 12, 'nom' => 'Observation des Lémuriens'],
        2 => ['min' => 2, 'max' => 6,  'nom' => 'Plongée Barrière de Corail'],
        3 => ['min' => 2, 'max' => 8,  'nom' => 'Expédition Quad Désert']
    ];

    // on vérifie que l'activité choisie existe 
    $id_activite_int = (int)$id_activite;
    // nb de participants sélectionnés
    $nb_participants = count($clients);
    if (isset($regles_activites[$id_activite_int])) {
        $regle = $regles_activites[$id_activite_int];
        if ($nb_participants < $regle['min']) {
            exit("Pas assez de participants pour " . $regle['nom'] . " : minimum " . $regle['min'] . ".");
        }
        if ($nb_participants > $regle['max']) {
            exit("Trop de participants pour " . $regle['nom'] . " : maximum " . $regle['max'] . ".");
        }
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
        'message' => $message_activite
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