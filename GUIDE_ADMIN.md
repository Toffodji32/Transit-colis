# 📘 Guide de l'Interface Administrateur

## 🎯 Vue d'ensemble

L'interface admin permet de gérer complètement le système Transit Colis Nigeria-Bénin. Elle est accessible uniquement aux utilisateurs avec le rôle **ROLE_ADMIN**.

## 🚀 Accès à l'interface admin

### URL de base
```
http://localhost:8000/admin
```

### Comment obtenir l'accès admin

Par défaut, les nouveaux comptes créés via `/register` ont le rôle **ROLE_USER**. Pour créer un compte admin :

#### Option 1 : Via PHP/Terminal
```bash
php bin/console dbal:run-sql "UPDATE \"user\" SET roles = '[\"ROLE_ADMIN\", \"ROLE_USER\"]' WHERE email = 'votre@email.com';"
```

#### Option 2 : Via l'interface admin (si vous avez déjà un admin)
1. Connectez-vous avec votre compte admin
2. Allez sur `/admin/users`
3. Cliquez sur "Nouvel Utilisateur"
4. Cochez la case "ROLE_ADMIN" lors de la création

## 🏗️ Fonctionnalités disponibles

### 1️⃣ Dashboard Admin (`/admin`)
- **Statistiques en temps réel**
  - Total de colis dans le système
  - Nombre d'utilisateurs
  - Nombre d'entrepôts
  - Colis en transit
  
- **Répartition par statut**
  - Enregistré
  - En préparation
  - En transit
  - Arrivé à destination
  - Prêt pour retrait
  - Livré
  
- **Actions rapides**
  - Créer un nouveau colis
  - Ajouter un utilisateur
  - Créer un entrepôt
  - Voir tous les colis
  
- **Colis récents**
  - Liste des 10 derniers colis enregistrés

---

### 2️⃣ Gestion des Entrepôts (`/admin/warehouses`)

#### Ajouter un entrepôt (`/admin/warehouses/new`)
Informations à renseigner :
- **Nom de l'entrepôt** : ex. "Entrepôt Principal Lagos"
- **Pays** : Nigeria ou Bénin
- **Adresse complète** : adresse de l'entrepôt
- **Ville** : Lagos, Cotonou, etc.
- **Téléphone** : numéro de contact
- **Email** : email de contact
- **Horaires d'ouverture** : ex. "Lundi - Vendredi: 8h - 18h"
- **Capacité maximale** : ex. "10000" (en kg)

#### Modifier un entrepôt (`/admin/warehouses/{id}/edit`)
- Tous les champs sont éditables
- L'entrepôt enregistré actuellement : 
  - Nigeria : "Entrepôt Principal Lagos"
  - Bénin : "Entrepôt Principal Cotonou"

---

### 3️⃣ Gestion des Utilisateurs (`/admin/users`)

#### Créer un utilisateur (`/admin/users/new`)
Informations à renseigner :
- **Nom** : nom de famille
- **Prénom** : prénom
- **Email** : email unique
- **Téléphone** : numéro de téléphone unique
- **Mot de passe** : mot de passe de connexion
- **Rôles** : **Sélection des rôles**
  - ✅ `ROLE_USER` : Client standard (créé automatiquement)
  - ✅ `ROLE_ADMIN` : Administrateur système
  - ✅ `ROLE_ENTREPOT` : Agent d'entrepôt

**Important** : Les rôles peuvent être combinés (ex: un admin peut aussi avoir ROLE_ENTREPOT)

#### Modifier un utilisateur (`/admin/users/{id}/edit`)
- Modifier les informations personnelles
- Changer les rôles
- **Réinitialiser le mot de passe** : laisser vide pour conserver, remplir pour changer

---

### 4️⃣ Gestion des Colis (`/admin/colis`)

#### Enregistrer un nouveau colis (`/admin/colis/new`)
C'est l'interface que les **agents entrepôt** doivent utiliser pour enregistrer les colis qui arrivent physiquement.

**Workflow réel** :
1. Un fournisseur dépose un colis à l'entrepôt (ex: Lagos)
2. L'agent d'entrepôt enregistre le colis via cette interface
3. Un numéro de suivi est **généré automatiquement** (ex: TC-2024-A1B2C3D4)
4. Ce numéro est communiqué au client
5. Le client peut ensuite créer son compte et associer ce numéro à son compte

**Informations à renseigner** :

**Section Colis** :
- **Numéro de suivi** : Optionnel - généré auto si vide (format: TC-YYYY-XXXXXXXX)
- **Poids** : en kg (ex: 5.5)
- **Pays d'origine** : Nigeria ou Bénin
- **Pays de destination** : Nigeria ou Bénin
- **Description** : contenu du colis

