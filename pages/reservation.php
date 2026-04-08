



<!-- pour faire simple cette partie va être injecté dans index et non afficher comme étant une nouvelle page.
 de toute façon j'ai ajouté un contenaire dans index pour contenir ce qui change au niveau de la vue du client. -->


<?php // catalogue des offres 
// on récupère toutes les données des offres
    $json = file_get_contents("../data/offre.json"); 
    $offres = json_decode($json,true);
?>

<!-- Les Divisions pour séparer visuellement chaque sections-->
<div class="container mt-4">
    <div class="card shadow-sm p-4">
        <h2 class="text-primary mb-4">Demande de Réservation</h2>
        <form id="form-reservation">
            <div class="row">
<!-- input pour les coordonnee de l'utilisateur -->
<!-- le nom-->
                <div class="col-md-6 mb-3">
                    <label>Votre nom :</label>
<!--le Input pour taper le nom avec un exemple -->
                    <input type="text" name="nom" class="form-control" placeholder="Ex: Jean Dupont" required>
                </div>
<!-- le mail-->

                <div class="col-md-6 mb-3">
                    <label>Votre mail :</label>
<!--Le mail à entrer et un exemple spécifique pour 
que l'utilisateur ne rentre pas un faux mail -->
                    <input type="email" name="email" class="form-control" placeholder="jean@exemple.com" required>
            
                </div>
            </div>

<!--La date d'arrivée-->
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Arrivée :</label>

<!--Un format de date classique à entrer avec le type "date"-->
                    <input type="date" name="date_debut" class="form-control" required>
                </div>

<!--La date de départ-->
            
                <div class="col-md-4 mb-3">
                    <label>Départ :</label>

<!--Un format de date classique à entrer avec le type "date"-->
                    <input type="date" name="date_fin" class="form-control" required>
                </div>

<!--nombre de personne-->
                <div class="col-md-4 mb-3">
                    <label>Nombre de personne :</label>
<!--un type number pour récuperer plutard le nombre de personne à recevoir pendant le séjour-->
                    <input type="number" name="nb_pers" class="form-control" min=1 value="1" required>
                </div>
            </div>

<!--choix des chambres-->
            <div class="mb-3">
                <label>Hébergement souhaité :</label>
<!--Une forme select pour que l'utilisateur puisse juste sélectionner 
au lieu de devoir cherher les noms des chambres-->
                
                <select name="chambre_choisie" class="form-select">
                    <option value = "1"> Un bungalow qu'on choisi pour vous </option>
                    <?php 
                    
                    // parcour de la liste des offres de chambre et afficher les détails de chacun
                        foreach($offres['chambre'] as $chambre){
                            echo "<option value ='".htmlspecialchars($chambre['id'])."'>".htmlspecialchars($chambre['nom'])." (".htmlspecialchars($chambre['prix'])."€/nuit)</option>";
                        }
                    ?>
                </select>
            </div>

<!----choix des prestations -->
            <div class="mb-4">
                <label class="fw-bold text-secondary mb-2">Activités souhaitées :</label>
                <div class="row">
                    <!-- Affichage des options de activités -->
                    <?php foreach ($offres["activite"] as $activite) :?>
                        <div class="col-md-6 mb-2">
                    <!-- Affichage de chaque prestation avec leur nom et leur prix et des cases à cocher pour sélectionner les activités-->
                            <div class="form-check border rounded p-2 shadow-sm" >
                                <input class="form-check-input ms-1" type="checkbox" 
                                            name="activites[]" 
                                            value="<?php echo $activite['id'] ?>" 
                                            id="activite-<?php echo $activite['id'] ?>">
                                <label class="form-check-label ms-2" for="activite-<?=$activite['id'] ?>">
                                    <?= htmlspecialchars($activite['nom']) ?> 
                                    <span class="badge bg-info text-dark"><?= $activite['prix'] ?>€</span>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>


<!--le formulaire du commentaire -->
            <div class="mb-3">
                <label>Commentaires :</label>
<!--un text Area pour entréer un commentaire personnalisé pour les activité choisis -->
                <textarea name="message" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Soumettre la demande</button>
        </form>

        <div id="patienter" class="mt-3"></div>

    </div>

</div>