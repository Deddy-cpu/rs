📘 README

Panduan lengkap untuk menjalankan project ini dari awal, mulai dari clone repositori,
install dependency, hingga menjalankan aplikasi.

---

## 📌 1. Persyaratan Sistem

Pastikan sudah menginstall:

### **Backend**
- PHP 8.1+  
- Composer  
- MySQL / MariaDB  
- Ekstensi PHP:  
  - mbstring  
  - xml  
  - ctype  
  - json  
  - tokenizer  
  - bcmath  
  - pdo_mysql  
  - fileinfo  

### **Frontend**
- Node.js 16+ (disarankan versi LTS)  
- NPM (otomatis terinstall dari Node.js)

---

## 📥 2. Clone atau Download Project

### **Clone melalui Git**
```bash
git clone https://github.com/username/nama-project.git
cd nama-project

Atau download ZIP

Download ZIP dari GitHub

Extract

Masuk ke folder project

📦 3. Install Dependency Laravel

Jalankan:

composer install


Ini akan menginstall semua package PHP dan membuat folder /vendor.

📦 4. Install Dependency Frontend (Vue + Vite)

Jalankan:

npm install


Ini akan menginstall semua package JavaScript.

⚙️ 5. Setup Environment File (.env)

Buat file .env:

cp .env.example .env


Generate key aplikasi Laravel:

php artisan key:generate

🛢️ 6. Konfigurasi Database

Edit .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=


Jika belum punya database:

php artisan migrate


Jika ingin jalankan dengan seeder:

php artisan migrate --seed

🧱 7. Build Frontend (Development atau Production)
Mode Development (auto reload)
npm run dev

Mode Production (file build siap deploy)
npm run build

▶️ 8. Jalankan Server Laravel
php artisan serve


Aplikasi akan berjalan di:

http://127.0.0.1:8000

🧪 9. Commands Tambahan
Clear cache
php artisan optimize:clear

Update dependencies
composer update
npm update

🚀 10. Siap Digunakan!

Anda sudah berhasil men-setup dan menjalankan aplikasi Laravel + Vue + Vite.

Jika ada kendala instalasi atau ingin membuat installer otomatis, silakan hubungi kembali.



