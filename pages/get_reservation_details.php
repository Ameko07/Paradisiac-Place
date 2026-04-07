<?php
    /** Un script pour récupérer les détails d'une reservation 
     * les afficher 
     * et traiter les demandes 
     * **/

    // Démarrage de la session pour récupérer les infos de l'admin connecté
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        exit ("Accès refusé, vous n'êtes pas un admin");
    }


    // récupération de l'id de la réservation à afficher
    $id = $_GET['id'] ?? null;
    if ($id === null) {
        echo "<div class='text-danger'>Identifiant de réservation manquant.</div>";
        exit;
    }
    $path_reservation = "../data/reservation.json";
    
    // lecture du fichier de reservation 
    $json_reservation = file_get_contents($path_reservation);
    $reservations = json_decode($json_reservation, true) ?: [];

    $json_offres = file_get_contents("../data/offre.json");
    $offres = json_decode($json_offres, true);

    


    // on va chercher la reservation avec l'id correspondant
    // variable de reservation actuelle
    $current = null;
    foreach ($reservations as $r) {
        if ($r['id_res'] == $id) {
            $current = $r;
            break;
        }
    }

    if (!$current) {
        echo "<div class='text-warning'>Réservation introuvable.</div>";
        exit;
    }

    // on va calculer la disponibilité de la chambre choisie pour cette reservation
    $bungalo_info = null;
    foreach ($offres['chambre'] as $c) {
        if ($c['id'] == $current['chambre_choisie']) {
            $bungalo_info = $c;
            break;
        }
    }

    

    $occupation = 0;
    $chambre_id = $current['chambre_choisie'];
    foreach ($reservations as $r) {

        // on ne compte que les réservations validées qui concernent la même chambre
        if ($r['id_res'] != $id && $r['chambre_choisie'] == $chambre_id && $r['status'] == 'validé') {

            $chevauche = $current['date_debut'] < $r['date_fin'] && $current['date_fin'] > $r['date_debut'];
            // chevauchage de date ? 
            if ($chevauche) {
                $occupation ++;
            } 
        }
    }

    // disponibilite de la chambre
    $stock_chambre = $bungalo_info['disponible'];
    // nombre de chambre dispo 
    $disponibilite = $stock_chambre - $occupation;
     
    
    $dispo_ok = ($disponibilite > 0);


    $mdp = "admin123"; // mot de passe pour valider une reservation 
    //$mdp = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 8);
    

?>

<div class="card border-primary">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <!-- Titre de la section -->
        <h5 class="mb-0">Examen de la réservation numéro <?php echo htmlspecialchars($id); ?></h5>

        <!--Disponibilité de la chambre et nombre de chambres disponibles -->
        <?php if ($dispo_ok): ?>
            <span class="badge bg-success">Chambre disponible (<?php echo htmlspecialchars($disponibilite); ?> chambre(s) de libre) </span>
        <?php else: ?>  
            <span class="badge bg-danger">Chambre non disponible (0 chambre(s) de libre)</span>
        <?php endif; ?>
    </div>
    
    <!-- Corps de la carte avec les détails de la réservation -->
     <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6> Détails de la réservation :</h6>

                <!-- Affichage des détails de la réservation -->
                <p>
                    <strong>Type de bungalow :</strong> <?php echo htmlspecialchars($bungalo_info['nom']); ?><br>
                    <strong>Capacité :</strong> <?php echo htmlspecialchars($bungalo_info['capacite']); ?><br>
                    <strong>Dates de séjour :</strong> du <?php echo htmlspecialchars($current['date_debut']); ?> au <?php echo htmlspecialchars($current['date_fin']); ?><br>
                    <strong>Nombre de personnes :</strong> <?php echo htmlspecialchars($current['nombre_personnes']); ?><br>
                </p>
        <!--liste des activite aue le client a choisi-->
                <h6> Activités souhaitées:</h6>

        <!--Affichage en structure de liste -->
                    <ul>
        <!-- Le un script qui verifie si la liste des ativite est vide ou non-->
                        <?php 
                        if (!empty($current['activites'])) {

                        // on le parcours en affichant le nom un par un 
                            foreach ($current['activites'] as $activite) { 
                                foreach ($offres['activite'] as $a) {
                                    if ($a['id'] == $activite) echo "<li>" . htmlspecialchars($a['nom']) . "</li>";
                                }
                            }
                        } else {
                            // si rien est trouvé on affiche que c'est vide
                            echo "<li>Aucune activité choisie</li>";
                        }

                        ?>

                    </ul>
            </div>
            <div class="col-md-6">
                <!-- Affichage des ACTIONS de l'admin-->
                <h6>Actions de l'admininstrateur:</h6>

                <div class="card border-0 bg-light mb-3">
                    <div class="card-body py-2">
                        <h6 class="mb-2">Modifier les arrhes reçues</h6>
                        <form action="pages/update_paiement.php" method="POST" class="d-inline form-update-arrhes">
                            <input type="hidden" name="id_res" value="<?php echo htmlspecialchars($current['id_res']); ?>">
                            <div class="input-group input-group-sm">
                                <input type="number" name="montant_arrhes" class="form-control" min="0" step="0.01" value="<?php echo htmlspecialchars($current['arrhes'] ?? 0); ?>" placeholder="Arrhes reçues">
                                <button class="btn btn-primary" type="submit">OK</button>
                            </div>
                        </form>
                        <small class="text-muted">Montant actuel : <?php echo number_format((float)($current['arrhes'] ?? 0), 2); ?>€</small>
                    </div>
                </div>

                <!-- Si la chambre est disponible, on affiche le message pour le client et le bouton de validation -->
                <?php if($dispo_ok):?>
                    <div class="alert alert-warning py-2">
                        <small><strong>Message pour le client :</strong><br>
                        "Bonjour <?php echo htmlspecialchars($current['nom_client']); ?>, votre réservation pour le bungalow <?php echo htmlspecialchars($bungalo_info['nom']); ?>  a été acceptée.
                        Veuillez vous connecter à l'espace client pour qvoir plus de détails concernant votre réservtion. 
                        Pour vous connecter, utilisez votre adresse email et le mot de passe suivant : <strong><?php echo htmlspecialchars($mdp); ?></strong>"
                    </small>
                    </div>

                    <!-- le bouton de validation qui s'affiche si la chambre est dispo-->
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-valider"
                            data-id="<?php echo htmlspecialchars($id); ?>"
                            data-email="<?php echo htmlspecialchars($current['email']); ?>"
                            data-mdp="<?php echo htmlspecialchars($mdp); ?>">
                            <i class="bi bi-check-circle"></i> Confirmer la réservation et Créer le compte client
                        </button>
                    <?php endif; ?>

                    <button class="btn btn-outline-danger btn-refuser"
                        data-id="<?php echo htmlspecialchars($id); ?>">
                        <i class="bi bi-x-circle"></i> Refuser la réservation
                    </button>
                                            
                    </div>
                
            </div>
        </div>

    </div>

</div>
