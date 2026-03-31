<?php
    session_start();
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'client') {
        // redirection vers la page de connexion si ce n'est pas le cas
        header('Location: login.php');
        exit("Accès refusé, vous n'êtes pas un client");
    }

    // sinon c'est un client qui est connecté on peut tout afficher (pour les client)

    $email_client = $_SESSION['email_client'] ;
    $nom_client = $_SESSION['nom_client'] ;

?>

<div class="container">
    <h1>Bienvenue, <?php echo htmlspecialchars($nom_client); ?>!</h1>
    <p> Vous êtes maintenant connecté à votre espace client.</p>
</div>