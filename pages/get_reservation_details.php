<?php
    /** Un script pour récupérer les détails d'une reservation 
     * les afficher 
     * et traiter les demandes 
     * **/

    // Démarrage de la session pour récupérer les infos de l'admin connecté
    session_start();
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

    // calcul des dates de séjour
    $chambre_id= $current['chambre_choisie'];
    $stock_chambre = 0;

    foreach ($offres['chambre'] as $c) {
        if ($c['id'] == $chambre_id) {
            $stock_chambre = $c['disponible'];
            $nom_chambre = $c['nom'];
            break;
        }
    }

    

    $occupation = 0;
    foreach ($reservations as $r) {

    // on ne compte que les reservqtion validé 
        if ($r['id_res'] != $id && $r['chambre_choisie'] == $chambre_id && $r['status'] == 'validé') {

            $chevauche = $current['date_debut'] < $r['date_fin'] && $current['date_fin'] > $r['date_debut'];
            // chevauchage de date ? 
            if ($chevauche) {
                $occupation ++;
            } 
        }
    }

    // disponibilite de l chambre
    $disponibilite = $stock_chambre - $occupation;

    // lecture du fichier des planning pour vérifier les disponibilités
    $json_planning = file_get_contents("../data/planning.json");
    $planning = json_decode($json_planning, true) ?: [];

    $conflict_activite = [];
    foreach ($planning as $act) {
        if ($act['date'] >= $current['date_debut'] && $act['date'] <= $current['date_fin']) {
            $conflict_activite[] = $act;
        }
    }

    
?>

<div class="row align-items-center">
    <div class="col-md-7">
        <h6>Message du client : </h6>
        <p class="text-muted"><em> "<?php echo htmlspecialchars($current['message'] ?? 'Aucun message'); ?>"</em></p>
        <p><strong> Analyse de la réservation : </strong> <em><?php echo htmlspecialchars($nom_chambre ); ?> (Stock : <?php echo $stock_chambre; ?> | libres : <?php echo $disponibilite; ?>)</em></p>
        
    </div>

    <div class="col-md-5 text-end">
        <div id="btn-zone-<?php echo $id; ?>">
            <?php if ($disponibilite > 0) : ?>
                <button class="btn btn-success btn-valider" data-id="<?php echo $id; ?>" 
                data-email="<?php echo htmlspecialchars($current['email'] ?? ''); ?>">Valider</button>
            <?php else : ?>
                <button class="btn btn-secondary" disabled>Tout est Complet </button>
            <?php endif; ?>
            <button class="btn btn-danger btn-refuser" data-id="<?php echo $id; ?>"
             data-email="<?php echo htmlspecialchars($current['email'] ?? ''); ?>">Refuser</button>
        </div>
    </div>
</div>
