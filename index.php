<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title> MadaDream - Plage de Madagascar </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!--utulisation de bootstrap pour le style et la mise en page autorisée-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
<body>
    <!--Barre de navigation  venant de bootstrap-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <!--Boite invisible aui empeche le menu de se coller au bord de l'ecran-->

        <div class="container">
            <a class="navbar-brand" href="#" id="MadaDream">MadaDream</a>
            <div class="navbar-nav">
                <!--style pour les lien-->
                <a class="nav-link" href="#" id="menu-accueil"> Accueil</a>
                <a class="nav-link" href="#" id="menu-decouvrir"> Découvrir</a>
                <a class="nav-link" href="#" id="menu-reserver"> Reserver</a>
                <a class="nav-link" href="#" id="menu-client"> Client</a>
                <a class="nav-link" href="#" id="menu-admin"> Admin</a>
        

            </div>
        </div>
    </nav>
    <!-- Zone centrale dynamique : chargée via main.js -->
    <div class="container mt-4" id="main-content"></div>
    <!--scripte pour d'abord afficher le texte et les couleur avant de charger la logique-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>

</body>
</html>