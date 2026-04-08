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


    // le mot de passe sera genere au moment de la validation serveur
    

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
                    <strong>Nombre de personnes :</strong> <?php echo htmlspecialchars($current['nombre_personnes'] ?? $current['nb_pers'] ?? 'Non précisé'); ?><br>
                </p>
        <!--liste des activite que le client a choisi-->
                <h6> Activités souhaitées:</h6>

        <!--Affichage en structure de liste -->
                    <ul>
        <!-- un script qui verifie si la liste des ativite est vide ou non-->
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

        <!-- Formulaire de mise à jour du paiement -->
        <!-- s'affiche lorsque la chambre est dispo -->
                <div class="mt-3 p-3 bg-light border rounded">
                    <h6><i class="bi bi-cash"></i> Gestion financière</h6>
                    <form class="form-update-paiement row g-2">
                        <input type="hidden" name="id_res" value="<?php echo htmlspecialchars($current['id_res']); ?>">
                        <div class="col-md-5">
                        <!-- Affichage des montants des arrhes et de la reduction-->
                        <!-- Les montant sont bien defini entre 0 et 100 avec min et max-->
                            <label class="small">Arrhes reçues (€) :</label>
                            <input type="number" name="montant_arrhes" class="form-control form-control-sm" min="0" step="0.01" 
                            value="<?php echo htmlspecialchars($current['arrhes'] ?? 0); ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="small">Réduction (%) :</label>

                        <!-- Les réductions autorisées sont 0, 10, 20 et 50% -->
                        <!--On verfie la conformite avec les valeurs autorisées-->
                            <select name="pourcentage_reduc" class="form-select form-select-sm">
                                <?php $reduc_actuelle = (float)($current['reduction'] ?? 0); ?>
                                <option value="0" <?php echo ($reduc_actuelle === 0.0) ? 'selected' : ''; ?>>0%</option>
                                <option value="10" <?php echo ($reduc_actuelle === 10.0) ? 'selected' : ''; ?>>10%</option>
                                <option value="20" <?php echo ($reduc_actuelle === 20.0) ? 'selected' : ''; ?>>20%</option>
                                <option value="50" <?php echo ($reduc_actuelle === 50.0) ? 'selected' : ''; ?>>50%</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Enregistrer</button>
                        </div>
                    </form>
                </div>

                <!-- Si la chambre est disponible, on affiche le message pour le client et le bouton de validation -->
                <?php if($dispo_ok):?>
                    <div class="alert alert-warning py-2">
                        <small><strong>Message pour le client :</strong><br>
                        "Bonjour <?php echo htmlspecialchars($current['nom'] ?? $current['nom_client'] ?? 'Client'); ?>, votre réservation pour le bungalow <?php echo htmlspecialchars($bungalo_info['nom']); ?> a été acceptée.
                        Veuillez vous connecter à l'espace client pour avoir plus de détails concernant votre réservation.
                        Le mot de passe sera généré automatiquement lors de la validation par l'administrateur."
                    </small>
                    </div>

                    <!-- le bouton de validation qui s'affiche si la chambre est dispo-->
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-valider"
                            data-id="<?php echo htmlspecialchars($id); ?>"
                            data-email="<?php echo htmlspecialchars($current['email']); ?>">
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
