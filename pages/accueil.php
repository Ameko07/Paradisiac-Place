<?php
        /** un scripte pour charger les offres**/
        $json = file_get_contents("../data/offre.json");
        $offres = json_decode($json, true);

        $activites = $offres["activite"] ?? [];
        $prestations = $offres["prestation"] ?? [];
?>

<div class="container">
<!-- Section Hero : qui permet de présenter le site -->
        <section class="sectionHero">
                <!-- contenue du hero modifié dans le CSS-->
                <div class="contenuHero">
                        <h1 class="titreHero">Bienvenue chez MadaDream</h1>
                        <p class="sousTitreHero mb-2">
                                Entre plage et forêt tropicale, vivez un séjour unique à Madagascar :
                                hébergement, activités encadrées et prestations pour voyager sans stress.
                        </p>
                        <p class="mb-0">Votre aventure commence ici.</p>
                                <!-- Les boutons CTA : des boutons insite le client à faire une action -->
                        <div class="ligneBoutons">
                                <button class="btn boutonSable versEspaceClient" type="button">Se connecter</button>
                                <button class="btn boutonContour versReservation" type="button">Réserver</button>
                                <button class="btn btn-light versDecouverte" type="button">Découvrir en détail</button>
                        </div>
                </div>
        </section>
        <!-- Section des activités et prestations -->
        <section class="blocInfo">
                <!-- mise en page des cartes en ligne -->
                <div class="row g-3">
                        <!-- chaque activité a sa propre carte -->
                        <div class="col-lg-6">
                                <div class="card carteMada">
                                        <div class="card-body">
                                                <h3 class="card-title h5">Activités phares</h3>
                                                <p class="text-muted">Sélection d'expériences pour découvrir Madagascar autrement.</p>
                                                <!-- Affichage en liste à points -->
                                                <ul class="mb-0">
                                <!-- un scripte pour parcrourir les activité et les afficher -->
                                                        <?php
                                                                $maxAct = min(4, count($activites));
                                                                for ($i = 0; $i < $maxAct; $i++) {
                                                                        $nom = htmlspecialchars($activites[$i]["nom"] ?? "Activité");
                                                                        $prix = htmlspecialchars($activites[$i]["prix"] ?? "-");
                                                                        echo "<li>" . $nom . " - <strong>" . $prix . "€</strong></li>";
                                                                }
                                                        ?>
                                                </ul>
                                        </div>
                                </div>
                        </div>
                        <!-- chaque prestation a sa propre carte -->

                        <div class="col-lg-6">
                                <div class="card carteMada">
                                        <div class="card-body">
                                                <h3 class="card-title h5">Prestations confort</h3>
                                                <p class="text-muted">Des services utiles pour un séjour fluide et agréable.</p>
                                                <ul class="mb-0">
                                <!--Comme les activités , on parcours et on affiche -->
                                                        <?php
                                                                $maxPresta = min(4, count($prestations));
                                                                for ($i = 0; $i < $maxPresta; $i++) {
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
        <!-- petite partie de mise en confiance des utilisateur pour les convaincre de reserver un séjours-->
        <section class="blocInfo listeAvantages">
                <h3 class="h5 mb-3">Pourquoi choisir MadaDream ?</h3>
                <ul class="mb-0">                        
                        <li>Des activités encadrées avec gestion des groupes et des animateurs : </li>
                        <p>Nos animateurs sont formés et expérimentés pour vous accompagner dans vos activités.</p>
                        <li>Une expérience immersive entre nature, détente et découverte locale :</li>
                        <p>Vous trouverez des lieux magnifiques, des animaux de toutes les couleures et des produités 
                                locales qui vous attendent.</p>
                        <li>Un service client réactif et professionnel : </li>
                        <p>Nous nous engageons à vous fournir un service client de qualité, disponible et réactif.</p>
                </ul>
        </section>

                                <!--S'affiche tout en bas de toutes les description et suppose que le client est convancu pour l'insité à reserver-->
        <section class="bandeauBas text-center">
                <h3 class="h4 mb-2">Prêt à vivre Madagascar ?</h3>
                <p class="mb-3">Connectez-vous si vous êtes déjà membre, ou réservez votre prochain séjour.</p>
                <div class="ligneBoutons justify-content-center mt-0">
                        <button class="btn boutonSable versEspaceClient" type="button">Se connecter</button>
                        <button class="btn btn-light versReservation" type="button">Réserver</button>
                </div>
        </section>
</div>