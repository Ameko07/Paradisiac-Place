
$(document).ready(function(){

    
    $('#main-content').load('pages/accueil.php');


    /**---------------DEBUT PARTIE ACCUEIL --------------------**/

    /*Bouton d'accueil*/
    $('#menu-accueil').click(function(event){
        // on emppeche le navigateur de suivre le lien 
        event.preventDefault();

        //la page d'accueil est la page par defaut
        $('#main-content').load('pages/accueil.php');
            
    });
    /*bouton LOGO */
    $('#MadaDream').click(function(event){
        // on emppeche le navigateur de suivre le lien 
        event.preventDefault();
        $('#main-content').load('pages/accueil.php');
    });

    // page de présentation détaillée des activités et prestations
    $('#menu-decouvrir').click(function(event){
        event.preventDefault();
        $('#main-content').load('pages/decouvrir.php');
    });

    // boutons CTA de l'accueil/découvrir// Tout en bas de la page d'accueil et de la page découvrir 
    $(document).on('click', '.versReservation', function(event){
        event.preventDefault();
        $('#menu-reserver').trigger('click');
    });

    /* Bouton Espace Client */
    $(document).on('click', '.versEspaceClient', function(event){
        event.preventDefault();
        $('#menu-client').trigger('click');
    });

    /*bouton de retour à l'accueil*/
    $(document).on('click', '.versDecouverte, #btn-retour-accueil', function(event){
        event.preventDefault();
        // selon le bouton cliqué on charge la page d'accueil ou la page de découverte
        if ($(this).hasClass('versDecouverte')) {
            $('#main-content').load('pages/decouvrir.php');
        } else {
            $('#main-content').load('pages/accueil.php');
        }
    });


    /**------FIN PARTIE ACCUEIL**/

    /**----- PARTIE ADMIN -----**/
    /**-------------Partie Login --------------**/
    // page de l'administration à load par clique sur le bouton en haut à droite

    /* Bouton Admin */
    //gère à la fois l'ouvertur de l'espace admin et la redirection vers la page de connexion si l'admin n'est pas encore connecté
    $('#menu-admin').click(function(event){
        event.preventDefault();

        // on verifie d'abord l'etat de la session comme pour le menu client
        $.get('pages/check_session.php', function(role){
            if (role.trim() === "admin") {
                // si admin deja connecte on ouvre directement l'espace admin
                $('#main-content').load('admin.php', function(response,status,xhr){
                    if (status == "success"){
                        $("#zone_tableau").load('pages/admin_valid.php');
                    }
                    else{
                        $("#main-content").html("<p class='text-danger'> ERREUR : Impossible de charger l'espace admin.</p>");
                    }
                });
            } else {
                // sinon on charge la page d'authentification de l'admin
                $('#main-content').load('pages/login.php', function(){
                    // chargement du formulaire perso pour l'admin
                    $('#type-connexion').val('admin');
                    $('#login-header').removeClass('bg-primary').addClass('bg-dark');
                    $('#login-title').text('Espace Admin');
                    $('#btn-login').removeClass('bg-primary').addClass('btn-dark');
                    $('#login-footer').hide();
                });
            }
        });
    });

// gestion de la soumission du formulaire de connexion pour l'admin et le client
    /* Bouton de connexion */
    $(document).on('submit', '#form-login', function(event){
        // on emppeche le navigateur de suivre le lien
        event.preventDefault();
        let donnees = $(this).serialize();

        /* Appel ajax qui permet de vérifier les identifiants, 
            afficher la partie admin ou sinon renvoyer une erreur */
        $.ajax({
            url: 'pages/verif_connexion.php',
            method: 'POST',
            data: donnees,
            success: function(response){
                res = response.trim();

            if (res === "success_admin"){
                // on load la page admin 
                $('#main-content').load('admin.php', function(response,status,xhr){
                    if (status == "success"){
                        // injection du tableau de validation des reservation pour l'admin 
                        $("#zone_tableau").load('pages/admin_valid.php');
                    }
                    // en cas d'erreur de chargement du fichier on renvoie error
                    else{
                        $("#zone_tableau").html("<p class='text-danger'> ERREUR :  Impossible de charger le tableau des réservations.</p>");

                    }

                });
            }
            else if (res === "success_client"){
                // c'est le client
                $('#main-content').load('pages/espace_client.php');
                $('#menu-client').text('Espace Client'); // on change le texte du menu pour indiquer que le client est connecté
            }
            else{
                // sinon une erreur
                $('#login-retour').html("<div class='alert alert-danger'> " + response + "</div>");
            }
        },
        error: function(){
            $('#login-retour').html("<div class='alert alert-danger'> ERREUR : Impossible de traiter la connexion. Veuillez réessayer plus tard.</div>");
        }
        });
    });

    /** --------FIN PARTIE LOGIN--------***/

    /**------- PARTIE VALIDATION DES RESERVATIONS -------**/

    /** Bouton de menu de validation des réservations **/ 

    $(document).on('click', '#menu-valid-res', function(event){
        // on emppeche le navigateur de suivre le lien
        event.preventDefault();

            // on load la page de validation des reservation pour l'admin
            $("#zone_tableau").load('pages/admin_valid.php')
    });

    // mise a jour des arrhes depuis le detail reservation admin
    $(document).on('submit', '.form-update-arrhes', function(event){
        event.preventDefault();
        // récupération du formulaire, du bouton de submit et de l'id de la réservation
        let form = $(this);
        let btn = form.find('button[type="submit"]');
        let id = form.find('input[name="id_res"]').val();
        //on désactive le bouton pour ne pas faire des double clics
        btn.prop('disabled', true);
        $("#admin-retour").html("<div class='text-info'><span class='spinner-border spinner-border-sm text-info'></span> Mise à jour des arrhes...</div>");

        //appel ajax pour mettre à jour les arrhes de la reservation
        $.ajax({
            url: 'pages/update_paiement.php',
            method: 'POST',
            data: form.serialize(),
            success: function(response){
                if (response.trim() === "success") {
                    $("#admin-retour").html("<div class='alert alert-success py-1'>Arrhes mises à jour avec succès.</div>");

                    // recharge uniquement la ligne de details ouverte
                    let target = $("#details-" + id).find('.container-details');
                    target.load('pages/get_reservation_details.php?id=' + id);
                } else {
                    $("#admin-retour").html("<div class='alert alert-danger py-1'>ERREUR au niveau du serveur: " + response + "</div>");
                }
                btn.prop('disabled', false);
            },
            error: function(){
                $("#admin-retour").html("<div class='alert alert-danger py-1'>ERREUR : Impossible de mettre à jour les arrhes.</div>");
                btn.prop('disabled', false);
            }
        });
    });

    // mise a jour paiement : arrhes et reduction depuis le detail reservation admin
    $(document).on('submit', '.form-update-paiement', function(e) {
        e.preventDefault();

        // récupération du formulaire, du bouton de submit et de l'id de la réservation
        let form = $(this);
        let btn = form.find('button[type="submit"]');
        let id = form.find('input[name="id_res"]').val();


        // desactivation du bouton pour éviter les double clics et message de chargement
        btn.prop('disabled', true);
        $("#admin-retour").html("<div class='text-info'><span class='spinner-border spinner-border-sm text-info'></span> Mise à jour du paiement...</div>");

        // appel ajax pour mettre à jour les arrhes et la reduction de la reservation
        $.ajax({
            url: 'pages/update_paiement.php',
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.trim() === "success") {
                    $("#admin-retour").html("<div class='alert alert-success py-1'>Paiement mis à jour !</div>");

                    // recharge uniquement la ligne de details ouverte pour mettre à jour les arrhes et la reduction
                    let target = $("#details-" + id).find('.container-details');
                    target.load('pages/get_reservation_details.php?id=' + id);
                } else {
                    $("#admin-retour").html("<div class='alert alert-danger py-1'>ERREUR au niveau du serveur: " + response + "</div>");
                }
                btn.prop('disabled', false);
            },
            error: function(){
                $("#admin-retour").html("<div class='alert alert-danger py-1'>ERREUR : Impossible de mettre à jour le paiement.</div>");
                btn.prop('disabled', false);
            }
        });
    });

    /**gestion des réservations**/
    // bouton examiner
    $(document).on('click', '.btn-examiner', function(event){

        // pour éviter que le clic sur le bouton n'affecte la ligne entière
        event.stopPropagation(); 

        // on récupère l'id de la reservation à exminer grace à la ligne du tableau 
        let tr = $(this).closest('tr');
        let id = tr.data('id');
        // on cible la ligne de détails qui correspond
        let detailsRow = $(`#details-${id}`);

        // si on voit déjà la ligne , on la cache 
        if (!detailsRow.hasClass('d-none')){
            detailsRow.addClass('d-none');
        }else{
            // sinon on l'affiche et on charge tout 
            // on ferme les autres lignes ouvertent avant d'ouvrir celle ci
            $("tr[id^='details-']").addClass('d-none');
            detailsRow.removeClass('d-none');
            let target = detailsRow.find('.container-details');

            target.html("<div class='text-center p-3'><div class='spinner-border' ></div>Chargement...</div>");
            target.load('pages/get_reservation_details.php?id=' + id);
        }


    }); 


    // bouton retour vers la liste des reservation dans la page de validation / planning / activité
    $(document).on('click', '.btn-retour-liste-res-admin', function(event){
        event.preventDefault();

        // on récupère le mode de retour pour savoir quelle page charger ,
        //  par défaut on charge la liste des reservation à valider
        let mode = $(this).data('retour-mode') || 'admin-liste';

        // les différentes fonctions de retour selon le mode
        let couloirs_admin = {

            // pour le mode "reset-liste" on ne recharge pas la page admin ,
            //  mais on replie juste les détails de reservation ouvert et on remonte en haut du tableau
            "reset-liste": function(){
                // on replie tout les details de reservation ouvert
                $("tr[id^='details-']").addClass('d-none');

                // retour visuel en haut du tableau
                let ancre = $('#table-reservations').closest('.container');
                if (ancre.length){
                    $('html, body').animate({ scrollTop: ancre.offset().top - 20 }, 220);
                }
            },
            "admin-liste": function(){
                // on recharge la page admin puis la liste des reservations à valider
                $('#main-content').load('admin.php', function(response,status,xhr){
                    if (status == "success"){
                        $("#zone_tableau").load('pages/admin_valid.php');
                    }
                    else{
                        $("#main-content").html("<p class='text-danger'> ERREUR : Impossible de revenir vers la liste des réservations.</p>");
                    }
                });
            }
        };

        // si mode inconnu on tente la voie normale admin-liste
        (couloirs_admin[mode] || couloirs_admin["admin-liste"])();
    });
                
    
    /** BOUTON DE VALIDATION DES RESERVATIONS **/
    // pour le fichier admin_valid.php
    // bouton accepter reservation pour l'admin 
    // tableau chargé dynamiquement 

    $(document).on('click', '.btn-valider', function(){

        // récupération de l'id de la reservation à valider
        let btn = $(this);
        let id  = btn.data('id');
        let mailClient = btn.data('email');
        

        // desactivation du bouton pour les double clic
        btn.parent().find('button').prop('disabled', true);
        // message patienter pour l'admin 
        $("#admin-retour").html("<div class='text-info'>"+ 
            "<span class='spinner-border spinner-border-sm text-info'></span>" +"Traitement en cours...</div>"); // on vide le message de retour pour éviter les confusions
        

        

        // appel ajax pour valider la reservation
        $.ajax({
            url: 'pages/traitement_validation.php',
            method: 'POST',
            data: { 
                id: id,
                mail: mailClient,
                action: 'valider',
                
            },
            success: function(response){
                // l'utilisation de trim permet de supprimer les espaces inutiles et problématiques
                // variable payload pour stocker la réponse du serveur après tentative de parsing en JSON
                let payload = null;
                let reponseTexte = (response || "").toString();
                try {
                    payload = JSON.parse(reponseTexte);
                } catch(e) {
                    payload = null;
                }

                // secours si la reponse contient du texte parasite autour du json
                
                /*if (!payload) {
                    let debutJson = reponseTexte.indexOf('{');
                    let finJson = reponseTexte.lastIndexOf('}');
                    if (debutJson !== -1 && finJson !== -1 && finJson > debutJson) {
                        try {
                            payload = JSON.parse(reponseTexte.substring(debutJson, finJson + 1));
                        } catch(err2) {
                            payload = null;
                        }
                    }
                }*/

                // si tout est OK 
                if((payload && payload.status === "success") || reponseTexte.trim() === "success" || reponseTexte.indexOf('"status":"success"') !== -1){
                    // affichage du message de succès
                    if (payload && payload.password) {
                        let basePath = window.location.pathname.split('/').slice(0, -1); // on enlève le dernier segment (admin_valid.php)
                        basePath.pop();
                        let urlConnexion = window.location.origin + basePath.join('/') + '/index.php';

                        let messageMail =
                            "Bonjour " + (payload.nom || "Client") + ",\n\n" +
                            "Votre réservation a été validée.\n" +
                            "Vous pouvez vous connecter à l'espace client avec les informations suivantes :\n\n" +
                            "URL : " + urlConnexion + "\n" +
                            "Identifiant : " + payload.email + "\n" +
                            "Mot de passe : " + payload.password + "\n\n" +
                            "À bientôt.";
                        
                        // affichage du message de succès avec les informations de connexion pour le client 
                        // et un textarea prêt à copier pour l'admin 
                        $("#admin-retour").html(
                            "<div class='alert alert-success py-1 mb-2'>" +
                            "Réservation validée avec succès !<br>" +
                            "Compte créé pour : <strong>" + payload.email + "</strong><br>" +
                            "Mot de passe généré : <strong>" + payload.password + "</strong>" +
                            "</div>" +
                            "<div class='card border-success'>" +
                                "<div class='card-body py-2'>" +
                                    "<label class='form-label small fw-bold mb-1'>Message prêt à copier dans votre mail :</label>" +
                                    "<textarea class='form-control form-control-sm mail-ready-admin' rows='8' readonly></textarea>" +
                                    "<button type='button' class='btn btn-sm btn-outline-success mt-2 btn-copier-mail-admin'>Copier le message</button>" +
                                "</div>" +
                            "</div>"
                        );

                        $("#admin-retour .mail-ready-admin").val(messageMail);
                    } else {
                        $("#admin-retour").html("<div class='alert alert-success py-1'> Réservation validée avec succès ! et Membre confirmé</div>");
                    }
                    
                    // on supprime la ligne de la réservation validée du tableau
                    $(`#row-${id}, #details-${id}`).fadeOut(500, function(){
                        $(this).remove(); // suppression de la ligne après l'animation de disparition
                    });

                    // sécurité : au cas ou fadeOut ne retire pas bien la ligne
                    setTimeout(function(){
                        $(`#row-${id}, #details-${id}`).remove();
                    }, 650);
                }else{ 
                    //sinon une erreur est affichée
                    $("#admin-retour").html("<div class='alert alert-danger py-1'> ERREUR au niveau du serveur: " + response + "</div>");
                    btn.parent().find('button').prop('disabled', false); // réactivation des boutons en cas d'erreur
                }
            },
            error: function(){
                // affichage du message d'erreur dans le cas d'une echec de requette
                $("#admin-retour").html("<div class='alert alert-danger py-1'> ERREUR : Impossible de valider la réservation. Veuillez réessayer plus tard.</div>");
            }
            
            
        });

       
    });

    // bouton copier du message mail admin
    $(document).on('click', '.btn-copier-mail-admin', function(event){
        event.preventDefault();

        // on cible la zone de texte qui contient le message à copier
        let zone = $(this).closest('.card').find('.mail-ready-admin');
        let texte = zone.val() || "";

        // si le texte est vide on ne fait rien pour éviter de copier un message vide dans le presse-papiers
        if (!texte) {
            return;
        }

        // copie du texte dans le presse-papiers en utilisant l'API Clipboard,
        //  sinon fallback à la méthode de sélection et execCommand : qui veut dire 
        // "exécuter la commande de copie" pour les anciens navigateurs
        let annoncerSucces = function(){
            $("#admin-retour").prepend("<div class='alert alert-info py-1 mb-2'>Message copié dans le presse-papiers.</div>");
        };

        let annoncerErreur = function(){
            $("#admin-retour").prepend("<div class='alert alert-danger py-1 mb-2'>Impossible de copier automatiquement. Sélectionnez le texte manuellement.</div>");
        };

        // vérification de la disponibilité de l'API Clipboard et 
        // tentative de copie
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(texte)
                .then(annoncerSucces)
                .catch(function(){

                    // en cas d'échec de l'API Clipboard, 
                    // on tente la méthode de secours qui consiste 
                    // à créer un textarea temporaire pour sélectionner 
                    // le texte et utiliser execCommand('copy')
                    let temp = document.createElement('textarea');
                    temp.value = texte;
                    temp.setAttribute('readonly', '');
                    temp.style.position = 'fixed';
                    temp.style.left = '-9999px';
                    document.body.appendChild(temp);
                    temp.select();
                    let ok = document.execCommand('copy');
                    document.body.removeChild(temp);
                    if (ok) {
                        annoncerSucces();
                    } else {
                        annoncerErreur();
                    }
                });
        } else {

            // fallback pour les anciens navigateurs : 
            // on crée un textarea temporaire pour sélectionner le texte et utiliser execCommand('copy')
            let temp = document.createElement('textarea');
            temp.value = texte;
            temp.setAttribute('readonly', '');
            temp.style.position = 'fixed';
            temp.style.left = '-9999px';
            document.body.appendChild(temp);
            temp.select();
            let ok = document.execCommand('copy');
            document.body.removeChild(temp);
            if (ok) {
                annoncerSucces();
            } else {
                annoncerErreur();
            }
        }
    });

     // refuser la reservation
    $(document).on('click', '.btn-refuser', function(){
        // récupération de l'id de la reservation à refuser
        let btn = $(this);
        let id = $(this).data('id');

        btn.parent().find('button').prop('disabled', true);
        // message patienter pour l'admin 
        $("#admin-retour").html("<div class='text-info'>"+ 
            "<span class='spinner-border spinner-border-sm text-info'></span>" +"Traitement en cours...</div>");
        
        if (confirm("Êtes-vous sûr de vouloir refuser cette réservation ?")){
            btn.closest('.btn-group').find('button').prop('disabled', true); // désactivation des boutons pour éviter les double clics
            $.post('pages/traitement_validation.php', {
                id: id,
                action: 'refuser'
            }, function(response){
                if(response.trim() === "success"){
                    // affichage du message de succès
                    // on supprime la ligne de la réservation refusée du tableau
                    // utilisation de fadeOut pour une transition plus fluide avant de supprimer la ligne du DOM
                    $(`#row-${id},#details-${id}`).fadeOut(500, function(){
                        $(this).remove(); // suppression de la ligne après l'animation de disparition
                    });
                    $("#admin-retour").html("<div class='alert alert-success py-1'> Réservation refusée !</div>");
                }
            });
        }
    });


    /** gestion des plannings 
     * le lien est injecté après connexion admin, donc on délègue l'événement sur le document
     * **/
    $(document).on('click', '#menu-gestion-planning', function(event){
        event.preventDefault();

        $('#main-content').load('pages/planning.php', function(response, status, xhr){
            // affichage d'erreur si un problème survient
            if (status == "error"){
                $("#main-content").html("<p class='text-danger'> ERREUR : Impossible de charger le planning.</p>");
            }
        });
    });

    $(document).on('click', '#menu-gestion-activite', function(event){
        event.preventDefault();

        $('#main-content').load('pages/activite_admin.php', function(response, status, xhr){
            if (status == "error"){
                $("#main-content").html("<p class='text-danger'> ERREUR : Impossible de charger la gestion des activités.</p>");
            }
        });
    });

    /** --------gestion des activiter et des groupes-------------**/
    
    $(document).on('submit', '.form-groupe-activite', function(event){
        event.preventDefault();

        let form = $(this);
        
        // appel ajax pour créer le groupe d'activité
        $.ajax({
            url: 'pages/traitement_groupe.php',
            method: 'POST',
            data: form.serialize(),
            success: function(response){
                if(response.trim() === "success"){
                    // affichage du message
                    alert("Groupe d'activité créé avec succès !");

                    // on recharge la page pour afficher le nouveau groupe dans la liste
                    $('#main-content').load('pages/activite_admin.php');
                }else{
                    alert("ERREUR au niveau du serveur: " + response);
                }
            },
            error: function(){
                alert("ERREUR : Impossible de créer le groupe d'activité. Veuillez réessayer plus tard.");
            }
        });
    });




    

    


    /**----FIN PARTIE ADMIN ----**/


    /**---- PARTIE RESERVATION -----**/ 


    // ativation par clique sur le bouton reserver
    $('#menu-reserver').click(function(event){
        // on emppeche le navigateur de suivre le lien 
        event.preventDefault();

        // garde-fou : si admin connecté, on évite de quitter son espace par erreur
        $.get('pages/check_session.php', function(role){
            if (role.trim() === "admin") {
                alert("Vous êtes connecté en admin. Utilisez le menu Admin ou déconnectez-vous pour tester une réservation client.");
                return;
            }

            // charger le fichier de reservation avec ajax
            // en gros lancer tout le contenue dans la div central
            $('#main-content').load('pages/reservation.php', function(response,status,xhr){
                
                // en cas d'erreur de chargement du fichier on renvoie error
                if (status == "error"){
                    $("#main-content").html("<p class='text-danger'> ERREUR :  Impossible de charger le formulaire.</p>");

                }

            });
        });
    });

    // Geston de la pages des reservation 
    // envoie par Ajax des données recceuilllies depuis le formulaire de reservation 
    $(document).on('submit', '#form-reservation', function(event){
        // anti rechargelent de la page 
        event.preventDefault();

        // récupération des données du formulaire
        let donnees = $(this).serialize();

        // tout piti message pour patienter 
        $('#patienter').html("<span class='text-info'> Envoi de la réservation en cours...</span>");
        
        // envoie des données grâce à Ajax
        $.ajax({
            url: 'pages/sauvegarde_reservation.php',
            method: 'POST',
            data: donnees,
            success: function(response){
                // affichage du message dans le cas succès 

                if(response.trim() === "success"){
                    $('#patienter').html("<div class='alert alert-success'> Réservation enregistrée avec succès ! Patientez un peu (jouez à valorant en attendant).</div>");
                }else{
                    $('#patienter').html("<div class='alert alert-danger'> ERREUR au niveau du serveur: " + response + "</div>");

                }
            },
            error: function(){
                // affichage du message dans le cas d'erreur
                $('#patienter').html("<div class='alert alert-danger'> ERREUR : Impossible d'envoyer la réservation. Veuillez réessayer plus tard.</div>");
            }
        })


    // filtre de date pour la vue journaliere des demandes d'activite
    $(document).on('click', '.btn-filtre-activite-admin', function(event){
        event.preventDefault();

        let form = $(this).closest('.form-filtre-date-activite-admin');
        let dateJour = form.find('input[name="date_jour"]').val() || '';
        let url = 'pages/activite_admin.php';

        if (dateJour) {
            url += '?date_jour=' + encodeURIComponent(dateJour);
        }

        $('#main-content').load(url, function(response, status, xhr){
            if (status == "error"){
                $("#main-content").html("<p class='text-danger'> ERREUR : Impossible de filtrer la gestion des activités.</p>");
            }
        });
    });

    $(document).on('click', '.btn-reset-filtre-activite-admin', function(event){
        event.preventDefault();

        $('#main-content').load('pages/activite_admin.php', function(response, status, xhr){
            if (status == "error"){
                $("#main-content").html("<p class='text-danger'> ERREUR : Impossible de réinitialiser le filtre des activités.</p>");
            }
        });
    });
    });


    /** scirpt permettant d'interdir au client de rentrer des dates invalides **/
    $(document).ready(function(){
        // pas de date déjà passée pour la date de début et de fin
        
        // la date d'aujourd'hui au format ISO (YYYY-MM-DD) pour l'attribut min des champs de date
        let today = new Date().toISOString().split('T')[0];

        $('input[name="date_debut"]').attr('min', today);
        $('input[name="date_debut"]').on('change', function(){
            let dateArrive  = $(this).val();

            if (dateArrive){
                // au moins le lendemain 
                let minDateFin = new Date(dateArrive);
                dateArrive.setDate(dateArrive.getDate() + 1); // on ajoute un jour à la date d'arrivée pour obtenir la date minimale de départ
                let minDateFinStr = minDateFin.toISOString().split('T')[0];

                let dateFinInput = $('input[name="date_fin"]');

                dateFinInput.attr('min', today);
                // si la date de fin est déjà sélectionnée et qu'elle est avant la nouvelle date d'arrivée, on la réinitialise
                if (dateFinInput.val() && dateFinInput.val() < minDateFinStr){
                    dateFinInput.val('');
                }
            }
        });

        // dernière vérification avant d'envoyer
        $('#form-reservation').on('submit', function(event){

            // récupération des dates d'arrivée et de départ pour vérifier que la date de départ est bien après la date d'arrivée
            let dateArrive = $('input[name="date_debut"]').val();
            let dateDepart = $('input[name="date_fin"]').val();
            // si les deux dates sont renseignées , on vérifie que la date de départ est après la date d'arrivée
            if (dateArrive && dateDepart){
                let dateArriveDate = new Date(dateArrive);
                let dateDepartDate = new Date(dateDepart);
                // si la date de départ est avant la date d'arrivée , on empêche l'envoi du formulaire
                if (dateArriveDate >= dateDepartDate){
                    event.preventDefault();
                    $('#patienter').html("<div class='alert alert-danger'> ERREUR : La date de départ doit être après la date d'arrivée.</div>");
                }
            }
        });
        
    });

    // affichage dynamique des montant lors de la selection des prestations




    /**---------FIN PARTIE RESERVATION -------------------**/

    
