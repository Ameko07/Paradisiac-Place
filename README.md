# Rapport - PARADISIAC-PLACE

## Présentation du projet
PARADISIAC-PLACE est une plateforme web dédiée à la réservation de séjours à Madagascar. L'application gère l'intégralité du flux entre les vacanciers et l'équipe administrative.

Le fonctionnement repose sur plusieurs étapes clés :
- Un client soumet sa demande de réservation en ligne.
- L'administration étudie, puis valide ou décline la sollicitation.
- Une fois confirmée, le client accède à un espace personnel pour consulter sa facture.
- Des activités groupées peuvent être planifiées et rattachées aux dossiers clients.

L'architecture privilégie la clarté et la simplicité, répondant ainsi à des objectifs pédagogiques précis.

## Technologies employées
Le projet s'appuie sur un socle technologique standard :
- Interface utilisateur : HTML, CSS, Bootstrap, JavaScript et jQuery.
- Logique serveur : PHP procédural.
- Stockage des données : Fichiers JSON.
- Dynamisme : Les vues et les données transitent via des appels AJAX pour éviter tout rechargement de page.

## Fonctionnalités du système

### Navigation client
Les visiteurs peuvent explorer les pages d'accueil et de découverte avant de passer à l'action. Le formulaire de réservation recueille les informations essentielles : identité, coordonnées, dates du séjour et nombre de participants. L'utilisateur choisit également son type d'hébergement et ses activités avant d'envoyer sa demande. Après validation par un administrateur, le client se connecte à son espace pour visualiser une facture détaillée incluant l'hébergement, les diverses prestations, les activités validées, ainsi que les déductions liées aux arrhes ou aux remises.

### Outils d'administration
L'accès sécurisé permet de piloter l'activité globale. L'administrateur visualise les réservations en attente et peut examiner chaque dossier en détail avant de trancher. Lorsqu'une demande est acceptée, le système génère automatiquement un compte client. L'équipe peut ensuite ajuster les informations financières, comme le montant des arrhes versées ou l'application d'une réduction. Un modèle de message contenant les accès est alors mis à disposition pour informer rapidement le client.

### Organisation des activités
La gestion des loisirs permet de consulter les demandes quotidiennes grâce à un filtre par date. L'administrateur crée des groupes de participants et désigne un animateur tout en veillant au respect des jauges minimales et maximales propres à chaque activité. Le système distingue clairement les groupes déjà confirmés des demandes encore en suspens.

## Organisation de l'interface

L'interface publique propose une navigation fluide entre les sections Accueil, Découvrir et Réserver. La zone centrale se met à jour dynamiquement, offrant une expérience responsive adaptée aux ordinateurs comme aux smartphones. Le formulaire de réservation utilise des structures claires pour guider la saisie, avec des contrôles sur les dates et la capacité d'accueil.

Côté gestion, l'administration dispose de tableaux de bord pour traiter les demandes sans changer de vue. Des indicateurs d'état informent en temps réel du succès ou de l'échec des traitements en cours. L'espace client, quant à lui, se concentre sur la lisibilité des frais engagés.

## Arborescence
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

## Synthèse technique
Ce projet remplit les exigences fonctionnelles attendues, du suivi client à la gestion financière. L'usage combiné de PHP, jQuery et JSON offre une solution légère et opérationnelle.

Quelques points techniques méritent une attention particulière :
- Gestion des accès concurrents : L'utilisation de `flock` en PHP assure l'intégrité des fichiers JSON lors des écritures simultanées.
- Sécurité : Les données sont traitées avec `htmlspecialchars` pour bloquer les risques d'injections XSS.
- Modernité du code : L'usage de l'opérateur de coalescence nulle facilite la lecture des scripts.

## État des lieux et perspectives

Le socle actuel permet d'enregistrer des réservations, de générer des comptes et de naviguer de manière asynchrone. Néanmoins, certains axes d'amélioration ont été identifiés pour de futures versions. La gestion du planning pourrait intégrer une vérification automatique des disponibilités pour éviter les doublons. La sécurité gagnerait à utiliser un hachage des mots de passe plus robuste et, à terme, une migration vers une base de données SQL permettrait de mieux structurer les volumes de données croissants.

---

## Résultats des tests finaux
Les derniers essais confirment la stabilité des rôles et des sessions. Un administrateur ne peut pas être déconnecté par mégarde en cliquant sur une vue client. La création de compte déclenche bien la génération d'un mot de passe unique. Les factures calculent correctement les soldes négatifs pour les remises. Enfin, les règles métiers concernant les quotas de participants aux activités sont opérationnelles et bloquent les groupes hors limites.