# Workflow des Statuts des Colis ✅

## Cycle de vie complet d'un colis

### 1️⃣ Création par l'entrepôt (ROLE_ENTREPOT)
**Statut** : `CRÉÉ`
- Le gestionnaire entrepôt enregistre le colis
- Numéro de suivi généré
- Email envoyé au destinataire

### 2️⃣ Client crée l'expédition
**Statut** : `EXPÉDITION_EN_COURS`
- Client entre le numéro reçu par email
- Le colis est associé à son compte
- L'expédition démarre

### 3️⃣ Gestion par les entrepôts

#### A. Entrepôt d'origine
**Statuts disponibles** :
- `EN_PRÉPARATION` : Le colis est préparé pour l'expédition
- `EN_TRANSIT` : Le colis quitte l'entrepôt d'origine
  - ✅ **Date départ enregistrée automatiquement**

#### B. Entre-deux
- Le colis voyage entre les deux pays
- Le client peut suivre le statut en temps réel

#### C. Entrepôt de destination
**Statuts disponibles** :
- `ARRIVÉ_À_DESTINATION` : Le colis arrive à l'entrepôt de destination
  - ✅ **Date d'arrivée enregistrée automatiquement**
- `PRÊT_POUR_RETRAIT` : Le client peut venir récupérer son colis
- `LIVRÉ` : Le colis a été remis au client

### 4️⃣ Cas particuliers
**Statut** : `DOMMAGE`
- Si un dommage est détecté pendant le transport
- Le gestionnaire peut ajouter un commentaire

**Statut** : `PROBLÈME`
- Pour tout autre problème détecté

## Fonctionnalités

### Dates automatiques
- **Date départ** : Enregistrée quand statut → `EN_TRANSIT`
- **Date arrivée** : Enregistrée quand statut → `ARRIVÉ_À_DESTINATION`

### Notifications
- Email envoyé à chaque changement de statut
- Le client est notifié en temps réel

### Interface entrepôt
- Modal de changement de statut
- Commentaire optionnel
- Validation des permissions (colis appartenant à l'entrepôt)
- Spinner de chargement

### Suivi client
- Historique complet des statuts
- Dates affichées dans les détails
- Photos du colis visibles

## Parcours visualisé

```
CRÉÉ → EXPÉDITION_EN_COURS → EN_PRÉPARATION → EN_TRANSIT → ARRIVÉ_À_DESTINATION → PRÊT_POUR_RETRAIT → LIVRÉ
                                                            ↓
                                                        DOMMAGE
                                                            ↓
                                                        PROBLÈME
```

## Fichiers modifiés

### Entity
- `src/Entity/Colis.php` : Nouveaux statuts + dateDepart + méthodes

### Controllers
- `src/Controller/EntrepotController.php` : CRÉÉ au lieu de ENREGISTRÉ + updateColisStatus()
- `src/Controller/ColisController.php` : EXPÉDITION_EN_COURS quand client crée

### Services
- `src/Service/EmailNotificationService.php` : Libellés mis à jour

### Templates
- `templates/entrepot/colis/list.html.twig` : Modal changement statut
- `templates/colis/show.html.twig` : Dates départ/arrivée affichées
- Tous les statuts affichés avec libellés corrects

## Migration
- `Version20251031201221.php` : Ajout colonne `date_depart`

Tout fonctionnel ! 🎉
