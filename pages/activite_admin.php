<?php

/**
 * Page de gestion des activités prevu pour tout les clients
 * on y trouve une vue journalière des demandes d'activité 
 * formulaire pour créer les groupes d'activités 
 * et une liste des groupes déjà validés pour garder une trace de l'organisation
 * **/




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

    // informations pour le bouton de retour vers la page admin avec la liste de validation
    $totem_retour_admin = [
        'couloir' => 'admin-liste',
        'label' => 'Retour à la liste des réservations'
    ];

    // groupes d'activités deja validés
    $path_prevues = "../data/activite_prevue.json";
    $activites_prevues = [];
    if (file_exists($path_prevues)) {
        $activites_prevues = json_decode(file_get_contents($path_prevues), true) ?: [];
    }

    // animateurs deja affectes : on les retire des listes de selection pour les autres groupes
    $animateurs_occupes = array_values(array_unique(array_filter(array_map(function($grp) {
        return trim((string)($grp['animateur'] ?? ''));
    }, $activites_prevues))));

    // filtre de date pour avoir une vue journaliere simple
    $date_jour = $_GET['date_jour'] ?? date('Y-m-d');

    // mini index des activites pour retrouver unite/prix rapidement
    $index_activites = [];
    foreach (($offres['activite'] ?? []) as $act) {
        $index_activites[(string)$act['id']] = $act;
    }

    // mêmes règles que le traitement pour guider l'admin visuellement
    $regles_activites = [
        1 => ['min' => 1, 'max' => 12],
        2 => ['min' => 2, 'max' => 6],
        3 => ['min' => 2, 'max' => 8]
    ];

    
    // les demandes d'activités validées, regroupées par id d'activité
    $demandes_activites = [];
    $index_reservations = [];
    foreach ($reservations as $res) {
        $index_reservations[(string)($res['id_res'] ?? '')] = $res;
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

    // affichage d'une vue journaliere des demandes d'activités :
    // pour la journée choisie, on affiche toutes les demandes 
    // d'activités des clients qui sont en séjour ce jour là
    $demandes_jour = [];

    // trouver les reserv avec des activites prevus pour la journée choisie
    foreach ($reservations as $res) {
        if (($res['status'] ?? '') !== 'validé' || empty($res['activites'])) {
            continue;
        }

        // on vérifie que la reservation chevauche la journée choisie
        $debut_ok = strtotime($res['date_debut'] ?? '') ?: 0;
        $fin_ok = strtotime($res['date_fin'] ?? '') ?: 0;
        $jour_ok = strtotime($date_jour) ?: 0;

        // si la date choisie n'est pas dans la période de séjour,
        //  on skip 
        if (!$debut_ok || !$fin_ok || !$jour_ok || $jour_ok < $debut_ok || $jour_ok > $fin_ok) {
            continue;
        }

        // sinon , on ajoute les activités de cette reservation à la vue journaliere
        // chaque activité de la reservation va générer une ligne 
        // dans la vue journaliere,qui indique si elle est satisfaite ou pas 
        // (c'est à dire si elle fait partie des activités prévues validées par l'admin)
        foreach ($res['activites'] as $act_id) {
            $act_key = (string)$act_id;
            $satisfaite = false;
            // on vérifie si cette activité est dans les activités prévues validées par l'admin pour cette journée
            foreach ($activites_prevues as $grp) {

                $participants = array_map('strval', $grp['participants'] ?? []);

                if ((string)($grp['id_activite'] ?? '') === $act_key && 
                    in_array((string)$res['id_res'], $participants, true)) {
                    $satisfaite = true;
                    break;
                }
            }

            // on ajoute la demande d'activité à la vue journaliere
            $demandes_jour[] = [
                'id_res' => $res['id_res'],
                'nom_client' => $res['nom_client'] ?? $res['nom'] ?? 'Inconnu',
                'nom_activite' => $index_activites[$act_key]['nom'] ?? ('Activité #' . $act_key),
                'unite' => $index_activites[$act_key]['unite'] ?? 'non précisée',
                'satisfaite' => $satisfaite
            ];
        }
    }

    // statistiques de la vue journaliere
    // nombre total de demandes d'activités pour la journée choisie
    $total_jour = count($demandes_jour);
    $non_satisfaites_jour = count(array_filter($demandes_jour, function($d){ return !$d['satisfaite']; }));

    // groupes déjà validés, triés du plus récent au plus ancien
    $groupes_valides = $activites_prevues;
    usort($groupes_valides, function($a, $b) {
        return strcmp((string)($b['date_creation'] ?? ''), (string)($a['date_creation'] ?? ''));
    });
?>
<!-- div principal -->
<div class="container mt-4">
    <!-- bouton retour vers la page admin avec la liste de validation -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-sm btn-outline-dark btn-retour-liste-res-admin" data-retour-mode="<?php echo htmlspecialchars($totem_retour_admin['couloir']); ?>">
            <i class="bi bi-arrow-left"></i> <?php echo htmlspecialchars($totem_retour_admin['label']); ?>
        </button>
    </div>

    <h3 class="mb-3"><i class="bi bi-person-fill"></i> Organisation des groupes</h3>
    <p class="text-muted small">Gérez les activités et les groupes d'intervenants ici.</p>

    <!-- vue par journée  avec statistiques-->
    <div class="card mb-4 border-info shadow-sm">
        <div class="card-header bg-info-subtle d-flex justify-content-between align-items-center">
            <strong><i class="bi bi-calendar-event"></i> Demandes d'activités du jour</strong>
            <span class="badge bg-dark"><?php echo htmlspecialchars($date_jour); ?></span>
        </div>
        <!-- Corps de la carte -->
        <div class="card-body">
            <div class="alert alert-light border py-2">
                <small>
                    Règle appliquée : une demande d'activité non satisfaite reste visible chaque jour du séjour
                    jusqu'à validation d'un groupe correspondant.
                </small>
            </div>

            <!-- Formulaire de filtre de date et d'activité -->
            <form class="form-filtre-date-activite-admin row g-2 mb-3">
                <div class="col-md-4">
                    <!-- choix d'une date dans un petit calendrier -->
                    <label class="form-label small">Choisir une date :</label>
                    <input type="date" name="date_jour" class="form-control form-control-sm" value="<?php echo htmlspecialchars($date_jour); ?>">
                </div>
                    <!-- bouton filter -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-info w-100 btn-filtre-activite-admin">Filtrer</button>
                </div>
                <!-- bouton de réinitialisation du filtre -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100 btn-reset-filtre-activite-admin">Réinitialiser</button>
                </div>
                <!-- affichage des statistiques de la vue journaliere -->
                <div class="col-md-4 d-flex align-items-end justify-content-md-end">
                    <small class="text-muted">Demandes : <?php echo $total_jour; ?> | Non satisfaites : <?php echo $non_satisfaites_jour; ?></small>
                </div>
            </form>


            <!-- Dans le cas où il n'y a pas de demandes pour la journée -->
            <?php if (empty($demandes_jour)) : ?>
                <div class="alert alert-light border mb-0">Aucune demande d'activité ce jour.</div>
            <?php else : ?>
                <!-- Tableau des demandes d'activités -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Activité</th>
                                <th>Unité</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- on affiche les demandes une par une -->
                            <?php foreach ($demandes_jour as $d) : ?>
                                <tr>
                            <!-- Affichage des détails de la demande d'activité 
                             avec le nom du client , le nom de l'activité et l'unité -->
                                    <td><?php echo htmlspecialchars($d['nom_client']); ?> <small class="text-muted">(#<?php echo htmlspecialchars($d['id_res']); ?>)</small></td>
                                    <td><?php echo htmlspecialchars($d['nom_activite']); ?></td>
                                    <td><?php echo htmlspecialchars($d['unite']); ?></td>
                                    <td>
                                        <?php if ($d['satisfaite']) : ?>
                                            <span class="badge bg-success">Satisfaite</span>
                                        <?php else : ?>
                                            <span class="badge bg-warning text-dark">Non satisfaite</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- bloc pour distinguer les groupes déjà validés -->
    <div class="card mb-4 border-success shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <strong><i class="bi bi-check2-circle"></i> Groupes déjà validés</strong>
            <span class="badge bg-light text-success"><?php echo count($groupes_valides); ?></span>
        </div>

        <!-- Corps de la carte -->
        <div class="card-body">
            <!-- Dans le cas où il n'y a pas de groupes validés -->
            <?php if (empty($groupes_valides)) : ?>
                <div class="alert alert-light border mb-0">Aucun groupe validé pour le moment.</div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Activité</th>
                                <th>Animateur</th>
                                <th>Participants</th>
                                <th>Message</th>
                                <th>Date création</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- on affiche les groupes une par une -->
                            <?php foreach ($groupes_valides as $groupe) : ?>
                                <?php
                                // pour chaque groupe, 
                                // on récupère les noms des participants à partir de leurs id_res
                                    $participants = [];
                                    foreach (($groupe['participants'] ?? []) as $id_res_part) {
                                        $res_part = $index_reservations[(string)$id_res_part] ?? null;
                                        if ($res_part) {
                                            $participants[] = $res_part['nom'] ?? $res_part['nom_client'] ?? ('Réservation #' . $id_res_part);
                                        }
                                    }
                                    $nom_activite_groupe = $index_activites[(string)($groupe['id_activite'] ?? '')]['nom'] ?? ($groupe['nom_activite'] ?? 'Activité inconnue');
                                ?>
                                <tr>
                            <!-- Affichage des détails du groupe -->
                                    <td><?php echo htmlspecialchars($nom_activite_groupe); ?></td>
                                    <td><?php echo htmlspecialchars($groupe['animateur'] ?? 'Non défini'); ?></td>
                                    <td>
                            <!--La liste des participants du groupe est affichée ici-->
                                        <?php if (!empty($participants)) : ?>
                                            <?php echo htmlspecialchars(implode(', ', $participants)); ?>
                                        <?php else : ?>
                                            <span class="text-muted">Aucun participant</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Affichage du message du groupe si l'admin a écrit un message -->
                                        <?php if (!empty(trim((string)($groupe['message'] ?? '')))) : ?>
                                            <?php echo htmlspecialchars($groupe['message']); ?>
                                        <?php else : ?>
                                            <span class="text-muted">Aucun message</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($groupe['date_creation'] ?? ''); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

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
        <!-- nombre de demandes d'activités pour l'activité précis -->
                <span class="badge bg-white text-primary"><?php echo count($demandes_activites[$act_id]); ?></span>
            </div>
            <div class="card-body">
                <form class="form-groupe-activite" method="post">
                    <input type="hidden" name="id_activite" value="<?php echo htmlspecialchars($act_id); ?>">
                    <input type="hidden" name="nom_activite" value="<?php echo htmlspecialchars($act['nom']); ?>">
                    
                    <!-- Affichage des règles de l'activité : le nombre de personnes minimum et maximum-->
                    <?php if (isset($regles_activites[(int)$act_id])) : ?>
                        <div class="alert alert-secondary py-2 mb-2">
                            <small>
                                Règle activité : minimum <?php echo $regles_activites[(int)$act_id]['min']; ?> personne(s),
                                maximum <?php echo $regles_activites[(int)$act_id]['max']; ?> personne(s).
                            </small>
                        </div>
                    <?php endif; ?>

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

                    <!-- message partagé avec tous les participants de l'activité -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Message activité (visible par les participants) :</label>
                        <textarea name="message_activite" class="form-control form-control-sm" rows="2" placeholder="Ex: RDV à 9h au ponton, prévoir une casquette."></textarea>
                    </div>

                    <!--div qui permet de choisir un animateur pour l'activité et de valider le groupe -->
                    <div class="row bg-light p-3 rounded mt-2 g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Assigner un Responsable : </label>
                            <select name="animateur" class="form-select" required>

                                <!-- demande de selection d'un guide -->
                                <option value="">Sélectionnez un animateur</option>
                                <optgroup label="Guides">
                                    <?php foreach (($staffs['guides'] ?? []) as $g) : ?>

                                    <!-- on verifie s'il est actif ou pas-->
                                        <?php if ($g['actif'] && !in_array($g['nom'], $animateurs_occupes, true)) : ?>
                                            <option value="<?php echo htmlspecialchars($g['nom']); ?>">
                                                <?php echo htmlspecialchars($g['nom']); ?> (<?php echo htmlspecialchars($g['domaine']); ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>

                                <!-- demande de selection d'un moniteur -->
                                <optgroup label="Moniteurs">
                                    <?php foreach (($staffs['moniteurs'] ?? []) as $m) : ?>
                                        <?php if ($m['actif'] && !in_array($m['nom'], $animateurs_occupes, true)) : ?>
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
    



