<?php
    /**
     * Php aui contient le code de la page de verification du client ET de l'admin
     * Les donner son comparer pour definir si c'est un admin ou un client 
     * soit venant du cliaue du bouton admin soit du client
     * **/
    // demarrage de la session 
    session_start();

    // les donnees envoye par le formulaire de connexion
    $type = $_POST['type_connexion'] ?? '';
    $user = $_POST['identifiant'] ?? '';
    $password = $_POST['password'] ?? '';


    // dans le cas de l'admin 
    if($type === 'admin'){
        // on verifie les identifiants de l'admin 
        if($user === 'admin' && $password === 'admin123'){
            // si c'est les bonnes identif , on stocke dans la session que c'est l'abmin 
            $_SESSION['role'] = 'admin';
            // on retourne une reponse de succes
            echo "success_admin";
        } else {
            echo "error_admin";
        }
    }
    // dans le cas du client
    else if($type === 'client'){
        // on verifie les identifiants du client 
        // on recupere les clients depuis le fichier JSON
        $fichier = '../data/users.json';

        // si le file existe 
        if (file_exists($fichier)) {
            $clients = json_decode(file_get_contents($fichier), true);    
            $trouve = false;


            // on cherche le client dedans 
        foreach($clients as $client) {
            if ($client['email'] === $user && $client['mdp'] === $password) {
                // si on le trouve on stock
                $_SESSION['role'] = 'client';
                $_SESSION['id_client'] = $client['id_c']??'';
                $_SESSION['nom_client'] = $client['nom'] ?? '';
                $_SESSION['email_client'] = $client['email'] ?? '';

                $trouve = true;

                // on retourne success
                echo "success_client";
                exit;
            }
        }
        if (!$trouve) {
            echo "error_client";
        }
    } else {
        // si on ne trouve rien , soit mot de passe ou email incorrect
        echo "erreur système : base de données non trouvée";
    }

}
        

        
    

?>