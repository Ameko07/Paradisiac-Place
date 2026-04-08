<?php

/** un scripte pour charger les offres depuis un fichier JSON **/
    $json = file_get_contents("../data/offre.json");
    $offres = json_decode($json, true);

    $activites = $offres["activite"] ?? [];
    $prestations = $offres["prestation"] ?? [];
?>

<!-- Page de présentation détaillée des activités et prestations 
 On y trouve : la liste détaillée de toutes les presations et des activité -->
<!-- Contenu de la page -->
<div class="container mt-4">
    <div class="card carteMada mb-3">
        <div class="card-body">
            <h2 class="mb-2">Découvrir MadaDream en détail</h2>
            <p class="text-muted mb-0">
                Vous trouverez ici une présentation complète de nos activités et prestations, 
                ainsi que les tarifs associés. 
                Que vous cherchiez des aventures en plein air ou des services pour rendre votre séjour plus confortable,
                nous avons tout ce qu'il vous faut pour vivre une expérience inoubliable à Madagascar.
            </p>
        </div>
    </div>
<!-- section pour les activités et les prestations -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card carteMada">
                <div class="card-body">
                    <h3 class="h5">Nos activités</h3>
                    <p class="text-muted">Pensées pour petits groupes et encadrées par des animateurs.</p>
                    <div class="list-group list-group-flush">
                <!-- Liste des activités -->
                        <?php foreach ($activites as $a): ?>
                            <div class="list-group-item px-0">
                                <div class="fw-bold"><?php echo htmlspecialchars($a["nom"] ?? "Activité"); ?></div>
                                <small class="text-muted">
                                    Unité: <?php echo htmlspecialchars($a["unite"] ?? "-"); ?>
                                    | Prix: <?php echo htmlspecialchars($a["prix"] ?? "-"); ?>€
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- section pour les prestations -->

        <div class="col-lg-6">
            <div class="card carteMada">
                <div class="card-body">
                    <h3 class="h5">Nos prestations</h3>
                    <p class="text-muted">Des options pour améliorer le confort du séjour.</p>
                    <div class="list-group list-group-flush">
                        <?php foreach ($prestations as $p): ?>
                            <div class="list-group-item px-0 d-flex justify-content-between">
                                <span><?php echo htmlspecialchars($p["nom"] ?? "Prestation"); ?></span>
                                <strong><?php echo htmlspecialchars($p["prix"] ?? "-"); ?>€</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- se connecter ou reserver -->

    <div class="bandeauBas text-center mt-4">
        <h3 class="h4 mb-2">Passez à l'action</h3>
        <p class="mb-3">Vous pouvez réserver dès maintenant ou vous connecter à votre espace.</p>
        <div class="ligneBoutons justify-content-center mt-0">
            <button class="btn boutonSable versEspaceClient" type="button">Se connecter</button>
            <button class="btn btn-light versReservation" type="button">Réserver</button>
            <button class="btn btn-outline-light" id="btn-retour-accueil" type="button">Retour accueil</button>
        </div>
    </div>
</div>
