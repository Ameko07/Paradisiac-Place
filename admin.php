<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title> MadaDream - Plage de Madagascar </title>

        <!--bootstrap : lien vers le fichier CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!--utulisation de bootstrap pour le style et la mise en page autorisée-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <!--Boite invisible qui empêche le menu de se coller au bord de l'ecran-->
        <div class="container">
            <a class="navbar-brand" href="#">Réservations</a>
            <div class="navbar-nav">
                <!--style pour les lien menu -->
                <a class="nav-link" href="#" id="menu-valid-res"> Valider Réservation</a>
                <a class="nav-link" href="#" id="menu-gestion-planning"> Gestion Planning</a>
                
        

            </div>
        </div>
    </nav>
    <!-- Un autre contener pour centrer le texte-->
    <div class="container mt-4" id="admin-body">
<!--une zone pour afficher les réservations en attente-->
        <div class="row">
            <div class="col-12">
                <h1>Espace Administrateur </h1>
                <p class="text-muted">Gestion des réservations</p>
                <hr>

                
                <div id="zone_tableau">
                    <!--le tableau de validation des réservations sera injecté ici par AJAX-->
                    <p>Selectionner une option dans le menu</p>
                </div>
            
        </div>
    </div>
    <!--scripte pour d'abord afficher le texte et les couleur avant de charger la logique-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/main.js"></script>

</body>
</html>