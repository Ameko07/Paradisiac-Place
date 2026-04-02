<?php 

/**
 * Déconnexion de l'utilisateur
    * Ce script détruit la session de l'utilisateur et redirige vers la page de connexion avec un message de succès.
     * Il est important de noter que la session doit être démarrée avant de pouvoir la  
        * détruire, c'est pourquoi nous utilisons session_start() au début du script.
 * **/
    session_start();
    session_unset(); // Vider les données de session
    session_destroy(); // Détruire la session

    // Redirection vers la page de connexion avec un message de succès
    header('Location: ../index.php?message=Déconnexion réussie');
    exit();
?>
