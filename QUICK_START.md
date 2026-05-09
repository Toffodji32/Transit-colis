# Guide Rapide - Transit Colis

## Workflow Complet ✅

### 1️⃣ Création du colis par l'entrepôt (ROLE_ENTREPOT)

**Le gestionnaire d'entrepôt crée un colis :**
- Aller sur `/entrepot/colis/new`
- Remplir : description, poids, photo, expéditeur, destinataire
- Le numéro de suivi est généré automatiquement
- Le colis est créé **sans user_id** (pas encore associé à un client)
- **Email envoyé au destinataire** avec le numéro de suivi

### 2️⃣ Client reçoit l'email 📧

L'email contient :
- Le numéro de suivi
- Les détails du colis (description, poids, origine, destination)
- **Un bouton "Créer mon expédition"** 

### 3️⃣ Client crée son expédition

**Deux options pour le client :**

#### Option A : Via l'email (recommandé)
1. Cliquer sur "Créer mon expédition" dans l'email
2. Arriver sur `/colis/new` avec le numéro pré-rempli
3. Cliquer sur "Créer l'expédition"
4. Le colis est automatiquement associé au client

#### Option B : Manuellement
1. Se connecter à `/colis/new`
2. Entrer le numéro reçu par email
3. Cliquer sur "Créer l'expédition"
4. Le colis est associé au client

#### Option C : Créer une expédition complète
1. Sur `/colis/new`, cliquer "créer une expédition manuellement"
2. Remplir tous les détails
3. Un nouveau numéro est généré

### 4️⃣ Le colis apparaît dans le dashboard client

Le client peut maintenant :
- Voir son colis sur `/dashboard`
- Suivre l'évolution sur `/colis/{id}`
- Voir l'historique des statuts

## Architecture

### Base de données
- **user_id** est maintenant **nullable** dans la table `colis`
- Un colis peut exister sans être associé à un client

### Routes

| Route | Description | Rôle |
|-------|-------------|------|
| `/entrepot/colis/new` | Créer colis (entrepôt) | ROLE_ENTREPOT |
| `/colis/new` | Créer expédition avec numéro | ROLE_USER |
| `/colis/new/manual` | Créer expédition complète | ROLE_USER |
| `/colis/{id}` | Détails du colis | ROLE_USER |
| `/dashboard` | Dashboard client | ROLE_USER |

### Services

- **UploadImageService** : Upload de photos de colis
- **EmailNotificationService** : Envoi d'emails (création et mises à jour)

### Templates

- `templates/colis/new.html.twig` : Formulaire de recherche par numéro
- `templates/colis/new_manual.html.twig` : Formulaire complet
- `templates/emails/colis_registered.html.twig` : Email avec bouton "Créer expédition"
- `templates/entrepot/colis/new.html.twig` : Création par entrepôt avec upload photo

## Tester le système

1. **Créer un gestionnaire d'entrepôt** :
   - Admin : `/admin/users/new`
   - Attribuer `ROLE_ENTREPOT`
   - Assigner à un entrepôt : `/admin/warehouses/{id}/edit`

2. **Créer un colis** :
   - Se connecter comme ROLE_ENTREPOT
   - Aller sur `/entrepot/colis/new`
   - Remplir le formulaire
   - Vérifier l'email dans Mailpit : `http://localhost:8025`

3. **Créer l'expédition** :
   - Se connecter comme ROLE_USER
   - Aller sur `/colis/new`
   - Entrer le numéro de suivi
   - Cliquer "Créer l'expédition"

4. **Vérifier** :
   - Le colis apparaît sur `/dashboard`
   - Les détails sont visibles

## Commandes utiles

```bash
# Créer un admin
php bin/console app:create-admin

# Voir les routes
php bin/console debug:router | grep colis

# Accéder à Mailpit
open http://localhost:8025

# Tester l'upload
# Les photos sont stockées dans public/uploads/colis/
```

## Problèmes résolus ✅

1. **user_id not null** : Rendu nullable pour permettre création sans client
2. **Workflow** : Colis créé par entrepôt → Email → Client crée expédition
3. **Email** : Bouton "Créer mon expédition" directement dans l'email
4. **Upload photos** : Service dédié pour gérer les fichiers
5. **Notifications** : Email automatique au destinataire

## Prochaines étapes possibles

- [ ] Système de paiement
- [ ] Notifications SMS
- [ ] Application mobile
- [ ] API REST pour intégrations
- [ ] Géolocalisation des colis
- [ ] Système de facturation
