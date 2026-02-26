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

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

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

# 📡 Konfigurasi Laravel Reverb

Laravel Reverb adalah WebSocket server bawaan Laravel untuk fitur broadcasting real-time.

## 1️⃣ Install Reverb

```bash
php artisan install:broadcasting
```

> Perintah ini secara otomatis menginstall package Reverb, membuat file konfigurasi `config/reverb.php`, dan menambahkan variabel environment yang diperlukan ke file `.env`.

Jika ingin install secara manual via Composer:

```bash
composer require laravel/reverb
php artisan reverb:install
```

## 2️⃣ Install & Build Frontend

Setelah Reverb terinstall, build ulang asset frontend agar konfigurasi Vite terbaru diterapkan:

```bash
npm install
npm run build
```

## 3️⃣ Menjalankan Reverb Server

```bash
php artisan reverb:start
```

Dengan opsi tambahan (host, port, debug):

```bash
php artisan reverb:start --host=0.0.0.0 --port=8080 --debug
```

> Server Reverb berjalan secara default di `http://localhost:8080`.

---

# ▶️ Menjalankan Aplikasi

Jalankan semua proses berikut **secara bersamaan** di terminal terpisah:

### Terminal 1 — Laravel Server

```bash
php artisan serve --port=8000
```

### Terminal 2 — Queue Worker

```bash
php artisan queue:work
```

### Terminal 3 — Reverb WebSocket Server

```bash
php artisan reverb:start
```

---

# 🌐 Akses Aplikasi

Buka browser dan akses:

```
http://localhost:8000
```

---

# 📄 Lisensi

Project ini menggunakan framework Laravel.
