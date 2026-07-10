# Flexbatir

Website resmi **Flexbatir** — dibangun dengan Laravel 13 & PHP 8.3.

## Tech Stack

- **Framework:** Laravel 13
- **PHP:** 8.3
- **Database:** MySQL
- **Frontend:** Vite
- **Server:** Nginx + Ubuntu (VPS)

## Requirement

- PHP >= 8.3
- Composer
- Node.js >= 20
- MySQL

## Instalasi Lokal

```bash
git clone https://github.com/pariiee/web-flexbatir.git
cd web-flexbatir

composer install
cp .env.example .env
php artisan key:generate

# Sesuaikan konfigurasi DB di .env lalu:
php artisan migrate

npm install
npm run dev
```

## Deployment

Deploy otomatis via GitHub Actions setiap push ke branch `main`.

Server: `159.223.76.117` — Domain: `https://flexbatir.web.id`

> Auto-deploy aktif via GitHub Actions. Last update: Jul 2026
