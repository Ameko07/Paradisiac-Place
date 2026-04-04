<?php
    session_start();
    if($_SESSION['role'] !== 'admin') { header('Location: index.php'); exit; }
?>


<!--barre de navigation -->


<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <!--Boite invisible qui empêche le menu de se coller au bord de l'ecran-->
    <div class="container">
        <a class="navbar-brand" href="#"><i class="bi bi-shield-lock me-2"></i> Réservations</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav me-auto">
                <!--style pour les lien menu -->
                <a class="nav-link" href="#" id="menu-valid-res"><i class="bi bi-check2-square me-2"></i> Valider Réservation</a>
                <a class="nav-link" href="#" id="menu-gestion-planning"><i class="bi bi-calendar3"></i> Gestion Planning</a>

            </div>
            <div class="navbar-nav">
                <span class="nav-item nav-link text-light disabled me-2">
                    <i class="bi bi-person-circle me-2"></i> Bienvenue, Admin
                </span>
                <a class="btn btn-outline-danger btn-sm d-flex align-items-center" href="pages/logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                </a>
        </div>
    </div>

</nav>
<!-- Un autre contener pour centrer le texte-->
<div class="container mt-4" id="admin-body">
<!--une zone pour afficher les réservations en attente-->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center ">
                <h1>Espace Administrateur </h1>    
                <span class="badge bg-success">Admin, connecté</span>
            </div>
            
            <p class="text-muted">Gestion des réservations et du planning</p>
            <hr>


            
            <div id="zone_tableau">
                <!--le tableau de validation des réservations sera injecté ici par AJAX-->
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Instructions :</strong> Selectionner une option dans le menu
                </div>
            </div>
            
        </div>
    </div>
</div>

