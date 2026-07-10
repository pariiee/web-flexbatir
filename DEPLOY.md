# Panduan Deploy - flexbatir.web.id

**VPS:** 159.223.76.117  
**Domain:** flexbatir.web.id  
**Stack:** Laravel 13, PHP 8.3, MySQL, Nginx

---

## Struktur File yang Dibuat

```
web-flexbatir/
├── deploy.sh                  # Script deploy otomatis
├── nginx.conf                 # Config Nginx untuk VPS
├── .env.production.example    # Template .env untuk production
└── .github/
    └── workflows/
        └── deploy.yml         # GitHub Actions auto-deploy
```

---

## BAGIAN 1 — Setup Awal VPS (Lakukan Sekali)

### 1.1 Login ke VPS

```bash
ssh root@159.223.76.117
```

### 1.2 Update sistem & install dependencies

```bash
apt update && apt upgrade -y

# Install Nginx
apt install -y nginx

# Install PHP 8.3
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Install MySQL
apt install -y mysql-server
```

### 1.3 Setup MySQL

```bash
mysql_secure_installation
# ikuti promptnya, set root password yang kuat

mysql -u root -p
```

Di dalam MySQL shell:

```sql
CREATE DATABASE flexbatir_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'flexbatir_user'@'localhost' IDENTIFIED BY 'PASSWORD_KUAT_DISINI';
GRANT ALL PRIVILEGES ON flexbatir_db.* TO 'flexbatir_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 1.4 Buat user deploy (jangan pakai root)

```bash
adduser deployer
usermod -aG sudo deployer
usermod -aG www-data deployer
```

### 1.5 Setup SSH key untuk GitHub Actions

Di VPS, jalankan sebagai deployer:

```bash
su - deployer
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/github_actions -N ""

# Tambahkan public key ke authorized_keys
cat ~/.ssh/github_actions.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys

# Tampilkan private key — copy ini untuk disimpan di GitHub Secrets
cat ~/.ssh/github_actions
```

**Simpan private key tersebut**, nanti dipakai di GitHub Secrets.

---

## BAGIAN 2 — Clone & Setup Project

### 2.1 Clone repository

```bash
# Masih di VPS sebagai deployer
mkdir -p /var/www
cd /var/www
git clone https://github.com/pariiee/web-flexbatir.git flexbatir
cd /var/www/flexbatir
```

### 2.2 Setup file .env

```bash
cp .env.production.example .env
nano .env
```

Edit bagian ini:
```
APP_KEY=          # akan di-generate di langkah berikutnya
DB_PASSWORD=      # password MySQL yang kamu buat tadi
```

### 2.3 Generate APP_KEY dan install dependencies

```bash
composer install --no-dev --optimize-autoloader
php8.3 artisan key:generate
npm ci --omit=dev
npm run build
```

### 2.4 Setup permissions

```bash
chown -R deployer:www-data /var/www/flexbatir
chmod -R 755 /var/www/flexbatir
chmod -R 775 /var/www/flexbatir/storage
chmod -R 775 /var/www/flexbatir/bootstrap/cache
```

### 2.5 Jalankan migration

```bash
php8.3 artisan migrate --force
```

### 2.6 Buat symlink storage

```bash
php8.3 artisan storage:link
```

---

## BAGIAN 3 — Setup Nginx

### 3.1 Copy config Nginx

```bash
cp /var/www/flexbatir/nginx.conf /etc/nginx/sites-available/flexbatir
ln -s /etc/nginx/sites-available/flexbatir /etc/nginx/sites-enabled/flexbatir

# Hapus default site
rm -f /etc/nginx/sites-enabled/default

# Test config
nginx -t

# Reload Nginx
systemctl reload nginx
```

### 3.2 Pastikan PHP-FPM jalan

```bash
systemctl start php8.3-fpm
systemctl enable php8.3-fpm
systemctl status php8.3-fpm
```

---

## BAGIAN 4 — Setup SSL (HTTPS) dengan Certbot

```bash
apt install -y certbot python3-certbot-nginx

certbot --nginx -d flexbatir.web.id -d www.flexbatir.web.id
```

Ikuti promptnya, masukkan email, pilih redirect HTTP ke HTTPS.

Certbot akan otomatis update nginx.conf kamu dengan SSL.

---

## BAGIAN 5 — Setup Auto-Deploy via GitHub Actions

### 5.1 Set GitHub Secrets

Buka repo GitHub → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

Tambahkan 3 secrets ini:

| Secret Name   | Value                                      |
|---------------|--------------------------------------------|
| `VPS_HOST`    | `159.223.76.117`                           |
| `VPS_USER`    | `deployer`                                 |
| `VPS_SSH_KEY` | isi dengan private key dari langkah 1.5    |

### 5.2 Pastikan deploy.sh bisa dieksekusi

Di VPS:

```bash
chmod +x /var/www/flexbatir/deploy.sh
```

### 5.3 Test auto-deploy

Sekarang setiap kamu **push ke branch `main`**, GitHub Actions akan otomatis:
1. SSH ke VPS
2. Jalankan `deploy.sh`
3. Pull latest code
4. `composer install`
5. Clear semua cache
6. `php artisan migrate --force`
7. Cache ulang config, route, view
8. Build frontend assets
9. Fix permissions

### 5.4 Cek status deploy

Buka tab **Actions** di GitHub repo kamu untuk lihat log deploy.

---

## BAGIAN 6 — Setup DNS Domain

Di panel domain `flexbatir.web.id`, tambahkan DNS record:

| Type | Name | Value            | TTL  |
|------|------|------------------|------|
| A    | @    | 159.223.76.117   | 3600 |
| A    | www  | 159.223.76.117   | 3600 |

Tunggu propagasi DNS sekitar 5–30 menit.

---

## BAGIAN 7 — Deploy Manual (kalau perlu)

Kalau mau deploy manual tanpa push ke GitHub:

```bash
ssh deployer@159.223.76.117
cd /var/www/flexbatir
bash deploy.sh
```

---

## Troubleshooting

**500 Error setelah deploy:**
```bash
tail -f /var/log/nginx/flexbatir_error.log
tail -f /var/www/flexbatir/storage/logs/laravel.log
```

**Permission denied:**
```bash
chown -R deployer:www-data /var/www/flexbatir/storage
chmod -R 775 /var/www/flexbatir/storage
```

**PHP-FPM tidak jalan:**
```bash
systemctl restart php8.3-fpm
```

**Nginx error:**
```bash
nginx -t
systemctl restart nginx
```

**Migration gagal:**
```bash
php8.3 artisan migrate:status
php8.3 artisan migrate --force -v
```
