# Rapport - PARADISIAC-PLACE

## Description du projet
PARADISIAC-PLACE est une application web de reservation de sejours a Madagascar.
Le projet propose un flux complet entre client et administration :

- le client depose une demande de reservation,
- l'administration valide ou refuse la demande,
- un espace client affiche le recapitulatif et la facture,
- des activites peuvent etre organisees et rattachees aux clients.

Le projet est developpe dans un cadre pedagogique, avec une architecture simple et lisible.

## Technologies utilisees
- Front-end : HTML, CSS, Bootstrap, JavaScript, jQuery
- Back-end : PHP procedural
- Persistance : fichiers JSON
- Communication : appels AJAX pour charger les vues et envoyer les donnees sans rechargement complet

## Fonctionnalites principales

### 1. Cote client
- Consultation de l'accueil et de la page decouverte
- Formulaire de demande de reservation :
  - nom, email, dates, nombre de personnes
  - choix de l'hebergement
  - choix d'activites
  - commentaire libre
- Envoi de la demande avec confirmation
- Connexion a l'espace client apres validation admin
- Affichage de la facture : hebergement, prestations, activites, arrhes et reduction

### 2. Cote administration
- Connexion admin
- Affichage des reservations en attente
- Consultation detaillee d'une reservation
- Validation ou refus d'une demande
- Generation d'un compte client lors de la validation
- Mise a jour des informations de paiement : arrhes et reduction
- Message pret a copier pour informer le client

### 3. Gestion des activites
- Vue des demandes d'activites par jour
- Filtrage par date
- Creation/validation de groupes avec animateur
- Respect des regles de capacite (minimum/maximum selon activite)
- Affichage separe des groupes deja valides

## Description de l'interface

### Interface publique
- Barre de navigation claire : Accueil, Decouvrir, Reserver, Client, Admin
- Zone centrale dynamique qui charge les pages avec AJAX
- Design responsive base sur Bootstrap pour PC et mobile

### Interface reservation
- Formulaire structure en cartes et champs clairs
- Messages de retour utilisateur pour succes/erreur
- Contraintes de saisie sur les dates et le nombre de personnes

### Interface administration
- Menu dedie a la gestion des reservations et activites
- Tableau des demandes en attente avec actions rapides
- Detail reservation affichable sans quitter la vue
- Messages d'etat pendant les traitements (chargement, succes, erreur)

### Interface client
- Espace personnel apres connexion
- Facture lisible avec lignes de deduction
- Actions possibles sur certaines prestations

## Structure generale du projet
```
Paradisiac-Place/
|- index.php
|- admin.php
|- css/
|- js/
|- pages/
|- data/
`- README.md
```

## Conclusion
Le projet couvre les besoins fonctionnels essentiels demandes dans le sujet :
reservation, validation administrative, suivi client, gestion financiere et organisation d'activites.
Il respecte aussi l'esprit du cours avec une implementation PHP + jQuery + JSON simple, propre et operationnelle.
flock($flockOp, LOCK_UN); // Libération
```

**Utilité :** Éviter les problèmes de concurrence si plusieurs admins valident simultanément

### Sécurité - htmlspecialchars()
```php
htmlspecialchars($data)
```
**Utilité :** Prévenir les injections XSS lors de l'affichage des données en HTML

### Opérateur de coalescence nulle (??)
```php
$nom = $_POST['nom'] ?? ''; // Évite les erreurs si clé vide
```

---

## 📝 Code principal JavaScript (`main.js`)

### Structure générale
Le fichier est organisé en 3 parties principales :

**1. PARTIE ACCUEIL**
- Charge `pages/accueil.php` au clic sur menu
- Page par défaut

**2. PARTIE ADMIN**
- Charge `admin.php` au clic sur menu
- Injecte `pages/admin_valid.php` dans zone tableau
- Gestion des clics sur boutons de validation
- Requête AJAX vers `pages/traitement_validation.php`

**3. PARTIE RÉSERVATION**
- Charge `pages/reservation.php` au clic sur menu
- Gestion soumission formulaire
- Requête AJAX vers `pages/sauvegarde_reservation.php`
- Affichage messages feedbacks

### Événements principaux
```javascript
// Délégation d'événements pour éléments dynamiques
$(document).on('click', '.btn-valider', function(){...})
$(document).on('submit', '#form-reservation', function(){...})
```

---

## 🚨 Problèmes et TODOs identifiés

### 🔴 Bug connu
- **Fichier :** `pages/admin_valid.php` ligne ~60
- **Problème :** Condition IF pour vérifier le statut "membre" ne fonctionne pas correctement
- **Impact :** Le badge membre/non-membre peut ne pas s'afficher correctement
- **Solution requise :** Correction de la logique IF

### ⚠️ Améliorations à apporter

1. **Gestion du planning**
   - `planning.json` n'est pas utilisé
   - À implémenter : système de vérification des disponibilités
   - À ajouter : gestion des conflits de dates

2. **Système de mots de passe**
   - Mot de passe par défaut dur-codé : "mada123"
   - À changer : système de génération sécurisé + hachage (PHP password_hash)

3. **Interface client**
   - Les clients validés n'ont pas d'espace personnel
   - À ajouter : page "Mon compte" / "Historique"

4. **Page "Client"**
   - Menu "Client" existe mais non implémenté
   - À définir : Quels doivent être les fonctionnalités ?

5. **Gestion du planning admin**
   - Menu "Gestion Planning" existe mais non implémenté
   - À définir : Interface de gestion des disponibilités

6. **Validation des dates**
   - Pas de vérification si dates_fin > dates_debut
   - Pas de vérification de conflits de dates