**Section Expéditeur** :
- **Nom complet**
- **Téléphone**
- **Email** (optionnel)

**Section Destinataire** :
- **Nom complet**
- **Téléphone**
- **Email**
- **Adresse complète** (optionnel)

**Fonctionnalités automatiques** :
- ✅ Génération automatique du numéro de suivi
- ✅ Calcul automatique des frais selon le poids et la route
- ✅ Assignation automatique des entrepôts selon pays
- ✅ Création de l'historique initial "Enregistré"
- ✅ Message de succès avec le numéro de suivi généré

#### Liste des colis (`/admin/colis`)
- Tableau complet de tous les colis
- Filtrer par statut, date, etc.
- Actions : Voir détails, Modifier statut

---

## 🔐 Rôles et Permissions

| Rôle | Description | Accès |
|------|-------------|-------|
| **ROLE_USER** | Client standard | Dashboard client, création expéditions, suivi |
| **ROLE_ENTREPOT** | Agent d'entrepôt | Interface d'enregistrement des colis (à développer) |
| **ROLE_ADMIN** | Administrateur système | Accès complet à `/admin`, gestion de tout |

### Rôles combinables
- Un admin peut aussi être client (`ROLE_ADMIN` + `ROLE_USER`)
- Un agent entrepôt peut être client (`ROLE_ENTREPOT` + `ROLE_USER`)
- Un admin peut tout faire (`ROLE_ADMIN` + `ROLE_USER` + `ROLE_ENTREPOT`)

---

## 📱 URLs principales

| Page | URL | Accès |
|------|-----|-------|
| Dashboard Admin | `/admin` | ROLE_ADMIN |
| Gestion Entrepôts | `/admin/warehouses` | ROLE_ADMIN |
| Créer Entrepôt | `/admin/warehouses/new` | ROLE_ADMIN |
| Gestion Utilisateurs | `/admin/users` | ROLE_ADMIN |
| Créer Utilisateur | `/admin/users/new` | ROLE_ADMIN |
| Liste Colis | `/admin/colis` | ROLE_ADMIN |
| Enregistrer Colis | `/admin/colis/new` | ROLE_ADMIN |
| Éditer Colis | `/admin/colis/{id}/edit` | ROLE_ADMIN |

---

## 🎬 Scénarios d'utilisation

### Scénario 1 : Premier setup du système
1. Créer un compte admin via SQL
2. Se connecter sur `/admin`
3. Créer les entrepôts Nigeria et Bénin
4. Créer des comptes utilisateurs (clients et agents)

### Scénario 2 : Enregistrement d'un colis arrivant
1. Fournisseur dépose colis à Lagos
2. Agent se connecte → `/admin/colis/new`
3. Remplit toutes les informations
4. Clique "Enregistrer" → Numéro généré automatiquement
5. Agent communique ce numéro au client
6. Client crée son compte et associe le numéro

### Scénario 3 : Ajout d'un nouvel entrepôt
1. Admin se connecte → `/admin/warehouses`
2. Clique "Nouvel Entrepôt"
3. Remplit toutes les informations
4. Enregistre → Entrepôt disponible

### Scénario 4 : Création d'un agent d'entrepôt
1. Admin → `/admin/users/new`
2. Renseigne les informations
3. Coches **ROLE_ENTREPOT** (et optionnellement ROLE_USER)
4. Enregistre → Agent peut enregistrer des colis

---

## 🚨 Sécurité

- ✅ Protection `#[IsGranted('ROLE_ADMIN')]` sur toutes les routes admin
- ✅ Si un utilisateur non-admin essaie d'accéder → **403 Forbidden**
- ✅ Vérification de l'unicité email/téléphone
- ✅ Hashage bcrypt des mots de passe
- ✅ CSRF protection sur tous les formulaires

---

## 🐛 Dépannage

### "Access Denied" sur `/admin`
→ Vérifiez que votre compte a bien `ROLE_ADMIN` :
```sql
SELECT email, roles FROM "user" WHERE email = 'votre@email.com';
```

### Impossibilité de créer un utilisateur
→ Vérifiez l'unicité email/téléphone dans la base

### Numéro de suivi non généré
→ Laissez le champ vide, il sera généré automatiquement

---

## 📞 Prochaines étapes

- [ ] Interface dédiée pour ROLE_ENTREPOT (simplifiée vs admin)
- [ ] Notifications email automatiques
- [ ] Statistiques avancées et graphiques
- [ ] Export PDF des rapports
- [ ] Gestion des tarifs dynamiques

---

**Version** : 1.0  
**Date** : Novembre 2024  
**Système** : Transit Colis Nigeria-Bénin


