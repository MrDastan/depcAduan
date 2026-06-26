#!/bin/bash
# Script setup VPS untuk depcaduan.delimaholdings.com
# Jalankan sekali sahaja sebagai root: bash setup-vps.sh

set -e
DOMAIN="depcaduan.delimaholdings.com"
APP_DIR="/var/www/depcaduan"
DB_NAME="depcaduan"
DB_USER="depcaduan"

echo "════════════════════════════════════════"
echo " Setup VPS — $DOMAIN"
echo "════════════════════════════════════════"

# ── 1. Update sistem ──────────────────────────────────────────
apt update && apt upgrade -y

# ── 2. Pasang PHP 8.3 ────────────────────────────────────────
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-bcmath php8.3-gd php8.3-zip php8.3-intl php8.3-curl php8.3-exif

# ── 3. Pasang Nginx & MySQL ───────────────────────────────────
apt install -y nginx mysql-server unzip curl git

# ── 4. Pasang Composer ───────────────────────────────────────
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# ── 5. Setup MySQL ────────────────────────────────────────────
echo "Masukkan password untuk database user '$DB_USER':"
read -s DB_PASS

mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF
echo "✅ Database '$DB_NAME' berjaya dibuat"

# ── 6. Clone repo & setup projek ─────────────────────────────
mkdir -p $APP_DIR
echo ""
echo "Masukkan URL GitHub repo (contoh: https://github.com/username/depcaduan.git):"
read REPO_URL

git clone $REPO_URL $APP_DIR
cd $APP_DIR

composer install --no-dev --optimize-autoloader --no-interaction

cp .env.example .env

sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN|" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USER|" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|" .env
sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env

php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data $APP_DIR/storage $APP_DIR/bootstrap/cache
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

git config --global --add safe.directory $APP_DIR

# ── 7. Konfigurasi Nginx ──────────────────────────────────────
cat > /etc/nginx/sites-available/depcaduan <<NGINX
server {
    listen 80;
    server_name $DOMAIN;
    root $APP_DIR/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }
    client_max_body_size 20M;
}
NGINX

ln -sf /etc/nginx/sites-available/depcaduan /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

# ── 8. Jana SSH key untuk GitHub Actions ─────────────────────
ssh-keygen -t ed25519 -C "github-actions-depcaduan" -f /root/.ssh/github_actions -N ""
cat /root/.ssh/github_actions.pub >> /root/.ssh/authorized_keys
chmod 600 /root/.ssh/authorized_keys

# ── 9. Pasang SSL ─────────────────────────────────────────────
apt install -y certbot python3-certbot-nginx
certbot --nginx -d $DOMAIN --non-interactive --agree-tos -m admin@delimaholdings.com || echo "⚠️  SSL gagal — pastikan DNS sudah pointing ke server ini"

echo ""
echo "════════════════════════════════════════"
echo " ✅ Setup selesai!"
echo "════════════════════════════════════════"
echo ""
echo "Salin PRIVATE KEY ini ke GitHub Secrets (VPS_SSH_KEY):"
echo "────────────────────────────────────────"
cat /root/.ssh/github_actions
echo "────────────────────────────────────────"
echo ""
echo "GitHub Secrets yang perlu ditambah:"
echo "  VPS_HOST  = $(curl -s ifconfig.me 2>/dev/null || echo 'IP_SERVER_ANDA')"
echo "  VPS_USER  = root"
echo "  VPS_PORT  = 22"
echo "  VPS_SSH_KEY = (key di atas)"
echo ""
echo "Selepas tambah secrets, push kod ke GitHub untuk trigger deploy automatik."
echo "URL sistem: https://$DOMAIN/admin/login"
