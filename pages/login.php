<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!--La page login est utilisé pour 2 connexions différentes
    Connexion en tant que client
    Connexion en tant qu'admin
-->

    <div class="container mt-5" >
    <!-- Le div en conteneur qui va contenir la page de connexion -->

        <div class="row justify-content-center">
    <!-- on centre la page de connexion dans le conteneur-->
            <div class="col-md-5">
                <div  class="card shadow border-0">
    <!-- La carte de connexion est un conteneur stylisé pour la page de connexion -->
                    <div id="login-header" class="card-header bg-primary text-white text-center py-3">
    <!--l'entete du conteneur de connexion-->
                        <h4 id="login-title" class="mb-0">Connexion</h4>
                    </div>
    <!--Le corps de la carte-->
                    <div class="card-body p-4">
                        <p id="login-subtitle" class="text-muted text-center small">Veuillez entrer vos identifiants pour vous connecter.</p>

    <!--Le formulaire à remplir avec les identifiant et le mot de passe-->
                        <form id="form-login">
                            <input type="hidden" id="type-connexion" name="type_connexion" value="">

    <!--Demande d'entrer l'identifiant qui est le mail du client, s'il n'est pas membre il devra faire une reservation -->
                            <div class="mb-3">
                                <label for="identifiant" for="identifiant" class="form-control-label">Email ou Identifiant </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="identifiant" id="identifiant" class="form-control"
                                        placeholder="nom@exemple.com"required>
                                </div>
                            </div>

                    
    <!--Entrer le mot de passe-->
                            <div class="mb-3">
                                <label for="password" class="form-control-label">Mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="********"required>
                                </div>
                                
                            </div>
    <!--le bouton de connexion-->
                            <div class="d-grid gap-2 mb-4">
                                <button type="submit" id="btn-login" class="btn btn-primary btn-lg">Se connecter</button>
                            </div>
                            
                        
                        </form>
    <!-- Message de retour pour les erreurs de connexion -->
                        <div id="login-retour" class="mt-3 text-center"></div>
                    </div>

                    <div id="login-footer" class="card-footer bg-light text-center py-3">
                        <small class="text-muted"> Pas encore Membre ? <br> Effectuer une reservation pour devenir membre </small>
                    </div>
                </div>
            
                
            </div>

        </div>
    </div>
 
</body>
</html>