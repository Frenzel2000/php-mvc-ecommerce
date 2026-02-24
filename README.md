# Sport Shop – PHP MVC Online Shop (Uni-Projekt)

Ein kleiner Online-Shop, den wir im Uni-Projekt mit **PHP** und **JavaScript** umgesetzt haben – basierend auf dem **MVC Pattern**.  
Fokus: saubere Trennung von Controller/Views/Models, Routing via `.htaccess` (mod_rewrite) und Datenbankanbindung.

---

## Features (Auszug)

- Produktübersicht mit Kategorien/Filtermöglichkeiten
- Produktdetailseiten
- Rollen-/Rechtemodell (z. B. User, User-Manager, Product-Manager)
- Datenhaltung über MySQL (SQL-Dump enthalten)
- URL-Routing über Apache Rewrite (`.htaccess`)
- AJAX Filter -> z.B. AJAX-basierte Produktfilter (ohne Reload)

---

## Tech Stack

- **PHP** (MVC Architektur)
- **JavaScript** (UI-Interaktivität / AJAX)
- **MySQL / MariaDB**
- **Apache** + `mod_rewrite` (lokal über XAMPP)

---

## Voraussetzung: Apache URL Rewrite aktiv

- XAMPP (Apache + MySQL/MariaDB)
- Wichtig: Das Routing nutzt .htaccess, daher wird mod_rewrite benötigt (in XAMPP meist bereits aktiv)

---

## Projekt starten (lokal mit XAMPP)

### 1) Projekt in `htdocs` kopieren

- `XAMPP/htdocs/ws2526_dwp_frenzel_kocyatagi_brandmaier`

### 2) Datenbank einrichten

1. MySQL/MariaDB in XAMPP starten
2. phpMyAdmin öffnen (localhost im Browser aufrufen)
3. Datenbank erstellen:

```sql
CREATE DATABASE sport_shop;
```

4. SQL importieren

- Datei: database/sport_shop.sql

### 3) Projekt im Browser öffnen

http://localhost/ws2526_dwp_frenzel_kocyatagi_brandmaier/

---

## Demo-Accounts (nur lokal -> Email + Passwort)

- User: user@email.de / user
- User-Manager: user.manager@email.de / admin
- Product-Manager: product.manager@email.de / admin
