# 🎓 E2E-MagangIn - Sistem Manajemen Magang JTI Polinema# E2E-MagangIn - Sistem Manajemen Magang



Platform resmi untuk memfasilitasi mahasiswa Jurusan Teknologi Informasi Politeknik Negeri Malang dalam proses pengajuan, pencatatan, dan pemantauan kegiatan magang atau kerja praktik.Sistem informasi untuk mengelola proses magang mahasiswa dengan perusahaan/industri.



---## 🧪 E2E Testing dengan Playwright Multi-Role



## 📋 Daftar IsiTesting E2E menggunakan **Playwright** dengan arsitektur **StorageState Authentication** untuk 4 role utama:

- **Admin** - Kelola Master Data, Persetujuan Lamaran, Monitoring

- [Tentang Project](#-tentang-project)- **Mahasiswa** - Registrasi, Pengajuan Lowongan, Log Aktivitas Harian

- [Teknologi](#%EF%B8%8F-teknologi)- **Dosen** - Validasi Log, Monitoring Progres, Laporan Bimbingan

- [Setup & Instalasi](#-setup--instalasi)- **Industri** - Posting Lowongan, Review Lamaran, Validasi Log, Generate Sertifikat

- [Testing](#-testing)

- [Fitur Utama](#-fitur-utama)### 🚀 Quick Start Testing

- [Role & Permission](#-role--permission)

- [Dokumentasi](#-dokumentasi)#### **1. Setup Authentication (Sekali Saja)**

```bash

---node tests/setup-auth.js

```

## 🎯 Tentang Project

#### **2. Jalankan Test per Role**

**E2E-MagangIn** adalah sistem manajemen magang yang mencakup:```bash

- 📝 Pendaftaran dan pengajuan magang# Test Admin

- 🏢 Manajemen lowongan dari industrinpx playwright test --project=admin

- 👨‍🏫 Monitoring dan bimbingan dari dosen

- 📊 Laporan dan evaluasi# Test Mahasiswa

- 🔐 Multi-role authentication (Admin, Mahasiswa, Dosen, Industri)npx playwright test --project=mahasiswa



---# Test Dosen

npx playwright test --project=dosen

## 🛠️ Teknologi

# Test Industri

### Backendnpx playwright test --project=industri

- **Laravel 10.x** - PHP Framework

- **MySQL/MariaDB** - Database# Test Multi-Role (Alur Lengkap)

- **PHP 8.x** - Programming Languagenpx playwright test --project=multi-role

```

### Frontend

- **Blade Templates** - Laravel Templating Engine#### **3. Lihat Report**

- **Bootstrap/KaiAdmin** - UI Framework```bash

- **jQuery** - JavaScript Librarynpx playwright show-report

- **SweetAlert2** - Alert/Modal Library```



### Testing### 📚 Dokumentasi Lengkap

- **Playwright** - E2E Testing Framework

- **PHPUnit** - Unit Testing#### **Dokumentasi Utama:**

- **Node.js** - JavaScript Runtime- **[`tests/Documentation/README.md`](tests/Documentation/README.md)** 🌟 - **Index semua dokumentasi** (BACA INI DULU!)

- **[`tests/Documentation/PANDUAN_STORAGESTATE.md`](tests/Documentation/PANDUAN_STORAGESTATE.md)** 🔐 - Setup & usage storageState

---- **[`tests/Documentation/CHECKLIST_SETUP.md`](tests/Documentation/CHECKLIST_SETUP.md)** ✅ - Checklist setup & eksekusi



## 🚀 Setup & Instalasi#### **Test Scenarios:**

- **[`tests/Documentation/API_TEST_CASES.md`](tests/Documentation/API_TEST_CASES.md)** 📊 - 26 API test cases

### 1. Clone Repository- **[`tests/Documentation/E2E_SCENARIOS_MULTI_ROLE.md`](tests/Documentation/E2E_SCENARIOS_MULTI_ROLE.md)** 🎭 - 25 E2E scenarios



```bash#### **Tutorial Tim:**

git clone https://github.com/afadlih/E2E-MagangIn.git- **`RINGKASAN_TESTING.md`** - Panduan eksekusi test

cd E2E-MagangIn- **`TUTORIAL_TEAM_TESTING.md`** - Template code untuk semua role

```

### 📊 Test Coverage

### 2. Install Dependencies

| Role | Scenarios Designed | Implemented | Status |

```bash|------|-------------------|-------------|--------|

# PHP dependencies| Admin | 5 | 1 | ✅ |

composer install| Mahasiswa | 5 | 1 | ✅ |

| Dosen | 5 | 1 | ✅ |

# Node.js dependencies| Industri | 5 | 1 | ✅ |

npm install| Multi-Role | 5 | 1 | ✅ |

| API Tests | 26 | 0 | 📋 |

# Playwright browsers| **Total** | **51** | **5** | **10%** |

npx playwright install chromium

```---



### 3. Setup Environment## About Laravel



```bashLaravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

# Copy environment file

copy .env.example .env- [Simple, fast routing engine](https://laravel.com/docs/routing).

- [Powerful dependency injection container](https://laravel.com/docs/container).

# Generate application key- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.

php artisan key:generate- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).

```- Database agnostic [schema migrations](https://laravel.com/docs/migrations).

- [Robust background job processing](https://laravel.com/docs/queues).

Edit `.env` file:- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

```env

DB_CONNECTION=mysqlLaravel is accessible, powerful, and provides tools required for large, robust applications.

DB_HOST=127.0.0.1

DB_PORT=3306## Learning Laravel

DB_DATABASE=magangin_db

DB_USERNAME=rootLaravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

DB_PASSWORD=

```You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.



### 4. Setup DatabaseIf you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.



```bash## Laravel Sponsors

# Run migrations

php artisan migrateWe would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).



# Seed database dengan user testing### Premium Partners

php artisan db:seed

```- **[Vehikl](https://vehikl.com/)**

- **[Tighten Co.](https://tighten.co)**

### 5. Start Application- **[WebReinvent](https://webreinvent.com/)**

- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**

```bash- **[64 Robots](https://64robots.com)**

# Start Laravel development server- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**

php artisan serve- **[Cyber-Duck](https://cyber-duck.co.uk)**

- **[DevSquad](https://devsquad.com/hire-laravel-developers)**

# Atau gunakan Laragon/XAMPP- **[Jump24](https://jump24.co.uk)**

# URL: http://localhost/E2E-MagangIn/public- **[Redberry](https://redberry.international/laravel/)**

```- **[Active Logic](https://activelogic.com)**

- **[byte5](https://byte5.de)**

---- **[OP.GG](https://op.gg)**



## 🧪 Testing## Contributing



Project ini memiliki **E2E Testing** dan **API Testing** yang lengkap dengan 100% pass rate.Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).



### 📘 Panduan Testing Lengkap## Code of Conduct



**➡️ [PANDUAN_TESTING.md](PANDUAN_TESTING.md)** - Dokumentasi testing lengkapIn order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).



### Quick Start Testing## Security Vulnerabilities



#### 1. Generate Authentication StateIf you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.



```bash## License

node tests/setup-auth.js

```The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


Output yang diharapkan:
```
✓ Admin login berhasil
✓ Mahasiswa login berhasil
✓ Dosen login berhasil
✓ Industri login berhasil
```

#### 2. Run E2E Tests

```bash
# Test semua role
npx playwright test

# Test per role
npx playwright test --project=admin
npx playwright test --project=mahasiswa
npx playwright test --project=dosen
npx playwright test --project=industri
```

#### 3. Run API Tests

```bash
# Test semua API
npx playwright test --project=api

# Test specific module
npx playwright test tests/api/auth --project=api
npx playwright test tests/api/mahasiswa --project=api
npx playwright test tests/api/lowongan --project=api
```

#### 4. Generate HTML Report

```bash
npx playwright show-report
```

### Test Results Summary

| Test Type | Tests | Status | Pass Rate |
|-----------|-------|--------|-----------|
| **E2E - Admin** | 1 test | ✅ PASS | 100% |
| **API - Authentication** | 5 tests | ✅ ALL PASS | 100% |
| **API - Mahasiswa** | 2 tests | ✅ ALL PASS | 100% |
| **API - Lowongan** | 2 tests | ✅ ALL PASS | 100% |
| **TOTAL** | **10 tests** | **✅ ALL PASS** | **100%** |

---

## ✨ Fitur Utama

### 👨‍💼 Admin
- ✅ Kelola master data (Prodi, Level, User)
- ✅ Monitoring semua aktivitas
- ✅ Generate laporan
- ✅ Approval & validasi

### 👨‍🎓 Mahasiswa
- ✅ Registrasi & login
- ✅ Browse lowongan magang
- ✅ Apply lamaran
- ✅ Log aktivitas harian
- ✅ Upload laporan

### 👨‍🏫 Dosen
- ✅ Monitoring mahasiswa bimbingan
- ✅ Validasi log aktivitas
- ✅ Feedback & review
- ✅ Generate laporan bimbingan

### 🏢 Industri
- ✅ Posting lowongan
- ✅ Review lamaran mahasiswa
- ✅ Validasi log aktivitas
- ✅ Generate sertifikat

---

## 🔐 Role & Permission

### Default Users (untuk testing)

| Role | Username | Password | Dashboard URL |
|------|----------|----------|---------------|
| **Admin** | `admin` | `12345` | `/dashboard-admin` |
| **Mahasiswa** | `mahasiswa` | `mhs` | `/dashboard-mahasiswa` |
| **Dosen** | `dosen` | `dsn` | `/dashboard-dosen` |
| **Industri** | `industri` | `ind` | `/dashboard-industri` |

### Permission Matrix

| Fitur | Admin | Mahasiswa | Dosen | Industri |
|-------|-------|-----------|-------|----------|
| Kelola Master Data | ✅ | ❌ | ❌ | ❌ |
| Browse Lowongan | ✅ | ✅ | ✅ | ❌ |
| Posting Lowongan | ❌ | ❌ | ❌ | ✅ |
| Apply Lamaran | ❌ | ✅ | ❌ | ❌ |
| Review Lamaran | ✅ | ❌ | ❌ | ✅ |
| Log Aktivitas | ❌ | ✅ | ❌ | ❌ |
| Validasi Log | ❌ | ❌ | ✅ | ✅ |
| Generate Laporan | ✅ | ✅ | ✅ | ✅ |

---

## 📁 Struktur Project

```
E2E-MagangIn/
├── app/                          # Laravel application
│   ├── Http/Controllers/        # Controllers
│   ├── Models/                  # Eloquent models
│   ├── Services/                # Business logic
│   └── Enums/                   # Enumerations
│
├── database/
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Database seeders
│
├── resources/
│   ├── views/                   # Blade templates
│   ├── css/                     # Stylesheets
│   └── js/                      # JavaScript files
│
├── routes/
│   ├── web.php                  # Web routes
│   └── api.php                  # API routes
│
├── tests/
│   ├── api/                     # API tests (9 tests)
│   │   ├── auth/               # Authentication tests
│   │   ├── mahasiswa/          # Mahasiswa CRUD tests
│   │   ├── lowongan/           # Lowongan tests
│   │   ├── helpers.js          # Helper functions
│   │   └── README.md           # API test docs
│   │
│   ├── e2e/                     # E2E tests
│   │   ├── admin/              # Admin tests
│   │   ├── mahasiswa/          # Mahasiswa tests (TODO)
│   │   ├── dosen/              # Dosen tests (TODO)
│   │   ├── industri/           # Industri tests (TODO)
│   │   └── README.md           # E2E test docs
│   │
│   ├── auth/                    # StorageState files
│   │   ├── admin.json
│   │   ├── mahasiswa.json
│   │   ├── dosen.json
│   │   └── industri.json
│   │
│   └── setup-auth.js            # Auth setup script
│
├── public/                      # Public assets
├── playwright.config.js         # Playwright config
├── PANDUAN_TESTING.md          # 📘 Testing guide
├── PANDUAN_API_TESTING.md      # API testing guide
├── HASIL_API_TESTING.md        # API test results
└── README.md                    # This file
```

---

## 📚 Dokumentasi

### Testing Documentation
- 📘 **[PANDUAN_TESTING.md](PANDUAN_TESTING.md)** - Panduan lengkap testing (E2E & API)
- 🔌 **[PANDUAN_API_TESTING.md](PANDUAN_API_TESTING.md)** - Step-by-step API testing
- 📊 **[HASIL_API_TESTING.md](HASIL_API_TESTING.md)** - Summary hasil API testing
- 📖 **[tests/api/README.md](tests/api/README.md)** - API test documentation
- 📖 **[tests/e2e/README.md](tests/e2e/README.md)** - E2E test documentation

---

## 🤝 Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📝 License

This project is private and proprietary.

---

## 👥 Team

**Jurusan Teknologi Informasi**  
**Politeknik Negeri Malang**

---

## 🎯 Project Status

- ✅ **Authentication System** - Complete & Tested
- ✅ **E2E Testing Framework** - Complete (Playwright)
- ✅ **API Testing** - Complete (9 tests, 100% pass rate)
- ✅ **Admin Dashboard** - Complete & Tested
- 🔄 **Feature Development** - In Progress
- 🔄 **Full E2E Test Coverage** - In Progress

---

**Last Updated:** November 3, 2025  
**Version:** 2.0  
**Status:** 🚀 Active Development
