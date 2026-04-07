<?php
        $json = file_get_contents("../data/offre.json");
        $offres = json_decode($json, true);

        $activites = $offres["activite"] ?? [];
        $prestations = $offres["prestation"] ?? [];
?>

<div class="container">
        <section class="landing-hero">
                <div class="landing-hero-content">
                        <h1 class="landing-hero-title">Bienvenue chez MadaDream</h1>
                        <p class="landing-hero-subtitle mb-2">
                                Entre plage et forêt tropicale, vivez un séjour unique à Madagascar :
                                hébergement, activités encadrées et prestations pour voyager sans stress.
                        </p>
                        <p class="mb-0">Votre aventure commence ici.</p>

                        <div class="landing-cta-row">
                                <button class="btn btn-mada-main btn-go-client" type="button">Se connecter</button>
                                <button class="btn btn-mada-outline btn-go-reserver" type="button">Réserver</button>
                                <button class="btn btn-light btn-go-decouvrir" type="button">Découvrir en détail</button>
                        </div>
                </div>
        </section>

        <section class="landing-section">
                <div class="row g-3">
                        <div class="col-lg-6">
                                <div class="card landing-card">
                                        <div class="card-body">
                                                <h3 class="card-title h5">Activités phares</h3>
                                                <p class="text-muted">Sélection d'expériences pour découvrir Madagascar autrement.</p>
                                                <ul class="mb-0">
                                                        <?php
                                                                $maxA = min(4, count($activites));
                                                                for ($i = 0; $i < $maxA; $i++) {
                                                                        $nom = htmlspecialchars($activites[$i]["nom"] ?? "Activité");
                                                                        $prix = htmlspecialchars($activites[$i]["prix"] ?? "-");
                                                                        echo "<li>" . $nom . " - <strong>" . $prix . "€</strong></li>";
                                                                }
                                                        ?>
                                                </ul>
                                        </div>
                                </div>
                        </div>

                        <div class="col-lg-6">
                                <div class="card landing-card">
                                        <div class="card-body">
                                                <h3 class="card-title h5">Prestations confort</h3>
                                                <p class="text-muted">Des services utiles pour un séjour fluide et agréable.</p>
                                                <ul class="mb-0">
                                                        <?php
                                                                $maxP = min(4, count($prestations));
                                                                for ($i = 0; $i < $maxP; $i++) {
                                                                        $nom = htmlspecialchars($prestations[$i]["nom"] ?? "Prestation");
                                                                        $prix = htmlspecialchars($prestations[$i]["prix"] ?? "-");
                                                                        echo "<li>" . $nom . " - <strong>" . $prix . "€</strong></li>";
                                                                }
                                                        ?>
                                                </ul>
                                        </div>
                                </div>
                        </div>
                </div>
        </section>

        <section class="landing-section landing-reasons">
                <h3 class="h5 mb-3">Pourquoi choisir MadaDream ?</h3>
                <ul class="mb-0">
                        <li>Un parcours simple : demande, validation, accès client, facture claire.</li>
                        <li>Des activités encadrées avec gestion des groupes et des animateurs.</li>
                        <li>Une expérience immersive entre nature, détente et découverte locale.</li>
                </ul>
        </section>

        <section class="landing-bottom-cta text-center">
                <h3 class="h4 mb-2">Prêt à vivre Madagascar ?</h3>
                <p class="mb-3">Connectez-vous si vous êtes déjà membre, ou réservez votre prochain séjour.</p>
                <div class="landing-cta-row justify-content-center mt-0">
                        <button class="btn btn-mada-main btn-go-client" type="button">Se connecter</button>
                        <button class="btn btn-light btn-go-reserver" type="button">Réserver</button>
                </div>
        </section>
</div>