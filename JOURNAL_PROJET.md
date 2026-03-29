# 📋 Journal Complet du Projet PARADISIAC-PLACE

## 🏖️ Vue d'ensemble du projet

**Nom du projet :** PARADISIAC-PLACE  
**Plateforme :** Plateforme de réservation de vacances à Madagascar  
**Nom commercial :** MadaDream - Plage de Madagascar  
**Type :** Application Web (PHP/JavaScript/HTML/CSS)  
**Objectif :** Permettre aux clients de réserver des séjours en bungalows à Madagascar avec validation administrative

---

## 📁 Architecture du projet

```
Paradisiac-Place/
├── index.php                          # Page principale (accueil client)
├── admin.php                          # Page d'administration
├── js/
│   └── main.js                        # Logique JavaScript (AJAX, événements)
├── pages/                             # Pages chargées dynamiquement via AJAX
│   ├── accueil.php                    # Contenu page d'accueil
│   ├── reservation.php                # Formulaire de réservation
│   ├── admin_valid.php                # Tableau de validation des réservations
│   ├── sauvegarde_reservation.php     # Traitement sauvegarde réservation
│   └── traitement_validation.php      # Traitement validation admin
└── data/                              # Données persistantes (JSON)
    ├── offre.json                     # Catalogue: chambres, activités, prestations
    ├── reservation.json               # Liste des réservations enregistrées
    ├── users.json                     # Liste des clients validés
    └── planning.json                  # Planning (non utilisé actuellement)
```

---

## 🎯 Fonctionnalités principales

### 1️⃣ **Pour les clients**

#### Page d'accueil
- Bienvenue à Madagascar avec présentation générale
- Navigation simple avec menu en haut de page
- Responsive design avec Bootstrap 5

#### Réservation de séjour
**Accessible via :** Menu "Réserver"  
**Formulaire contient :**
- Nom du client
- Email (validation email requise)
- Date d'arrivée (input date)
- Date de départ (input date)
- Nombre de personnes
- Choix du bungalow/hébergement (select)
  - Option auto: "Un bungalow qu'on choisi pour vous"
  - Options manuelles: Chargées depuis `offre.json`
    - Bungalow Sable Blanc (80€/nuit, 2 pers max)
    - Suite Lagon Turquoise (150€/nuit, 4 pers max)
- Commentaires (textarea optionnel)
- Bouton "Soumettre la demande"

**Processus :**
1. Formulaire soumis via AJAX
2. Données envoyées à `sauvegarde_reservation.php`
3. Nouvelle réservation créée avec `status: "en attente"`
4. ID généré automatiquement (id_res = dernier ID + 1)
5. Données sauvegardées dans `reservation.json`
6. Message de confirmation affichée au client

**Données enregistrées :**
```
{
  "id_res": 1,
  "nom": "Naruto Uzumaki",
  "email": "hinata@gmail.com",
  "date_debut": "2026-03-27",
  "date_fin": "2026-03-29",
  "nb_pers": "1",
  "chambre_choisie": "1",
  "message": "c'est un week-end en amoureux",
  "status": "en attente"
}
```

### 2️⃣ **Pour l'administrateur**

#### Interface admin
**Accessible via :** Menu "Admin" (bouton en haut à droite)

#### Tableau de validation des réservations
**Fonctionnalité :** Visualiser et valider les réservations en attente

**Colonnes du tableau :**
1. **Informations Client** : Nom + Email
2. **Dates de séjour** : Date d'arrivée et de départ
3. **Client membre** : Badge vert (Membre) ou badge jaune (Non membre)
4. **Réponse Admin** : Bouton "Accepter"

**Logique du badge membre :**
- Vérifie si l'email du client existe dans `users.json`
- Si oui : "Membre" (badge vert)
- Si non : "Non membre" (badge jaune)
- ⚠️ **TODO :** Bug signalé dans le code - vérification IF à corriger

