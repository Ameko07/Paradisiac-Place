<?php 

/**
 * Déconnexion de l'utilisateur
    * Ce script détruit la session de l'utilisateur et redirige vers la page de connexion avec un message de succès.
 * **/
    session_start();
    session_unset(); // Vider les données de session
    session_destroy(); // Détruire la session

    // Redirection vers la page de connexion avec un message de succès
    header('Location: ../index.php?message=Déconnexion réussie');
    exit();
?>
