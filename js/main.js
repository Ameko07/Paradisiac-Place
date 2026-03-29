
$(document).ready(function(){


    /**---------------DEBUT PARTIE ACCUEIL --------------------**/
    // accueil : l'accueil s'affiche de base , mais se réaffiche quand on reviens en appuyant sur le bouton accueil 
    $('#menu-accueil').click(function(event){
        // on emppeche le navigateur de suivre le lien 
        event.preventDefault();

        //la page d'accueil est la page par defaut
        $('#main-content').load('pages/accueil.php');
            
    });

    /**------FIN PARTIE ACCUEIL**/

    /**----- PARTIE ADMIN -----**/

    // page de l'administration à load par clique sur le bouton en haut à droite
    $('#menu-admin').click(function(event){
        event.preventDefault();
        // on load la page admin 
        $('#main-content').load('../admin.php', function(response,status,xhr){
            if (status == "success"){
                // injection du tableau de validation des reservation pour l'admin 
                $("#zone_tableau").load('pages/admin_valid.php');
            }
            // en cas d'erreur de chargement du fichier on renvoie error
            else{
                $("#zone_tableau").html("<p class='text-danger'> ERREUR :  Impossible de charger le tableau des réservations.</p>");

            }

        });
    });

    $(document).on('click', '#menu-valider-res', function(){
        // on emppeche le navigateur de suivre le lien
        event.preventDefault();

            // on load la page de validation des reservation pour l'admin
            $("#zone_tableau").load('pages/admin_valid.php')
    });

    /**bouton details**/
    $(document).on('click', '.btn-details', function(){
        // on emppeche le navigateur de suivre le lien
        let id = $(this).data('id');
        $(`#details-${id}`).toggleClass('d-none'); // toggle pour afficher ou cacher les détails
    
    });

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
                if(response.trim() === "success"){
                    // affichage du message de succès
                    $("#admin-retour").html("<div class='alert alert-success py-1'> Réservation validée avec succès ! et Membre confirmé</div>");
                    
                    // on supprime la ligne de la réservation validée du tableau
                    $(`#row-${id} , #details-${id}`).fadeOut(500, function(){
                        $(this).remove(); // suppression de la ligne après l'animation de disparition
                    });
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


    /** gestion des plannigns **/
    $('#menu-gestion-planning').click(function(event){
        event.preventDefault();
        $('#main-content').load('pages/planning.php', function(response,status,xhr){

            // affichage d'erreur si  un probleme survient
            if (status == "error"){
                $("#main-content").html("<p class='text-danger'> ERREUR :  Impossible de charger le planning.</p>");

            }

        });
    });
    

    


    /**----FIN PARTIE ADMIN ----**/


    /**---- PARTIE RESERVATION -----**/ 


    // ativation par clique sur le bouton reserver
    $('#menu-reserver').click(function(event){
        // on emppeche le navigateur de suivre le lien 
        event.preventDefault();

        // charger le fichier de reservation avec ajax
        // en gros lancer tout le contenue dans la div central
        $('#main-content').load('pages/reservation.php', function(response,status,xhr){
            
            // en cas d'erreur de chargement du fichier on renvoie error
            if (status == "error"){
                $("#main-content").html("<p class='text-danger'> ERREUR :  Impossible de charger le formulaire.</p>");

            }

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

    });


    /**---------FIN PARTIE RESERVATION -------------------**/

    


    

    
});