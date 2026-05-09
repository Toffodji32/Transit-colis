# Configuration Email Gmail SMTP ✅

L'application est configurée pour envoyer des emails via Gmail SMTP.

## Configuration

### Fichier `.env`
```
MAILER_DSN=smtp://berekiaadamou14@gmail.com:bbxnxzabjcuqijbt@smtp.gmail.com:587
```

### Service Email
**Fichier** : `src/Service/EmailNotificationService.php`

**Adresse expéditrice** : `berekiaadamou14@gmail.com`

## Emails envoyés

### 1. Notification de création de colis
- **Quand** : Quand un gestionnaire d'entrepôt enregistre un nouveau colis
- **Destinataire** : Email du destinataire du colis
- **Sujet** : 🎁 Votre colis a été enregistré - [NUMÉRO]
- **Template** : `templates/emails/colis_registered.html.twig`
- **Contenu** :
  - Numéro de suivi
  - Détails du colis (poids, description, origine, destination)
  - Bouton "Créer mon expédition" (pour créer l'expédition)
  - Lien de suivi direct

### 2. Notification de changement de statut
- **Quand** : Quand le statut d'un colis change
- **Destinataire** : Email du destinataire du colis
- **Sujet** : 📦 Mise à jour : [STATUT] - [NUMÉRO]
- **Template** : `templates/emails/statut_update.html.twig`

## Test

✅ Configuration testée et fonctionnelle

Pour tester l'envoi d'email :
1. Créer un colis via l'interface entrepôt (`/entrepot/colis/new`)
2. Vérifier l'email reçu par le destinataire
3. Le lien "Créer mon expédition" redirige vers `/colis/new`

## Sécurité

⚠️ **IMPORTANT** : En production, utilisez des variables d'environnement sécurisées pour stocker les credentials SMTP.

Pour changer l'adresse email expéditrice :
1. Modifier `src/Service/EmailNotificationService.php`
2. Remplacer `berekiaadamou14@gmail.com` par votre adresse

Pour changer les credentials SMTP :
1. Modifier `.env` ou créer `.env.local`
2. Mettre à jour `MAILER_DSN`
3. Exécuter `php bin/console cache:clear`
