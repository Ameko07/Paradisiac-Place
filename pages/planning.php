<?php
    /**
     * -- -- Page de planning pour l'admin -- -- 
     * une page qui affiche l'ensemble des réservation validé pour pouvoir faire les plannings
     *  
     * **/   
    

    session_start();
    // vérification que l'utilisateur est bien un admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        // redirection vers la page d'accueil si ce n'est pas le cas
        header('Location: ../index.php');
        exit ("Accès refusé, vous n'êtes pas un admin");
    }


    //on va commencer par récupérer les reservations validé pour les afficher dans un tableau
    $json = file_get_contents("../data/reservation.json");
    $reservations = json_decode($json, true) ?: []; // au cas ou le fichier est vide

    $json_offres = file_get_contents("../data/offre.json");
    $offres = json_decode($json_offres, true);

    // un tableau par id de chambre pour les trouve plus facilement
    $nom_chambres = [];
    foreach ($offres['chambre'] as $c) {
        $nom_chambres[$c['id']] = $c['nom'];
    }

    // on vq filtrer les reservqtion pour uniquement prendre les validé
    $valides = array_filter($reservations, function($res) {
        if (isset($res['status'])) {
            return strtolower($res['status']) === 'validé';
        }
        return false; 
    });

    // petit trie par date de début 
    usort($valides, function($a, $b) {    
        return strtotime($a['date_debut']) - strtotime($b['date_debut']);
    });



?>

<!-- Le conteneur pour afficher le planning -->

<div class="container mt-4">

    <h3 class="mb-4 "><i class="bi bi-calendar-check"></i> Planning des réservations</h3>

    <!-- utilisation de Bootstrap pour le style du tableau -->
<!-- on verfie si des reservations sont trouvees--> 
    <?php if (empty($valides)) : ?>
        <div class="alert alert-info">
            Aucune réservation validée pour le moment.
        </div>
    <?php else : ?>

    <!--on les affiche dans un tableau de boostrap-->
        <div class="table-responsive shadow-sm">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">

    <!--les donner a afficher son ici -->
                    <tr>
                        <th>Dates du séjour</th>
                        <th>Client</th>
                        <th>Hébergement</th>
                        <th>Nombre de personnes</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>

    <!--on parcourt les reservations valides et on recupere leurs donneers une par une-->
                    <?php foreach ($valides as $res) : ?>
                        <tr>
                            <td>
                                <small>Du</small>  <strong><?php echo htmlspecialchars($res['date_debut']); ?></strong><br>
                                <small>Au</small>   <strong><?php echo htmlspecialchars($res['date_fin']); ?></strong >
                            </td>
                            <td>
                                <!-- on affiche le nom du client pour chaque reservation-->
                                <strong><?php echo htmlspecialchars($res['nom']?? $res['nom_client']); ?></strong>
                                <span class="text-muted small"><br><?php echo htmlspecialchars($res['email']?? $res['email_client']); ?></span>
                            </td>
                            <!-- on affiche le type de bungalow et le nombre de voyageur pour chaque reservation-->
                            <td>
                                <i class="bi bi-house-door"></i><?php echo isset($nom_chambres[$res['chambre_choisie']]) ? htmlspecialchars($nom_chambres[$res['chambre_choisie']]) : 'Inconnu'; ?> 
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">
                                    <?php echo htmlspecialchars($res['nb_pers']?? $res['nombre_personnes']); ?>pers.</span>
                                </td>
                            <td>
                                <span class="badge bg-success small">Validé</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
        <div class="row mt-5">
            <h5><i class="bi bi-pie-chart"></i>Résumé des types de Bungalows :</h5>
            <?php 
                // on va compter le nombre de réservation pour chaque type de bungalow
                foreach ($offres['chambre'] as $c) :
                    $count = 0;
                    // seulement pour les réservations validées
                    foreach ($valides as $res) {
                        if ($res['chambre_choisie'] == $c['id']) {
                            $count++;
                        }
                    }            
            ?>

            <!-- on affiche le nombre de réservation pour chaque type de bungalow dans une card de bootstrap -->
            <div class="col-md-4">
                
                <div class="card bg-light">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><?php echo htmlspecialchars($c['nom']); ?></h6>
                        <small class="text-muted">Nombre de réservations enregistrées : <?php echo $count; ?></small>
                    </div>
                </div>
                
            </div>
            <?php endforeach ?>
        </div>
    <?php endif; ?>
</div>