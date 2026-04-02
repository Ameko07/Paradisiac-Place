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
    foreach ($reservations as $reservation) {
        if ($reservation['email'] === $email_client) {
            $reservation_client = $reservation;
            break;
        }
    }

    $date_debut = $reservation_client['date_debut'] ?? '';
    $date_fin = $reservation_client['date_fin'] ?? '';

    // calcul du nombre de jours de la reservation
    $deb = new DateTime($date_debut); 
    $fin = new DateTime($date_fin);

    $nb_nuits = $deb->diff($fin)->days; // le nombre de jours de la reservation
    // pas le droit d'avoir une reservation de 0 jours ou moins
    if ($nb_nuits <= 0) {
        $nb_nuits = 1;
    }

    // prix de la chambre
    // on le récupère juste depuis les offres 
    $prix_chambre = 0;
    $nom_chambre = '';
    foreach ($offres['chambre'] as $c) {
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
    foreach ($prestations_choisies as $id_presta) {
        foreach ($offres['prestation'] as $presta) {
            if ($presta['id'] == $id_presta) {
                $total_presta += $presta['prix'];
                $details_presta[] = $presta;
                break;
            }
        }
    }



    $Prix_total = $nb_nuits * $prix_chambre;
    $prix_total_brut = $Prix_total + $total_presta;

    

    // les reduc et arrhes sont maintenant stockés au niveau de la réservation
    
    $reduction = isset($reservation_client['reduction']) ? (float)$reservation_client['reduction'] : 0;
    $arrhes = isset($reservation_client['arrhes']) ? (float)$reservation_client['arrhes'] : 0;
    $remise = ($reduction / 100) * $prix_total_brut;
    $Prix_total_apres_reduc = $prix_total_brut - $remise;

    
// TODO : ajouter une section pour les prestations supplémentaires (spa, excursions, etc) et les afficher dans la facture aussi

// TODO : ajouter une section pour les avis clients et permettre au client de laisser un avis sur son séjour
// TODO : ajouter une section pour les messages et permettre au client de contacter l'hôtel depuis son espace client

// TODO : corriger les identifiant des chambre choisi dans le fichier reservation.json (actuellement c'est une string alors que dans les offres c'est un int) pour que le prix s'affiche correctement
?>

<div class="container mt-4">
    <div class="row align-items-center mb-4">
<!--Utilisation des col : des colonne pour affichage à gauche et à droite-->
        <div class="col-md-8">
<!----Affichage d'un message de bienvenue personnalisé avec le nom du client et une icône-->
            <div class="container-fluid py-2">
                <!-- le container fuid permert de qui prends la largeur dispo--> 
                <h1 class="display-5 fw-bold text-success">

<!-- format bi-sun : -->
                    <i class="bi bi-sun"></i>Bienvenue, <?php echo htmlspecialchars($nom_client); ?>!</h1>
                <p class="col-md-8 fs-4 text-muted"> Votre séjour à Madagascar commence ici.</p>
            </div>
        </div>
    
        <div class="col-md-4 text-end text-md-end">
            <!-- un bouton de déconnexion qui redirige vers la page de login et détruit la session-->
            <a class="btn btn-outline-danger" href="pages/logout.php" >
                <i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <!--affichage des infor de séjours -->
                    <h4 class="card-title mb-4 border-bottom pb-2">Information du séjour :</h4>
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
                            <td class="text-end"><?php echo number_format($Prix_total, 2); ?>€</td>
                        
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
                        <!-- un scripte qui calcule la remise à appliquer si il y en a-->
              <!-- affichage de la réduction appliquée-->
                        <?php if($reduction > 0) :?>
                            <tr class="text-danger">
                                <td><i class="bi bi-percent"></i>(-<?php echo htmlspecialchars($reduction); ?>%)</td>
                                <td class="text-end"><strong>-<?php echo number_format($remise,2); ?>€</strong></td>
                            </tr>
                        <?php endif; ?>
                        <!-- affichage de l'arrhes si il y en a-->
                        <?php if($arrhes > 0) : ?>
                        <tr class="text-primary italic">
                            <td><i class="bi bi-cash-stack"></i>Arrhes versées </td>
                            <td class="text-end">-<?php echo number_format($arrhes, 2); ?>€</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                     <!--affichage du prix restant à payer-->
                    <tfoot>
                        <tr class="table-dark fs-5">
                            <td><strong>Prix restant à payer : </strong></td>
                            <td class="text-end"><strong><?php echo number_format($Prix_total_apres_reduc, 2); ?>€</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
        


            



                
            
        

    
        
            
            
                    

                        

            

                       
                        
                        

                        
                    
                    
                   
     

