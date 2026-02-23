<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

# Digimon QC

Aplikasi berbasis Laravel untuk sistem **Digimon QC** dengan dukungan Queue, Database Session, Cache Database, dan Reverb (WebSocket).

---

# ✅ Persyaratan Sistem

Pastikan sistem memenuhi kebutuhan berikut:

- PHP >= 8.2
- Composer (versi terbaru)
- Node.js & NPM
- Git
- MySQL
- Redis (opsional – jika menggunakan queue/broadcast)
- XAMPP / Laragon / Web Server lokal
- Text Editor (VS Code disarankan)

---

# 🚀 Instalasi Lokal

## 1️⃣ Clone Repository

```bash
cd c:/xampp/htdocs      # XAMPP
cd c:/laragon/www       # Laragon

git clone https://github.com/reishantridyarafly/digimon_v2.git
cd digimon_v2
```

---

## 2️⃣ Install Dependencies

### Install dependency PHP

```bash
composer install
```

### Install dependency frontend

```bash
npm install
npm run build
```

---

## 3️⃣ Konfigurasi Environment

Copy file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

> ⚠️ Jangan pernah commit file `.env` ke repository.

---

# ⚙️ Konfigurasi File `.env`

Sesuaikan konfigurasi berikut:

```env
APP_NAME="Digimon QC"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=digimon
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

PRODUCTION_URL=http://127.0.0.1:8001/
```

---

# 🗄 Setup Database

1. Buat database baru:

```
digimon
```

2. Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

3. Buat symbolic link storage:

```bash
php artisan storage:link
```

4. Bersihkan cache:

```bash
php artisan optimize:clear
```

---

# ▶️ Menjalankan Aplikasi

### Jalankan Laravel Server

```bash
php artisan serve
```

### Jalankan Queue Worker

```bash
php artisan queue:work
```

### Jalankan Reverb WebSocket Server

```bash
php artisan reverb:start
```

---

# 🌐 Akses Aplikasi

Buka browser dan akses:

```
http://localhost:8000
```

# 📄 Lisensi

Project ini menggunakan framework Laravel.
