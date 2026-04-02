<!--Un fihier php qui permettra à l'admin de valider les réservations -->
<!--à rajouter dans l'interface d'administration-->

<?php

    session_start();
    // vérification que l'utilisateur est bien un admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        // redirection vers la page d'accueil si ce n'est pas le cas
        header('Location: ../index.php');
        exit ("Accès refusé, vous n'êtes pas un admin");
    }
    // on va récupérer toute les reservation en status "en attente"
    $json = file_get_contents("../data/reservation.json");
    $attentes = json_decode($json, true);

    // les users aussi pour voir s'il n'existe pas déjà un mail qui a fait une réservation
    $json_users = file_get_contents("../data/users.json");
    $users = json_decode($json_users, true);

    // on récupère les offre pour voir si la cohérence avec les demande et les offres
    $json_offres = file_get_contents("../data/offre.json");
    $offres = json_decode($json_offres, true);

    // la liste des mails déjà utilisé pour faire une réservation
    $mails_reservation = array_column($users, 'email');

    // TODO : FAIRE UNE PAGE POUR LA VALIDATION DES PRESTATION AVEC LES LOGIQUE QUI VONT AVEC 
    // EXEMPLE : Nombre de peronne suffisante ? bonne date ? avis du client ? age ? etc
    // TODO : la génération de mot de passe se fera manuellement par l'admin "envoyer un mail au client" 
    // TODO : peut-être essayer de transformet toute la ligne de la reservation en bouton pour afficher la reservation en détaille (Date qui supperpose à d'autre) et mettre les bouton accepter et refuser DANS la reservation cliqué et la création du mot de passe que l'admin écrira MANUELLEMENT
    // TODO : et supprimer le bouton détails à la place 
    // TODO : supprimer le bouton "Valider reservation" dans la bare de navigation ou le remplacer par une autre fonctonnalité revenir sur la liste des reservation
    // TODO : pas juste bouton accepter ou refuser 
?>

<div class="container mt-4">
    <h3 class="mb-4 text-dark">Validation des réservations</h3>
    
    <!-- utilisation de Bootstrap pour le style du tableau -->

    <table class="table table-hover align-middle">

    <!-- utilisation de Bootstrap pour le style du tableau -->
<!--utilisation de thread pour automatiser la validation-->
        <thead>
            <tr>
                <th>Informations Client</th>
                <th>Dates de séjour</th>
                <th>Bungalow</th>
                <th>Client membre</th>
                <th>Réponse Admin</th>
            </tr>
        </thead>
        <tbody id="table-reservations">

            <?php
                // parcour des réservation pour afficher uniquement les en attente
                foreach ($attentes as $reservation) :
                    if ($reservation['status'] == 'en attente') :?>
            <tr id="row-<?php echo $reservation['id_res']; ?>">
                <!-- Affichage des informations du client -->
                <td>
                <!--Affichage des informations du client-->
                <!-- Utilisation de Bootstrap pour le style du tableau -->
                <!-- <strong> permet de mettre en gras le texte -->
                    <strong><?php echo htmlspecialchars($reservation['nom']); ?></strong><br>

                <!-- <small class="text-muted"> mise en italique du texte du mail -->
                    <small class="text-muted">Email: <?php echo htmlspecialchars($reservation['email']); ?></small><br>
                </td>

                <!--Affichage des dates de séjour-->
                <td>
                    <strong>Du:</strong> <?php echo htmlspecialchars($reservation['date_debut']); ?><br>
                    <strong>Au:</strong> <?php echo htmlspecialchars($reservation['date_fin']); ?>
                </td>
                <!--Affichage du nombre de personnes-->
                <td>
                    <span class="badge bg-info text-dark"><?php echo htmlspecialchars($reservation['nb_pers']); ?>Pers.</span><br>
                    <small> Type: <?php echo htmlspecialchars($reservation['chambre_choisie']); ?></small>
                </td>



                <!--Affichage du type de client : savoir s'il est déjà membre en vérifiant son mail -->
                <td>

                
                    <?php echo in_array($reservation['email'], $mails_reservation) ?
                        "<span class='badge bg-success'>Membre</span>" :
                        "<span class='badge bg-secondary'>Non Membre</span>"; 
                    ?>
                    
                </td>

                <!--Bouton d'acceptation de la réservation -->
                <td>
                    <div class="btn-group" >
                    <!--Bouton pour visualiser les détails de la réservation-->
                        <button class="btn btn-outline-primary btn-sm btn-details" data-id="<?php echo $reservation['id_res']; ?>" title="Voir les détails">
                            Détails
                        </button>
                        <!-- utilisation de Bootstrap pour le style du bouton -->
                <!--data id et mail permet de passer les informations à la fonction JavaScript-->
                    
                    <!--accepter-->
                    <button class="btn btn-success btn-sm btn-valider"
                        data-id="<?php echo $reservation['id_res']; ?>"
                        data-email="<?php echo htmlspecialchars($reservation['email']); ?>"
                        >Accepter
                    </button>
                    <!--refuser-->
                    <button class="btn btn-danger btn-sm btn-refuser"
                        data-id="<?php echo $reservation['id_res']; ?>"
                        data-email="<?php echo htmlspecialchars($reservation['email']); ?>"
                    >Refuser
                    </button>
                    </div>
                
                </td>

            </tr>
            <tr id="details-<?php echo $reservation['id_res']; ?>" class="d-none bg-light">
                <td colspan="5">
                    <div class="p-3 border-start border-primary border rounded border-4">
                        <h5>Détails de la réservation</h5>
                        <p><strong>Message du client:</strong><br>

                    <!--Message du client, sinon affichage d'un message par défaut-->
                        <em><?php echo nl2br(htmlspecialchars($reservation['message'] ?: 'Aucun message')); ?></em> </p>
                        <!-- Affichage des offres correspondantes à la réservation -->
                        <h6>Offres correspondantes:</h6>
                        <ul>
                            <?php
                                foreach ($offres as $offre) {
                                    // Vérification de la cohérence entre les dates de la réservation et les offres
                                    if (
                                        ($offre['date_debut'] <= $reservation['date_debut'] && $offre['date_fin'] >= $reservation['date_debut']) ||
                                        ($offre['date_debut'] <= $reservation['date_fin'] && $offre['date_fin'] >= $reservation['date_fin'])
                                    ) {
                                        echo "<li>" . htmlspecialchars($offre['description']) . " (Du " . htmlspecialchars($offre['date_debut']) . " au " . htmlspecialchars($offre['date_fin']) . ")</li>";
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                </td>

            </tr>
                <!--fermeture de la boucle et de la conditions-->
            <?php 
                endif;  
                endforeach;
            ?>
        </tbody>
                <!--id admin-retour à récuperer dans le script-->
        <div id="admin-retour" class="mt-3"></div>
</div>