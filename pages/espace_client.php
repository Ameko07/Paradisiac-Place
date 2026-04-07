<?php
    /** un scripte qui affiche l'espace client et la facture de la reservation
     * les données sont récupérées depuis les fichiers JSON et la session
     * **/

    
    
    session_start();
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'client') {
        // redirection vers la page de connexion si ce n'est pas le cas
        header('Location: login.php');
        exit("Accès refusé, vous n'êtes pas un client");
    }



    // sinon c'est un client qui est connecté on peut tout afficher (pour les client)

    $email_client = $_SESSION['email_client'] ;
    $nom_client = $_SESSION['nom_client'] ;


    // les info de reservqtion du client 
    // gestion des reservation et affichage de la faccture 

    // on recupere les reservation du client depuis le fichier JSON
    $fichier_reservation = '../data/reservation.json';
    $json_reservations = file_get_contents($fichier_reservation);
    $reservations = json_decode($json_reservations, true) ?: []; // au cas ou le fichier est vide

    // on recupere les offres
    $fichier_offres = '../data/offre.json';
    $json_offres = file_get_contents($fichier_offres);
    $offres = json_decode($json_offres, true) ?: []; // au cas ou le fichier est vide


    
    // comme un client n'a droit qu'à une reservation , on cehrche sa reservation 
    $reservation_client = null;
    $reservation_index = null;
    foreach ($reservations as $index => $reservation) {
        if ($reservation['email'] === $email_client) {
            $reservation_client = $reservation;
            $reservation_index = $index;
            break;
        }
    }

    // tableau des prestations à choisir pour le client 
    if (isset($_POST['id_presta']) && $reservation_client) {
        $id_presta = $_POST['id_presta'];
        $action = $_POST['action'] ?? 'ajouter';


        // si l'action est "ajouter" on rajoute la prestation dans le tableau
        if ($action === 'ajouter') {
            if (!in_array($id_presta, $reservation_client['prestations'] ?? [])) {
                $reservation_client['prestations'][] = $id_presta;
            }

            // sinon non on supprime la prestation
        } else {
            if (($key = array_search($id_presta, $reservation_client['prestations'] ?? [])) !== false) {
                unset($reservation_client['prestations'][$key]);
                // réindexer le tableau pour éviter les trous
                $reservation_client['prestations'] = array_values($reservation_client['prestations']);
            }
        }

        // on remet la reservation modifiee dans le tableau principal avant sauvegarde
        if ($reservation_index !== null) {
            $reservations[$reservation_index] = $reservation_client;
        }

        // on sauvegarde direct
        file_put_contents($fichier_reservation, json_encode($reservations, JSON_PRETTY_PRINT));

    }
    /** ------------------calcul de la facture------------------------- **/

    

    $date_debut = $reservation_client['date_debut'] ?? '';
    $date_fin = $reservation_client['date_fin'] ?? '';

    // calcul du nombre de jours de la reservation
    $nb_nuits = 1;
    if (!empty($date_debut) && !empty($date_fin)) {
        $deb = new DateTime($date_debut);
        $fin = new DateTime($date_fin);
        $nb_nuits = $deb->diff($fin)->days;
    }

    // le nombre de jours de la reservation
    // pas le droit d'avoir une reservation de 0 jours ou moins
    if ($nb_nuits <= 0) {
        $nb_nuits = 1;
    }

    // prix de la chambre
    // on le récupère juste depuis les offres 
    $prix_chambre = 0;
    $nom_chambre = '';
    $liste_chambres = $offres['chambre'] ?? [];
    foreach ($liste_chambres as $c) {
        if ($c['id'] == $reservation_client['chambre_choisie']) {
            $prix_chambre = $c['prix'];
            $nom_chambre = $c['nom'];
            break;
        }
    }

    // Les prestations choisies
    $prestations_choisies = $reservation_client['prestations'] ?? [];

    $total_presta = 0;
    $details_presta = [];
    $liste_prestations = $offres['prestation'] ?? [];
    foreach ($prestations_choisies as $id_presta) {
        foreach ($liste_prestations as $presta) {
            if ($presta['id'] == $id_presta) {
                $total_presta += $presta['prix'];
                $details_presta[] = $presta;
                break;
            }
        }
    }

    



    // calcul du prix total avant réduction
    $total_hebergement = $nb_nuits * $prix_chambre;
    $Prix_total = $total_hebergement + $total_presta;

    // réduction à appliquer (2 reductions existent:
    //  reduction dans Users qui est une reduc de fidelité 
    // et réduction dans reservation qui est une reduc de saisonnalité)

    $taux_reduction_reserv = isset($reservation_client['reduction']) ? $reservation_client['reduction'] : 0;
    $montant_remise = ($Prix_total * $taux_reduction_reserv) / 100; 

    // apres la reduction 
    $Prix_total_apres_reduc = $Prix_total - $montant_remise;

    // les arrhes versées par le client
    $arrhes = isset($reservation_client['arrhes']) ? $reservation_client['arrhes'] : 0;

    // le prix restant à payer
    $reste_a_payer = max(0, $Prix_total_apres_reduc - $arrhes);

    


// TODO : ajouter une section pour les avis clients et permettre au client de laisser un avis sur son séjour
// TODO : ajouter une section pour les messages et permettre au client de contacter l'hôtel depuis son espace client

?>

<div class="container mt-4">
    <div class="row align-items-center mb-4">
<!--Utilisation des col : des colonne pour affichage à gauche et à droite-->
        <div class="col-md-8">
<!----Affichage d'un message de bienvenue personnalisé avec le nom du client et une icône-->
            
                <!-- le container fuid permert de qui prends la largeur dispo--> 
            <h1 class="display-5 fw-bold text-success">

