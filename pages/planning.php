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

    // on vq filtrer les reservqtion pour uniquement prendre les validé
    $valides = array_filter($reservations, function($res) {
        if (!isset($res['status'])) {
            return mb_strtolower($res['status'], 'UTF-8') === 'validé';
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

    <h3 class="mb-4 ">Planning des réservations</h3>

    <!-- utilisation de Bootstrap pour le style du tableau -->
<!-- on verfie si des reservations sont trouvees--> 
    <?php if (empty($valides)) : ?>
        <p>Aucune réservation validée pour le moment.</p>
    <?php else : ?>

    <!--on les affiche dans un tableau de boostrap-->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>

    <!--les donner a afficher son ici -->
                    <tr>
                        <th>Dates</th>
                        <th>Client</th>
                        <th>Bungalow</th>
                        <th>Nombre de Voyageur</th>
                    </tr>
                </thead>
                <tbody>

    <!--on parcourt les reservations valides et on recupere leurs donneers une par une-->
                    <?php foreach ($valides as $res) : ?>
                        <tr>
                            <td>Du <strong><?php echo htmlspecialchars($res['date_debut']); ?></strong><br>
                                Au <strong><?php echo htmlspecialchars($res['date_fin']); ?></strong >
                            </td>
                            <td><?php echo htmlspecialchars($res['nom']); ?></td>
                            <!-- on affiche le type de bungalow et le nombre de voyageur pour chaque reservation-->
                            <td>Type : <?php echo htmlspecialchars($res['chambre_choisie']); ?></td>
                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($res['nb_pers']); ?>pers.</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    <?php endif; ?>
</div>