7. **Stockage sécurisé**
   - Actuellement : fichiers JSON en lecture/écriture directe
   - À considérer : Migration vers base de données (MySQL)

8. **Traçabilité**
   - Pas de logs des actions admin
   - À ajouter : historique des modifications

---

## 📊 Données actuelles dans les fichiers JSON

### Réservations enregistrées (3 réservations)
1. Jean Dupon - 10-15 juin 2026 - 2 personnes - Bungalow 1 - **En attente**
2. Naruto Uzumaki - 27-29 mars 2026 - 1 personne - Bungalow 1 - **En attente**
3. Kaka - 5-16 avril 2026 - 1 personne - (données partielles)

### Clients validés
- 1 client : Jean Dupon (acompte: 50€, aucune réduction)

---

## 🎓 Contexte académique

- **Institution :** Université (Licence/Master informatique)
- **Matière :** Programmation Web (ProgWeb)
- **Projet :** Création d'une plateforme e-commerce/réservation
- **Constraints :** 
  - Utilisation de Bootstrap autorisée pour le style
  - Interaction AJAX requise
  - Gestion JSON (pas de base de données SQL)

---

## 📅 État actuel du projet

### ✅ Complété
- [x] Interface accueil basique
- [x] Formulaire de réservation avec validation HTML5
- [x] Système de sauvegarde des réservations en JSON
- [x] Interface admin avec tableau de validation
- [x] Système de validation des réservations (status update)
- [x] Création automatique de clients (users.json)
- [x] Navigation dynamique via AJAX
- [x] Design responsive avec Bootstrap

### 🟡 Partiellement complété
- [ ] Menu "Client" - Créé mais non implémenté
- [ ] Menu "Gestion Planning" - Créé mais non implémenté
- [ ] Fichier `planning.json` - Structure vide, non utilisé

### ❌ À faire
- [ ] Corriger le bug du badge membre/non-membre
- [ ] Implémenter l'espace client personnel
- [ ] Implémenter la gestion du planning/disponibilités
- [ ] Sécuriser la gestion des mots de passe
- [ ] Ajouter la vérification des conflits de dates
- [ ] Implémenter un système de logs
- [ ] Migrer vers une base de données
- [ ] Ajouter authentification

---

## 🔍 Points clés à comprendre

1. **Pas de base de données :** Toutes les données sont en fichiers JSON
2. **AJAX asynchrone :** Les pages se chargent sans rechargement complet
3. **Statut des réservations :** Deux états - "en attente" vs "validé"
4. **Lien client-réservation :** Créé lors de la validation par l'email
5. **Génération d'ID :** Incrémentale basée sur le dernier ID enregistré

---

## 📌 Pour continuer le développement

1. **Corriger le bug signalé** dans admin_valid.php
2. **Implémenter la gestion du planning** (vérification disponibilités)
3. **Créer l'espace client** (consultation profil, historique)
4. **Sécuriser les mots de passe** avec password_hash/verify
5. **Ajouter une base de données** pour mieux structurer les données
6. **Implémenter l'authentification** (login/logout)

---

**Document généré le :** 27 mars 2026  
**Dernière mise à jour :** Voir la structure actuelle du projet  
**Auteur :** L'équipe de développement

---

## ✅ Campagne de tests finale (Avril 2026)

### Tests Connexion et rôles
- [x] Connexion admin valide -> ouverture espace admin sans rechargement complet.
- [x] Connexion client valide -> ouverture espace client.
- [x] Session admin active + clic Client/Réserver -> garde-fou affiché, session admin conservée.

### Tests Réservations admin
- [x] Validation d'une réservation -> ligne retirée du tableau en attente.
- [x] Création compte client lors de validation -> mot de passe aléatoire généré côté serveur.
- [x] Message prêt à copier affiché (URL + identifiant + mot de passe).

### Tests Paiement et facture
- [x] Mise à jour arrhes via admin -> visible côté facture client en ligne négative.
- [x] Réduction limitée à 0/10/20/50.
- [x] Réduction appliquée uniquement sur les prestations.

### Tests Activités
- [x] Vue admin "demandes du jour" fonctionnelle avec filtre date.
- [x] Statut satisfaite/non satisfaite affiché.
- [x] Non satisfaite reportée sur les jours du séjour tant que non validée.
- [x] Règles métiers de groupe appliquées (min/max participants selon activité).
- [x] Message activité saisi par admin et visible par les participants côté client.

### Tests Robustesse JSON
- [x] Format legacy des prestations normalisé (objet -> tableau).
- [x] Ajout/suppression de prestations stable après normalisation.

---

## 🎤 Checklist Démo Orale (5 minutes)

### Minute 1 - Présentation rapide
- [ ] Présenter le concept du lieu de rêve et les rôles (client / admin).
- [ ] Montrer que l'application est principalement mono-page (chargements AJAX).

### Minute 2 - Parcours client
- [ ] Faire une demande de réservation (nom, email, dates, nb pers, activités).
- [ ] Montrer la confirmation côté interface.

### Minute 3 - Parcours admin
- [ ] Ouvrir validation des réservations et examiner une demande.
- [ ] Valider la réservation et montrer : mot de passe aléatoire + message prêt à copier.

### Minute 4 - Facture client
- [ ] Se connecter en client avec les identifiants générés.
- [ ] Montrer la facture : hébergement + prestations + activités validées.
- [ ] Montrer les lignes négatives : réduction prestations + arrhes.

### Minute 5 - Activités et règles métier
- [ ] Ouvrir la gestion des activités du jour (filtre date).
- [ ] Montrer une contrainte min/max participants (refus puis succès).
- [ ] Montrer le message d'activité visible dans la facture des participants.