/** --- PARTIE CLIENT  -----**/

/** --- PARTIE CONNEXION LOGIN  -----**/

    $('#menu-client').click(function(event){
        event.preventDefault();

        $.get('pages/check_session.php', function(role){
            if (role.trim() === "client"){
                    // si le client est déjà connecté on le redirige vers son espace client
                    $('#main-content').load('pages/espace_client.php');
            } else if (role.trim() === "admin") {
                // cas spécial pour éviter l'impression de deconnexion admin
                alert("Session admin active. Déconnectez-vous d'abord si vous voulez ouvrir l'espace client.");
            }else{
                    // sinon on charge la page de connexion pour le client
                    
                $('#main-content').load('pages/login.php' , function(){
                    // chargement du formulaire perso pour le client 

                    $('#type-connexion').val('client');
                    $('#login-header').addClass('bg-primary');
                    $('#login-title').text('Espace Client');
                    $('#btn-login').addClass('btn-primary');
                    $('#label-email').text('votre adresse email');
                });
            }
        });

    });

    // calcul du montant estimé pour le client connecté

    // les offres de chambre avec leur prix
    let offres = [];

    // fonction pour recuperer les offres from Json 
    function chargerOffres(){
        $.getJSON('data/offre.json', function(data){
            offres = data;
        });

    }

    chargerOffres(); // appel de la fonction pour charger les offres au chargement de la page
    $(document).on('change', '#date-debut , #date-fin , #chambre', function(){
        
        
        // definition des variables pour les dates de début et de fin de la reservation
        let d1 = $('#date-debut').val();
        let d2 = $('#date-fin').val();
        let chambreID = $('#chambre').val();

        if (d1 && d2 && chambreID){
            let date1 = new Date(d1);
            let date2 = new Date(d2);

            if (date1 < date2){
                let diff = Math.abs(date2 - date1);
                // conversion de la durée en nombre de nuits
                let nuits = Math.ceil(diff / (1000 * 60 * 60 * 24)); 
                
                // récupération du prix de la chambre sélectionnée 
                // syntaxe de la fonction find : dans offres chaque offre est teste pour la comparaison ]
                // et on retoune celui qu on trouve
                let choixChambre = offres.find(offre => offre.id == chambreID);
                // techniquement on ne peut que trouver une chambre avec cet id car les id sont uniques ,
                //  mais on peut ne pas trouver de chambre si l'id n'existe pas dans le fichier Json
                let prixChambre = choixChambre ? choixChambre.prix : 0;

                let montant = nuits * prixChambre;

                $('#montant-estime').html(
                    "<div class='alert alert-info'> Montant estimé : <strong>" + montant + "€</strong> pour " + nuits + " nuits.</div>");
            }
        }

        
    });

    // gestion des ajout de prestations 
    $(document).on('submit', '.form-modifier-prestation', function(event){
        event.preventDefault();

        let form = $(this);
        let btn = form.find('button');
        let donnees = form.serialize();
        
        // désactivation du bouton pour éviter les double clics
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Chargement...'); 
            

        // appel ajax pour ajouter la prestation à la facture du client
        $.ajax({
            url: 'pages/modifier_prestation.php',
            method: 'POST',
            data: donnees,
            success: function(response){
                if(response.trim() === "success"){

                    form.fadeOut(300, function(){

                    // on recharge juste la page du client pour mettre à jour la facture et les prestations ajoutées
                        $('#main-content').load('pages/espace_client.php');
                    });
                }else{
                    alert("ERREUR au niveau du serveur: " + response);
                    btn.prop('disabled', false).html('Réessayer plus tard'); // réactivation du bouton en cas d'erreur
                }
            },
            error: function(){
                alert("ERREUR : Impossible de modifier la prestation. Veuillez réessayer plus tard.");
                btn.prop('disabled', false).html('Réessayer plus tard'); // réactivation du bouton en cas d'erreur
            }
        });
        
    });






/**----- FIN PARTIE CONNEXION LOGIN  -----**/

/**----- FIN PARTIE CLIENT  -----**/
    
});