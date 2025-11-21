# Configuration de l'envoi de mails - Mailwo v3

## 📧 Configuration SMTP dans `.env`

Pour que l'envoi de mails fonctionne, vous devez configurer les paramètres SMTP dans votre fichier `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@gmail.com
MAIL_FROM_NAME="Mailwo"
```

## 🔧 Exemples de configuration par fournisseur

### Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=mot-de-passe-app-gmail
MAIL_ENCRYPTION=tls
```

**Important pour Gmail :**
- Activez l'authentification à 2 facteurs
- Créez un "Mot de passe d'application" depuis votre compte Google
- Utilisez ce mot de passe d'application dans `MAIL_PASSWORD`

### Mailtrap (Test)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre-username-mailtrap
MAIL_PASSWORD=votre-password-mailtrap
MAIL_ENCRYPTION=tls
```

### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=votre-domaine.mailgun.org
MAILGUN_SECRET=votre-cle-api
```

### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=votre-cle-api-sendgrid
MAIL_ENCRYPTION=tls
```

## 🚀 Utilisation

1. **Importer un CSV**
   - Accédez à `/import/create`
   - Téléchargez votre fichier CSV avec les colonnes (email, nom, prenom, etc.)
   - Le système extrait automatiquement les en-têtes

2. **Envoyer des mails personnalisés**
   - Accédez à `/sendMail/create`
   - Composez votre message
   - Utilisez les variables dynamiques : `{{prenom}}`, `{{nom}}`, `{{email}}`, etc.
   - Cliquez sur "Aperçu" pour voir le résultat
   - Envoyez !

## 📋 Format CSV requis

Votre fichier CSV doit contenir au minimum une colonne `email` :

```csv
email,prenom,nom,entreprise
contact@example.com,Jean,Dupont,ACME Corp
client@test.fr,Sophie,Martin,TechStart
info@societe.com,Pierre,Durand,WebAgency
```

## 🔐 Sécurité

- Ne commitez jamais votre fichier `.env`
- Utilisez des variables d'environnement pour les informations sensibles
- Pour la production, utilisez un service SMTP professionnel
- Limitez le nombre d'envois simultanés pour éviter d'être bloqué

## 🐛 Dépannage

### Les emails ne partent pas
1. Vérifiez votre configuration `.env`
2. Testez avec Mailtrap d'abord
3. Consultez les logs : `storage/logs/laravel.log`
4. Vérifiez que votre pare-feu autorise le port SMTP

### Emails dans les spams
1. Configurez SPF et DKIM sur votre domaine
2. Utilisez un service SMTP professionnel
3. Évitez les mots-clés spam
4. Ajoutez un lien de désinscription

### Rate limiting
Si vous envoyez beaucoup d'emails, ajoutez un délai :

```php
// Dans SendMailController.php, dans la boucle foreach
sleep(1); // Attendre 1 seconde entre chaque email
```

## 📚 Ressources

- [Documentation Laravel Mail](https://laravel.com/docs/mail)
- [Mailtrap - Test d'emails](https://mailtrap.io)
- [Google App Passwords](https://support.google.com/accounts/answer/185833)

