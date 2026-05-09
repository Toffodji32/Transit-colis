# CAHIER DES CHARGES FONCTIONNEL
## Système de Suivi de Colis Nigeria-Bénin

---

## 1. PRÉSENTATION DU PROJET

### 1.1 Contexte
Entreprise d'import-export opérant entre le Nigeria et le Bénin avec deux entrepôts (un dans chaque pays). Actuellement, la gestion des colis et la communication avec les clients se fait manuellement par appels et messages.

### 1.2 Objectif
Développer une application web permettant de digitaliser et automatiser le suivi des colis, la tarification, et la communication avec les clients.

### 1.3 Bénéfices attendus
- Réduction du temps de traitement des demandes
- Amélioration de la satisfaction client par la transparence
- Réduction des appels/messages manuels
- Traçabilité complète des opérations
- Automatisation des notifications

---

## 2. ACTEURS DU SYSTÈME

### 2.1 Client
- Expédie ou reçoit des colis
- Suit ses envois en temps réel
- Reçoit des notifications automatiques

### 2.2 Administrateur/Gestionnaire d'entrepôt
- Gère les colis entrants et sortants
- Met à jour les statuts des colis
- Gère les tarifs

### 2.3 Super Administrateur
- Gère les utilisateurs
- Accède aux statistiques globales
- Configure le système

---

## 3. FONCTIONNALITÉS PRINCIPALES

### 3.1 Gestion des Colis

#### 3.1.1 Enregistrement d'un colis
**Description :** Enregistrer un nouveau colis dans le système

**Données requises :**
- Numéro de colis (généré automatiquement ou manuellement)
- Pays d'origine (Nigeria/Bénin)
- Pays de destination (Bénin/Nigeria)
- Poids du colis (en kg)
- Description du contenu
- Informations expéditeur :
  - Nom complet
  - Numéro de téléphone
  - Email (optionnel)
- Informations destinataire :
  - Nom complet
  - Numéro de téléphone
  - Email
  - Adresse de livraison
- Date d'enregistrement
- Statut initial : "Enregistré"

**Processus :**
1. Client commande au Nigeria
2. Vendeur livre à l'entrepôt Nigeria avec informations du destinataire
3. Gestionnaire enregistre le colis dans le système
4. Numéro de suivi généré et envoyé au client

#### 3.1.2 Mise à jour du statut
**Statuts possibles :**
1. **Enregistré** - Colis reçu à l'entrepôt d'origine
2. **En préparation** - Colis en cours de conditionnement
3. **En transit** - Colis en route vers le pays de destination
4. **Arrivé à destination** - Colis arrivé à l'entrepôt de destination
5. **Prêt pour retrait** - Client peut venir récupérer
6. **Livré** - Colis remis au destinataire
7. **Problème** - Incident signalé (optionnel)

**Déclenchement automatique :** Email + SMS à chaque changement de statut

### 3.2 Système de Tarification

#### 3.2.1 Calcul automatique des frais
**Formule :** Prix = Poids (kg) × Tarif au kg

**Paramètres configurables :**
- Tarif Nigeria → Bénin (par kg)
- Tarif Bénin → Nigeria (par kg)
- Tarifs spéciaux par tranche de poids (optionnel)
  - 0-5 kg : tarif standard
  - 5-20 kg : tarif réduit
  - 20+ kg : tarif négocié

**Fonctionnalités :**
- Simulateur de prix (avant envoi)
- Historique des tarifs
- Facturation automatique

#### 3.2.2 Gestion des paiements
- Enregistrement du statut de paiement (Payé/Non payé)
- Modes de paiement acceptés
- Génération de reçus/factures

### 3.3 Suivi en Temps Réel

#### 3.3.1 Interface de suivi client
**Accessible par :**
- Numéro de colis
- Compte utilisateur (pour voir tous ses colis)

**Informations affichées :**
- Numéro de colis
- Statut actuel avec badge coloré
- Historique complet des mouvements
- Localisation actuelle (entrepôt)
- Date estimée d'arrivée
- Montant des frais
- Statut du paiement

#### 3.3.2 Tableau de bord client
- Liste de tous les colis du client
- Filtres : Statut, Date, Pays
- Statistiques personnelles
- Historique complet

### 3.4 Système de Notifications

#### 3.4.1 Notifications par Email
**Déclencheurs :**
- Enregistrement du colis
- Chaque changement de statut
- Arrivée à destination
- Alerte si colis non retiré après 7 jours

**Contenu type :**
```
Sujet : Mise à jour de votre colis #[NUMERO]

Bonjour [NOM CLIENT],

Votre colis #[NUMERO] est maintenant : [STATUT]

Détails :
- Origine : [PAYS]
- Destination : [PAYS]
- Localisation actuelle : [ENTREPOT]
- Date : [DATE]

[Si statut = Prêt pour retrait]
Vous pouvez venir récupérer votre colis à l'adresse :
[ADRESSE ENTREPOT]
Horaires : [HORAIRES]

Suivez votre colis : [LIEN]
```

#### 3.4.2 Notifications SMS (optionnel)
Messages courts pour les changements critiques :
- Enregistrement
- En transit
- Prêt pour retrait

### 3.5 Gestion des Utilisateurs

#### 3.5.1 Inscription/Connexion Client
- Email + Mot de passe
- Numéro de téléphone
- Profil avec informations personnelles
- Historique de tous les envois

#### 3.5.2 Panneau d'administration
**Fonctionnalités :**
- Liste de tous les colis avec filtres avancés
- Mise à jour rapide des statuts
- Gestion des clients
- Statistiques et rapports
- Configuration des tarifs

---

## 4. FONCTIONNALITÉS SECONDAIRES

