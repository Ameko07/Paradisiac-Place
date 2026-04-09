

<?php
    /** Page de validation des réservations pour l'admin
     * Affiche les réservations en attente avec les informations du client, les dates de séjour et le type de client (membre ou non)
     * Permet à l'admin d'examiner les détails de chaque réservation
     * Permet à l'admin d'accepter ou de refuser une réservation
     * **/
// gestion de connexion admin + navigation retour vers la liste des réservations

    session_start();
    // vérification que l'utilisateur est bien un admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        // redirection vers la page d'accueil si ce n'est pas le cas
        header('Location: ../index.php');
        exit ("Accès refusé, vous n'êtes pas un admin");
    }
    // on va récupérer toute les reservation en status "en attente"
    $json = file_get_contents("../data/reservation.json");
    $attentes = json_decode($json, true) ?: [];

    // les users aussi pour voir s'il n'existe pas déjà un mail qui a fait une réservation
    $json_users = file_get_contents("../data/users.json");
    $users = json_decode($json_users, true) ?: [];

    // on récupère les offre pour voir si la cohérence avec les demande et les offres
    $json_offres = file_get_contents("../data/offre.json");
    $offres = json_decode($json_offres, true) ?: [];

    // index des bungalows pour recuperer rapidement nom et stock par id
    $chambres_par_id = [];
    foreach (($offres['chambre'] ?? []) as $chambre) {
        $chambres_par_id[$chambre['id']] = $chambre;
    }

    // la liste des mails déjà utilisé pour faire une réservation
    $mails_reservation = array_column($users, 'email');
?>

<!-- div principal -->

<div class="container mt-4">
    <!-- bouton retour vers la liste des reservation -->
    <div class="d-flex justify-content-end mb-2">
        <button class="btn btn-sm btn-outline-secondary btn-retour-liste-res-admin" data-retour-mode="reset-liste">
            <i class="bi bi-arrow-left"></i> Retour à la liste des réservations
        </button>
    </div>

    <h3 class="mb-4 text-dark">Validation des réservations</h3>

    <!-- zone de retour admin pour afficher les messages de validation, mot de passe et message mail -->
    <div id="admin-retour" class="mb-3"></div>
    
    <!-- utilisation de Bootstrap pour le style du tableau -->
    <table class="table table-hover align-middle">

    <!-- utilisation de Bootstrap pour le style du tableau -->
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
            /** un script pour afficher les réservations en attente 
             * et calculer la disponibilité des chambres **/

                // parcour des réservation pour afficher uniquement les en attente
                foreach ($attentes as $reservation) :
                    if (($reservation['status'] ?? '') == 'en attente') :
                        // on récupère les informations de la chambre choisie pour cette réservation
                        $chambre_id = (int)($reservation['chambre_choisie'] ?? 0);
                        $date_debut_actuelle = $reservation['date_debut'] ?? '';
                        $date_fin_actuelle = $reservation['date_fin'] ?? '';

                        $chambre_info = $chambres_par_id[$chambre_id] ?? null;
                        $stock_chambre = (int)($chambre_info['disponible'] ?? 0);
                        $nom_chambre = $chambre_info['nom'] ?? 'Bungalow inconnu';

                        // occupation calculee avec les reservations validees qui se chevauchent sur la meme chambre
                        $occupation = 0;
                        foreach ($attentes as $autre) {
                            if ((int)($autre['id_res'] ?? 0) === (int)($reservation['id_res'] ?? 0)) {
                                continue;
                            }

                            if ((int)($autre['chambre_choisie'] ?? 0) !== $chambre_id) {
                                continue;
                            }

                            if (($autre['status'] ?? '') !== 'validé') {
                                continue;
                            }

                            $autre_debut = $autre['date_debut'] ?? '';
                            $autre_fin = $autre['date_fin'] ?? '';
                            $chevauche = $date_debut_actuelle < $autre_fin && $date_fin_actuelle > $autre_debut;
                            if ($chevauche) {
                                $occupation++;
                            }
                        }

                        $disponibilite = $stock_chambre - $occupation;
                        $dispo_ok = $disponibilite > 0;
                    ?>
            <!-- chaque ligne de réservation on un id different : ca permet de cibler chaque réservation lors des actions -->
            <tr id="row-<?php echo $reservation['id_res']; ?>" class="tr-res" data-id="<?php echo $reservation['id_res']; ?>" style="cursor: pointer;">
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
                    <strong><?php echo htmlspecialchars($nom_chambre); ?></strong><br>
                    <span class="badge bg-info text-dark"><?php echo htmlspecialchars($reservation['nb_pers']); ?> Pers.</span><br>
                    <?php if ($dispo_ok): ?>
                        <span class="badge bg-success mt-1"><?php echo htmlspecialchars($disponibilite); ?> chambre(s) libre(s)</span>
                    <?php else: ?>
                        <span class="badge bg-danger mt-1">Complet sur ces dates</span>
                    <?php endif; ?>
                </td>



                <!--Affichage du type de client : savoir s'il est déjà membre en vérifiant son mail -->
                <td>

                
                    <?php echo in_array($reservation['email'], $mails_reservation) ?
                        "<span class='badge bg-success'>Membre</span>" :
                        "<span class='badge bg-secondary'>Non Membre</span>"; 
                    ?>
                    
                </td>
                

                <!--Bouton d'acceptation de la réservation -->
                <td class="text-end">
                    
                <!--Bouton pour visualiser les détails de la réservation-->
                    <button class="btn btn-examiner btn-outline-primary ">Examiner <i class="bi bi-search"></i>
                    </button>
                </td>
                        <!-- utilisation de Bootstrap pour le style du bouton -->
                <!--data id et mail permet de passer les informations à la fonction JavaScript-->
                    
            </tr>
            <tr id="details-<?php echo $reservation['id_res']; ?>" class="d-none bg-light">
                <td colspan="5">
                    <div class="container-details p-3"></div>
                </td>

            </tr>
                <!--fermeture de la boucle et de la conditions-->
            <?php 
                endif;  
                endforeach;
            ?>
        </tbody>
    </table>
</div>
    
