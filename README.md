# LogiCar

LogiCar est une application interne de gestion des plannings de transport, des ramassages et du parc automobile de Sofitrans.

## Technologies

- Laravel 12 et PHP 8.2+
- Vue 3, Vue Router, Pinia, date-fns et Bootstrap 5
- Vite pour les ressources front-end
- MySQL/MariaDB en production; SQLite est configuré pour les tests

## Démarrage

Après avoir configuré les variables d'environnement dans `.env` :

```sh
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
```

Dans un autre terminal, démarrer l'application :

```sh
php artisan serve
```

Pour construire les ressources de production et lancer les tests :

```sh
npm run build
composer test
```

## Organisation

- `routes/web.php` est le registre des routes HTTP : sessions, interfaces et ressources REST (`/users`, `/plannings`, `/cars`, `/movements`, `/anomalies`, `/trips`).
- `app/Http/Controllers` contient les contrôleurs des ressources métier; `AuthController` ne gère que la session, tandis que `UserController` gère la ressource utilisateurs.
- `app/Models` et `database/migrations` définissent les données métier.
- `resources/js/component/SNR` contient l'espace de gestion SNR (plannings, véhicules, utilisateurs).
- `resources/js/component/OS` contient l'espace des autres services.
- `resources/views` contient les pages d'entrée Laravel; les pages métier sont rendues par Vue Router.

## Parcours et accès

- `/login` permet l'authentification; `/` redirige vers cette page.
- `/snr` et `/other_service` sont les points d'entrée des deux espaces Vue et leurs sous-routes sont servies par Laravel.
- Les routes SNR (utilisateurs, ramassages, flotte et anomalies) sont réservées au service `SNR`; les autres comptes ne peuvent consulter et modifier que les plannings de leur propre service.
- Les routes métier et les données de flotte nécessitent une session authentifiée.
- Les ressources utilisent les verbes HTTP (`GET`, `POST`, `PATCH`, `DELETE`) et les identifiants dans leurs URI; les vues Vue (`/snr`, `/other_service`) restent des points d'entrée de navigation.

Les comptes utilisateurs sont créés depuis l'espace SNR et reçoivent initialement le mot de passe `0000`; il doit être remplacé depuis les paramètres du compte.