<!-- format bi-sun : -->
                <i class="bi bi-sun"></i>Bienvenue, <?php echo htmlspecialchars($nom_client); ?>!</h1>
            <p class="col-md-8 fs-4 text-muted"> Votre séjour à Madagascar commence ici. Voici votre récapitulatif de votre séjour.</p>
            
        </div>
    
        <div class="col-md-4 text-end text-md-end">
            <!-- un bouton de déconnexion qui redirige vers la page de login et détruit la session-->
            <a class="btn btn-outline-danger" href="logout.php" >
                <i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-body">
                    <!--affichage des infor de séjours -->
                    <h5 class="border-bottom pb-2 mb-3">Information du séjour :</h5>
                    <!--utilisation de la class bi pour les icones -->
                    <!-- bi-calendar permet d'afficher un calendrier--> 
                    <p><i class="bi bi-calendar-check text-success"></i><strong>durée :</strong>
                        <?php echo htmlspecialchars($nb_nuits); ?> nuits (du <?php echo htmlspecialchars($date_debut); ?> au <?php echo htmlspecialchars($date_fin); ?>)
                    </p>
                    <p><i class="bi bi-door-open text-success"></i><strong>chambre :</strong>
                        <?php echo htmlspecialchars($nom_chambre); ?>
                    </p>
                    <p><i class="bi bi-tag text-success"></i><strong>Tarif :</strong>
                        <?php echo htmlspecialchars($prix_chambre); ?>€ par nuit
                    </p>
                    <hr>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-4 ">
            <div class="card-body">
                <h5 class="border-bottom pb-2 mb-3">Prestations supplémentaires :</h5>
                <div class="list-group list-group-flush">
<!--Affichage des prestations supplémentaires choisies par le client-->
                    <?php foreach ($liste_prestations as $prestation) :
                        $isActive = in_array($prestation['id'], $prestations_choisies);
                        ?>
                        <div class="list-group-item d-flex align-items-center justify-content-between px-0">
                         <span> <?php echo htmlspecialchars($prestation['nom']); ?> (<?php echo htmlspecialchars($prestation['prix']); ?>€)</span> 
                            
                            <form method="POST" class="form-modifier-prestation d-flex align-items-center justify-content-between border rounded p-2
                             <?php echo $isActive ? 'bg-light' : 'bg-white'; ?>">
                                <span><?php echo htmlspecialchars($prestation['nom']); ?></span>
                                <input type="hidden" name="id_presta" value="<?php echo $prestation['id']; ?>">

                                <?php if ($isActive) : ?>
                                    <input type="hidden" name="action" value="supprimer">
                                    <button type="submit" class="btn btn-sm btn-danger ">
                                        <i class="bi bi-plus-lg"></i>Supprimer
                                    </button>
                                <?php else : ?>
                                    <input type="hidden" name="action" value="ajouter">
                                    <button type="submit"  class="btn btn-success ">
                                        <i class="bi bi-plus-lg"></i>Ajouter
                                    </button>
                                <?php endif; ?>

                            </form>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
        
        <div class="col-md-7">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white"> 
                <!-- bi-receipt pour afficher une facture-->
                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>
                    Votre Facture : <?php echo htmlspecialchars($nom_chambre); ?>
                </h5>
            </div>
            <div class="card-body p-0">
                <!-- utilisation d'une table pour afficher la facture de manière structurée-->
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                <!--affichage du calcul du prix total-->
                            <td>Hébergement (<?php echo htmlspecialchars($nb_nuits); ?> nuits * <?php echo htmlspecialchars($prix_chambre); ?>€)</td>
                            <td class="text-end"><?php echo number_format($total_hebergement, 2); ?>€</td>
                        
                        </tr>
                        <!-- affichage des prestations choisies et leur prix-->
                        <?php if (!empty($details_presta)) : ?>
                            <?php foreach ($details_presta as $presta) : ?>
                                <tr>
                                    <td><i class="bi bi-check-circle text-success me-2"></i><?= htmlspecialchars($presta['nom']) ?></td>
                                    <td class="text-end"><?= number_format($presta['prix'], 2) ?>€</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- affichage de la réduction appliquée -->
                        <?php if($taux_reduction_reserv > 0) :?>
                            <tr class="table-warning text-danger">
                                <td><strong>Réduction saisonnière (<?php echo htmlspecialchars($taux_reduction_reserv); ?>%)</strong></td>
                                <td class="text-end"><strong>-<?php echo number_format($montant_remise,2); ?>€</strong></td>
                            </tr>
                        <?php endif; ?>

                        <!-- arrhes -->
                        <?php if($arrhes > 0) :?>
                            <tr class="table-warning text-danger">
                                <td><strong>Arrhes versées</strong></td>
                                <td class="text-end"><strong>-<?php echo number_format($arrhes, 2); ?>€</strong></td>
                            </tr>
                        <?php endif; ?>
                        
                       
                    </tbody>
                     <!--affichage du prix restant à payer-->
                    <tfoot>
                        <tr class="table-dark fs-5">
                            <td class="fs-5"><strong>Prix restant à payer : </strong></td>
                            <td class="text-end fs-5"><strong><?php echo number_format($reste_a_payer, 2); ?>€</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

                            <!--Affichage des arrhes versées par le client-->
        <?php if ($arrhes <= 0) : ?>
            <div class="alert alert-warning mt-3 border-warning">
                <i class="bi bi-info-circle me-2"></i> Vous n'avez pas encore versé de garantie.
            </div>
        <?php else : ?>
            <div class="alert alert-success mt-3 border-success">
                <i class="bi bi-check-circle me-2"></i> Merci d'avoir versé vos arrhes. Nous avons hâte de vous accueillir !
            </div>
        <?php endif; ?>
    </div>
</div>  
