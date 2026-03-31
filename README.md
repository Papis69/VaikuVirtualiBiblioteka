# Vaikų Virtuali Biblioteka

Bakalauro darbas: „Virtualios vaikų bibliotekos projektavimas ir programavimas"

## Technologijos

- **PHP 8.2+**
- **Symfony 7.1** (MVC Framework)
- **Twig** (šablonų variklis)
- **Doctrine ORM** (duomenų bazės abstrakcija)
- **MySQL 8.0** (duomenų bazė)
- **HTML / CSS / JavaScript**

## Sistemos funkcionalumas

1. **Vartotojų sistema** – registracija, prisijungimas, atsijungimas, rolės (USER/ADMIN)
2. **Knygų katalogas** – paieška, filtravimas pagal amžių/kategoriją, knygos peržiūra
3. **Misijų sistema** – misijų sąrašas, atlikimas, taškų suteikimas
4. **Taškai ir ženkliukai** – automatinis ženkliukų suteikimas pagal taškų kriterijus
5. **Prizų sistema** – prizų „pirkimas" taškais
6. **Administravimas** – knygų, misijų, prizų CRUD, vartotojų sąrašas

## Paleidimo instrukcija

### 1. Reikalavimai

- PHP 8.2 arba naujesnė versija
- Composer (https://getcomposer.org/)
- MySQL 8.0 (per XAMPP, WAMP arba atskirai)
- Symfony CLI (nebūtina, bet rekomenduojama: https://symfony.com/download)

### 2. Projekto instaliavimas

```bash
# 1. Atsisiųskite projektą ir eikite į katalogą
cd VaikuVirtualiBiblioteka

# 2. Įdiekite priklausomybes
composer install

# 3. Sukonfigūruokite duomenų bazę .env faile
# Pagal nutylėjimą: DATABASE_URL="mysql://root:@127.0.0.1:3306/vaiku_biblioteka?serverVersion=8.0&charset=utf8mb4"
# Jei naudojate slaptažodį, pakeiskite: mysql://root:JUSU_SLAPTAZODIS@127.0.0.1:3306/vaiku_biblioteka

# 4. Sukurkite duomenų bazę
php bin/console doctrine:database:create

# 5. Sukurkite lenteles (migracijos)
php bin/console doctrine:schema:create

# 6. Užpildykite testinius duomenis
php bin/console doctrine:fixtures:load --no-interaction

# 7. Paleiskite serverį
symfony server:start
# arba:
php -S 127.0.0.1:8000 -t public
```

### 3. Prisijungimo duomenys (po fixtures)

| Rolė  | El. paštas              | Slaptažodis |
|-------|-------------------------|-------------|
| ADMIN | admin@biblioteka.lt     | admin123    |
| USER  | jonas@biblioteka.lt     | user123     |

### 4. Projekto struktūra

```
src/
├── Controller/           # Kontroleriai (MVC)
│   ├── Admin/            # Administravimo kontroleriai
│   ├── HomeController    # Pradinis puslapis
│   ├── SecurityController # Prisijungimas/atsijungimas
│   ├── RegistrationController
│   ├── BookController    # Knygų katalogas
│   ├── MissionController # Misijos
│   ├── RewardController  # Prizai
│   └── ProfileController # Profilis
├── Entity/               # Doctrine ORM modeliai
├── Repository/           # Duomenų bazės sluoksnis
├── Form/                 # Symfony formos
├── Service/              # Verslo logika (BadgeService)
└── DataFixtures/         # Testiniai duomenys

templates/                # Twig šablonai
├── base.html.twig        # Pagrindinis layout
├── home/                 # Pradinis puslapis
├── security/             # Prisijungimas
├── registration/         # Registracija
├── book/                 # Knygos
├── mission/              # Misijos
├── reward/               # Prizai
├── profile/              # Profilis
└── admin/                # Admin skydelis

config/                   # Symfony konfigūracija
public/
├── css/style.css         # Pagrindinis CSS
└── js/app.js             # JavaScript
```

## Duomenų bazės lentelės

- `user` – vartotojai
- `category` – knygų kategorijos
- `book` – knygos
- `mission` – misijos
- `user_mission` – vartotojų misijų progresas
- `badge` – ženkliukai
- `user_badge` – vartotojų gauti ženkliukai
- `reward` – prizai
- `user_reward` – vartotojų įsigyti prizai

## Autorius

Bakalauro darbas, 2026 m.
