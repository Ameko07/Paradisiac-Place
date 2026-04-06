<?php

/**
 * Page de gestion des activités prevu pour tout les clients
 * **/

// TODO : ajouter une animation fading pour les activité valider 
// TODO : ne pas afficher des moniteurs inactif 
    session_start();
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        header('Location: ../index.php');
        exit ("Accès refusé, vous n'êtes pas un admin");
    }

    // liste des reservations validé pour recuperer les activités choisies par les clients
    $json_res = file_get_contents("../data/reservation.json");
    $reservations = json_decode($json_res, true) ?: []; // au cas ou le fichier est vide

    // les offres pour récupérer les info des prestations
    $json_offres = file_get_contents("../data/offre.json");
    $offres = json_decode($json_offres, true) ?: []; // au cas ou le fichier est vide

    // la liste des intervenants
    $staff_json = file_get_contents("../data/staff.json");
    $staffs = json_decode($staff_json, true) ?: []; // au cas ou le fichier est vide

    
    // les demandes d'activités validées, regroupées par id d'activité
    $demandes_activites = [];
    foreach ($reservations as $res) {
        if ($res['status'] == 'validé' && !empty($res['activites'])) {
            foreach ($res['activites'] as $act_id) {
                if (!isset($demandes_activites[$act_id])) {
                    $demandes_activites[$act_id] = [];
                }

                $demandes_activites[$act_id][] = [
                    'id_res' => $res['id_res'],
                    'nom_client' => $res['nom_client'] ?? $res['nom'] ?? 'Inconnu',
                    'debut' => $res['date_debut'],
                    'fin' => $res['date_fin'],
                ];
            }
        }
    }
?>
<!-- div principal -->
<div class="container mt-4">
    <h3 class="mb-3"><i class="bi bi-person-fill"></i> Organisation des groupes</h3>
    <p class="text-muted small">Gérez les activités et les groupes d'intervenants ici.</p>

<!--Petit message d'information pour signaler qu'aucune activité n'a été réservée-->
    <?php if (empty($demandes_activites)): ?>
        <div class="alert alert-info" >
            Aucune activité n'a été réservée pour le moment.
        </div>
    <?php endif; ?>

    <!-- Liste des activités -->
        <?php foreach ($offres['activite'] as $act) : 
            $act_id = $act['id'];
             // si aucune réservation pour cette activité, on passe à la suivante
            if (empty($demandes_activites[$act_id])) continue;
            
        ?>
    <!-- une carte pour chaque activité -->
        <div class="card mb-4 border-primary shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?php echo htmlspecialchars($act['nom']); ?></h5>
                <span class="badge bg-white text-primary"><?php echo count($demandes_activites[$act_id]); ?></span>
            </div>
            <div class="card-body">
                <form class="form-groupe-activite" method="post">
                    <input type="hidden" name="id_activite" value="<?php echo htmlspecialchars($act_id); ?>">
                    <input type="hidden" name="nom_activite" value="<?php echo htmlspecialchars($act['nom']); ?>">

                    <!--un tableau pour afficher les client et leur periodes de sejours-->
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">Sel.</th>
                                <th>Client</th>
                                <th>Periode de séjour</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- on affiche les demandes une par une pour chaque activité -->
                            <?php foreach ($demandes_activites[$act_id] as $dem) : ?>
                                <tr>
                                    <td><input type="checkbox" name="clients[]" value="<?php echo htmlspecialchars($dem['id_res']); ?>" class="form-check-input"></td>
                                    <td><strong><?php echo htmlspecialchars($dem['nom_client']); ?></strong></td>
                                    <td><small>Du <?php echo htmlspecialchars($dem['debut']); ?> au <?php echo htmlspecialchars($dem['fin']); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!--div qui permet de choisir un animateur pour l'activité-->
                    <div class="row bg-light p-3 rounded mt-2 g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Assigner un Responsable : </label>
                            <select name="animateur" class="form-select" required>

                                <!-- demande de selection d'un guide -->
                                <option value="">Sélectionnez un animateur</option>
                                <optgroup label="Guides">
                                    <?php foreach (($staffs['guides'] ?? []) as $g) : ?>

                                    <!-- on verifie s'il est actif ou pas-->
                                        <?php if ($g['actif']) : ?>
                                            <option value="<?php echo htmlspecialchars($g['nom']); ?>">
                                                <?php echo htmlspecialchars($g['nom']); ?> (<?php echo htmlspecialchars($g['domaine']); ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>

                                <!-- demande de selection d'un moniteur -->
                                <optgroup label="Moniteurs">
                                    <?php foreach (($staffs['moniteurs'] ?? []) as $m) : ?>
                                        <?php if ($m['actif']) : ?>
                                            <option value="<?php echo htmlspecialchars($m['nom']); ?>">
                                                <?php echo htmlspecialchars($m['nom']); ?> (<?php echo htmlspecialchars($m['domaine']); ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>
                        <!--Div pour le dernier bouton de validation-->
                        <div class="col-md-6 d-flex align-items-end ">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-all"></i>Valider le groupe
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
    