**Actions admin :**
- Cliquer sur "Accepter" déclenche une requête AJAX
- La réservation passe de "en attente" à "validé"
- Un nouveau client est créé dans `users.json` si l'email n'existe pas déjà
- Affichage message de succès et animation fadeOut de la ligne
- ⚠️ **Futur :** Possibilité d'ajouter plusieurs admins en même temps (utilisation de flock pour éviter les conflits)

**Données client créées :**
```
{
  "id_c": 0,
  "nom": "Jean Dupon",
  "email": "Jean@gmail.com",
  "mdp": "mada123",          // Mot de passe par défaut
  "id_res": 1,              // Lié à la réservation
  "arrhes": 0,              // Acompte (à remplir)
  "reduction": 0            // Réduction (à remplir)
}
```

---

## 📊 Structure des données JSON

### `offre.json` - Catalogue des services
```json
{
  "chambre": [
    {
      "id": 1,
      "nom": "Bungalow Sable Blanc",
      "prix": 80,
      "capacite": 2
    },
    {
      "id": 2,
      "nom": "Suite Lagon Turquoise",
      "prix": 150,
      "capacite": 4
    }
  ],
  "activite": [
    {
      "id": 1,
      "nom": "Observation des Lémuriens",
      "unite": "demi-journee",
      "prix": 40
    },
    {
      "id": 2,
      "nom": "Plongée Barrière de Corail",
      "unite": "heure",
      "prix": 25
    },
    {
      "id": 3,
      "nom": "Expédition Quad Désert",
      "unite": "journee",
      "prix": 90
    }
  ],
  "prestation": [
    {
      "id": 1,
      "nom": "Navette Aéroport",
      "prix": 30
    },
    {
      "id": 2,
      "nom": "Dîner Fruit de mer",
      "prix": 25
    },
    {
      "id": 3,
      "nom": "Petit Déjeuner Malgache",
      "prix": 20
    }
  ]
}
```

### `reservation.json` - Liste des réservations
- Statuts possibles : "en attente", "validé"
- Contient toutes les informations de la demande

### `users.json` - Liste des clients validés
- Créé automatiquement à la validation d'une réservation
- Chaque client a un ID unique
- Mot de passe par défaut : "mada123"
- Suivi des acomptes (arrhes) et réductions

### `planning.json`
- **État :** Non utilisé actuellement
- **Future utilisation :** Gestion des disponibilités, planing des réservations

---

## 🔧 Technologies utilisées

| Technologie | Usage | Version |
|---|---|---|
| **PHP** | Backend, traitement des données, fichiers JSON | Moderne |
| **JavaScript (jQuery)** | Interactivité, requêtes AJAX, événements | 3.7.1 |
| **HTML5** | Structure des pages | - |
| **CSS** | Styling (Bootstrap) | - |
| **Bootstrap** | Framework CSS responsive | 5.3.0 |
| **JSON** | Persistance de données | - |

---

## 🔌 Flux AJAX et interactions

### Flux 1 : Chargement des pages
```
Client clique sur menu → event.preventDefault() → 
.load() injecte page PHP → Contenu affiché dynamiquement
```

**Exemples :**
- Menu Accueil → `pages/accueil.php`
- Menu Réserver → `pages/reservation.php`
- Menu Admin → `admin.php` + `pages/admin_valid.php`

### Flux 2 : Soumission d'une réservation
```
1. Client remplit formulaire
2. Submit #form-reservation déclenché
3. $.ajax envoie $(this).serialize() POST
4. URL destination: pages/sauvegarde_reservation.php
5. Réponse "success" → Affichage message succès
6. Erreur → Affichage message erreur
```

### Flux 3 : Validation d'une réservation (Admin)
```
1. Admin clique bouton "Accepter"
2. $.ajax déclenché avec:
   - url: pages/traitement_validation.php
   - data: {id, mail, action}
3. Backend:
   - Change status "en attente" → "validé"
   - Crée nouveau client dans users.json si nouveau mail
4. Réponse "success" → Animation fadeOut + message succès
```

---

## 💾 Gestion des fichiers

### Sécurité - Verrous de fichiers (flock)
```php
// Verrouillage exclusif (LOCK_EX)
flock($flockOp, LOCK_EX);
fwrite($flockOp, json_encode($data));
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