### 4.1 Système de Recherche
- Recherche par numéro de colis
- Recherche par nom de client
- Recherche par date
- Recherche par statut

### 4.2 Statistiques et Rapports
**Pour les administrateurs :**
- Nombre de colis par mois/semaine
- Revenus générés
- Délais moyens de livraison
- Colis en attente
- Performance par route (Nigeria→Bénin / Bénin→Nigeria)

### 4.3 Module de Communication
- Messagerie interne client-administrateur
- FAQ automatisée
- Support client intégré

### 4.4 Gestion des Entrepôts
- Capacité de stockage
- Localisation précise
- Horaires d'ouverture
- Informations de contact

---

## 5. SPÉCIFICATIONS TECHNIQUES

### 5.1 Architecture Recommandée

**Frontend :**
- Application web responsive (React.js, Vue.js ou Angular)
- Application mobile (React Native ou Flutter) - Phase 2

**Backend :**
- API REST (Node.js/Express, Python/Django, ou PHP/Laravel)
- Base de données relationnelle (PostgreSQL ou MySQL)

**Services tiers :**
- Service d'envoi d'emails (SendGrid, Mailgun, AWS SES)
- Service SMS (Twilio, Africa's Talking)
- Hébergement cloud (AWS, DigitalOcean, Heroku)

### 5.2 Base de Données - Tables Principales

#### Table `users`
- id, nom, prenom, email, telephone, password, role, date_creation

#### Table `colis`
- id, numero_colis, poids, description, pays_origine, pays_destination, images
- expediteur_nom, expediteur_tel, expediteur_email
- destinataire_nom, destinataire_tel, destinataire_email
- statut_actuel, date_enregistrement, date_livraison
- montant_frais, statut_paiement, user_id

#### Table `historique_statuts`
- id, colis_id, statut, date_changement, commentaire, user_id_modificateur

#### Table `tarifs`
- id, route (Nigeria→Bénin / Bénin→Nigeria), poids_min, poids_max, prix_par_kg, date_debut, date_fin

#### Table `notifications`
- id, colis_id, type (email/sms), statut_envoi, date_envoi, destinataire

### 5.3 API Endpoints Principaux

**Authentification :**
- POST `/api/auth/register` - Inscription
- POST `/api/auth/login` - Connexion
- POST `/api/auth/logout` - Déconnexion

**Colis :**
- GET `/api/colis` - Liste des colis (avec filtres)
- POST `/api/colis` - Créer un colis
- GET `/api/colis/:numero` - Détails d'un colis
- PUT `/api/colis/:id/statut` - Mettre à jour le statut
- GET `/api/colis/suivi/:numero` - Suivi public (sans auth)

**Tarifs :**
- GET `/api/tarifs` - Obtenir les tarifs actuels
- POST `/api/tarifs/calculer` - Calculer le prix pour un poids donné
- PUT `/api/tarifs/:id` - Modifier un tarif (admin)

**Utilisateurs :**
- GET `/api/users/me` - Profil utilisateur
- GET `/api/users/me/colis` - Tous les colis de l'utilisateur
- PUT `/api/users/me` - Modifier le profil

**Statistiques (Admin) :**
- GET `/api/stats/dashboard` - Statistiques générales
- GET `/api/stats/revenus` - Rapports de revenus
- GET `/api/stats/performance` - Indicateurs de performance

### 5.4 Sécurité
- Authentification JWT (JSON Web Tokens)
- Hashage des mots de passe (bcrypt)
- HTTPS obligatoire
- Protection CORS
- Validation des données entrantes
- Rate limiting sur les API

---

## 6. INTERFACES UTILISATEUR

### 6.1 Interface Client

#### Page d'accueil
- Barre de recherche pour suivi rapide (par numéro)
- Simulateur de tarifs
- Informations sur les services
- Bouton d'inscription/connexion

#### Tableau de bord client
- Vue d'ensemble des colis actifs
- Historique complet
- Notifications récentes
- Accès rapide aux fonctionnalités

#### Page de suivi
- Timeline visuelle du parcours du colis
- Informations détaillées
- Historique des statuts
- Coordonnées de contact

### 6.2 Interface Administrateur

#### Tableau de bord admin
- Statistiques en temps réel
- Colis à traiter en priorité
- Graphiques de performance
- Alertes importantes

#### Gestion des colis
- Liste avec filtres avancés
- Actions rapides (changement de statut)
- Vue détaillée avec historique complet
- Génération de documents (factures, reçus)

#### Gestion des tarifs
- Configuration des prix par route
- Historique des modifications
- Simulateur de revenus

---

## 7. WORKFLOW COMPLET

### Scénario : Envoi Nigeria → Bénin

1. **Commande au Nigeria**
   - Client commande chez un vendeur
   - Vendeur livre à l'entrepôt avec infos destinataire

2. **Enregistrement (Entrepôt Nigeria)**
   - Gestionnaire pèse le colis
   - Enregistre dans le système
   - Système génère numéro de suivi
   - Email automatique envoyé au destinataire

3. **Préparation**
   - Colis conditionné
   - Statut mis à jour → Email envoyé

4. **Transit**
   - Colis part vers le Bénin
   - Statut mis à jour → Email envoyé

5. **Arrivée Bénin**
   - Colis arrive à l'entrepôt Bénin
   - Statut mis à jour → Email envoyé

6. **Prêt pour retrait**
   - Colis vérifié et disponible
   - Email + SMS envoyé avec adresse de retrait

7. **Retrait**
   - Client se présente avec numéro de colis
   - Paiement si non effectué
   - Remise du colis
   - Statut "Livré" → Email de confirmation

---
