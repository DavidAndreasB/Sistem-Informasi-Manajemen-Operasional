# 🏭 Venus Tekindo — Sistem Informasi Manajemen Operasional

Aplikasi web **full-stack** untuk manajemen operasional workshop manufaktur, dibangun dengan **Laravel 12**. Sistem ini mendigitalisasi alur kerja dari pembuatan Surat Perintah Kerja (SPK) hingga pencatatan jobsheet dan quality control.

> 📌 **Konteks:** Dibuat sebagai project Skripsi/Tugas Akhir untuk kebutuhan nyata PT Venus Tekindo — sebuah workshop manufaktur yang bergerak di bidang machining, welding, dan metal finishing.

---

## ✨ Fitur Utama

### 📋 Manajemen SPK (Surat Perintah Kerja)
- CRUD SPK lengkap dengan nomor otomatis (format: `VT.DD.MM.YY.XXX`)
- Multi-item per SPK dengan tracking status per item
- Asosiasi mesin ke setiap item kerja (pivot table)
- Export SPK ke PDF (menggunakan DomPDF)

### 📝 Jobsheet & Pencatatan Kerja
- Pencatatan aktivitas pengerjaan per operator per mesin
- Tracking durasi kerja (jam mulai - selesai) dengan validasi
- Riwayat pekerjaan terhubung ke SPK item spesifik
- Kalkulasi total jam kerja otomatis

### ✅ Quality Control (QC)
- Sistem approval per item (Pending → OK / Reject)
- Catatan QC untuk item yang di-reject
- Status pengerjaan bertingkat (Proses → Selesai → QC)

### 💰 Simulasi Harga
- Kalkulator harga berdasarkan tarif mesin dari database
- Export hasil simulasi ke PDF untuk penawaran harga

### 👥 Role-Based Access Control (RBAC)
| Role | Akses |
|------|-------|
| **Super Admin** | Full access: user management, mesin, client, simulasi harga, SPK, jobsheet |
| **Quality Control** | Review & approve/reject item, lihat SPK & jobsheet |
| **Operator** | Input jobsheet, update status pengerjaan item |

### 🏗️ Master Data
- **Mesin**: CRUD mesin dengan tarif per jam (Milling, Bubut, Grinding, Las, dll.)
- **Client**: Manajemen data pelanggan dengan inisial

### 📊 Dashboard
- Ringkasan jumlah SPK aktif, selesai, total item
- Overview operasional real-time

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 12 (PHP 8.2+) |
| **Frontend** | Blade Templates + SBAdmin 2 (Bootstrap 4) |
| **Database** | MySQL |
| **PDF Generation** | Barryvdh DomPDF |
| **Authentication** | Laravel Breeze |
| **Asset Bundling** | Vite |
| **DataTables** | jQuery DataTables |

---

## 📐 Arsitektur & Database

### Entity Relationship (Ringkas)

```
User (1) ──────── (N) JobSheet
  │
  └── role: super_admin | quality_control | operator

Client (1) ──────── (N) Spk
Spk (1) ──────── (N) SpkItem
SpkItem (N) ──── (N) Machine  [pivot: machine_spk_item]
Spk (1) ──────── (N) JobSheet
SpkItem (1) ──── (N) JobSheet
```

### Struktur Direktori (Key Files)

```
app/
├── Http/Controllers/
│   ├── SpkController.php          # CRUD SPK + PDF export
│   ├── JobSheetController.php     # Pencatatan kerja operator
│   ├── QcController.php           # Quality control approval
│   ├── SimulasiController.php     # Kalkulator harga + PDF
│   ├── MachineController.php      # Master data mesin
│   ├── ClientController.php       # Master data client
│   ├── UserController.php         # User management (admin)
│   └── DashboardController.php    # Dashboard overview
├── Models/
│   ├── Spk.php                    # Auto-increment SPK number
│   ├── SpkItem.php                # Many-to-many with Machine
│   ├── JobSheet.php               # Work logging
│   ├── Machine.php                # Mesin + tarif
│   ├── Client.php                 # Data pelanggan
│   └── User.php                   # RBAC (3 roles)
database/
├── migrations/                    # 16 migration files
└── seeders/
    └── DatabaseSeeder.php         # Comprehensive dummy data
```

---

## 🚀 Instalasi & Setup

### Prasyarat
- PHP 8.2+
- Composer
- MySQL
- Node.js & NPM

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/DavidAndreasB/Skripsi.git
cd Skripsi

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Buat database MySQL
# Buat database bernama 'venus_tekindo' di MySQL

# 5. Jalankan migrasi & seeder
php artisan migrate --seed

# 6. Build assets & jalankan server
npm run build
php artisan serve
```

### Akun Default (dari Seeder)

| Role | Username | Password |
|------|----------|----------|
| Super Admin | `superadmin` | `admin123` |
| Quality Control | `qc_user` | `password` |
| Operator | `operator_1` | `password` |

> ⚠️ **Catatan:** Password di atas hanya untuk development. Selalu ganti password di environment production.

---

## 📸 Screenshots

> *(Coming soon — akan ditambahkan screenshot dashboard, SPK, dan jobsheet)*

---

## 📄 Lisensi

Project ini dibuat sebagai Skripsi/Tugas Akhir. Source code tersedia untuk referensi dan pembelajaran.

---

## 👤 Author

**David Andreas B.**
- GitHub: [@DavidAndreasB](https://github.com/DavidAndreasB)
