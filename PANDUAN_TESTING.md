# 🧪 Panduan Testing E2E MagangIn

## 📋 Daftar Isi
1. [Prerequisites](#prerequisites)
2. [Setup Awal](#setup-awal)
3. [Menjalankan E2E Testing](#menjalankan-e2e-testing)
4. [Menjalankan API Testing](#menjalankan-api-testing)
5. [Troubleshooting](#troubleshooting)
6. [Struktur Project](#struktur-project)

---

## 🔧 Prerequisites

Pastikan hal berikut sudah terinstall:
- ✅ Node.js (v16 atau lebih tinggi)
- ✅ Laragon/XAMPP (untuk menjalankan Laravel)
- ✅ PHP 8.x
- ✅ Composer
- ✅ MySQL/MariaDB

---

## 🚀 Setup Awal

### 1. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Install Playwright browsers
npx playwright install chromium
```

### 2. Setup Database

```bash
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database dengan user testing
php artisan db:seed
```

### 3. Generate Authentication State

```bash
# Generate storageState untuk semua role
node tests/setup-auth.js
```

Output yang diharapkan:
```
✓ Admin login berhasil
✓ Mahasiswa login berhasil
✓ Dosen login berhasil
✓ Industri login berhasil

Setup selesai! File storageState tersimpan di:
  - tests/auth/admin.json
  - tests/auth/mahasiswa.json
  - tests/auth/dosen.json
  - tests/auth/industri.json
```

---

## 🎭 Menjalankan E2E Testing

### Test Semua Role

```bash
# Run all E2E tests
npx playwright test

# Run dengan UI mode
npx playwright test --ui

# Run dengan headed browser
npx playwright test --headed
```

### Test Per Role

```bash
# Test admin only
npx playwright test --project=admin

# Test mahasiswa only
npx playwright test --project=mahasiswa

# Test dosen only
npx playwright test --project=dosen

# Test industri only
npx playwright test --project=industri

# Test multi-role scenarios
npx playwright test --project=multi-role
```

### Test Specific File

```bash
# Test file tertentu
npx playwright test tests/e2e/admin/admin-kelola-prodi.spec.js

# Test dengan project tertentu
npx playwright test tests/e2e/admin/admin-kelola-prodi.spec.js --project=admin
```

### Generate Report

```bash
# Generate HTML report
npx playwright show-report

# Report akan terbuka di browser: http://localhost:9323
```

---

## 🔌 Menjalankan API Testing

### Run All API Tests

```bash
# Test semua API endpoints
npx playwright test --project=api

# Hasil: 9 tests (Authentication, Mahasiswa, Lowongan)
```

### Run Specific API Tests

```bash
# Test authentication only
npx playwright test tests/api/auth/login.spec.js --project=api

# Test mahasiswa CRUD
npx playwright test tests/api/mahasiswa --project=api

# Test lowongan endpoints
npx playwright test tests/api/lowongan --project=api
```

### API Test Coverage

| Module | Tests | Endpoints |
|--------|-------|-----------|
| Authentication | 5 tests | POST /login |
| Mahasiswa | 2 tests | GET /mahasiswa, POST /mahasiswa |
| Lowongan | 2 tests | GET /lowongan, GET /lowongan/{id} |

---

## 🐛 Troubleshooting

### 1. Authentication Gagal

**Problem:**
```
TimeoutError: page.waitForURL: Timeout exceeded
```

**Solution:**
```bash
# Re-generate storageState
node tests/setup-auth.js

# Pastikan database sudah di-seed
php artisan db:seed
```

### 2. CSRF Token Mismatch (API Testing)

**Problem:**
```
419 CSRF token mismatch
```

**Solution:**
API tests sudah menggunakan browser context untuk handle CSRF token. Jika masih error:
```bash
# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
```

### 3. Port Already in Use (HTML Report)

**Problem:**
```
Error: Port 9323 is already in use
```

**Solution:**
```bash
# Kill process yang menggunakan port 9323
# Atau tunggu beberapa detik dan coba lagi
npx playwright show-report
```

### 4. Database Connection Error

**Problem:**
```
SQLSTATE[HY000] [2002] No connection could be made
```

**Solution:**
```bash
# Check .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=magangin_db
# DB_USERNAME=root
# DB_PASSWORD=

# Start MySQL/MariaDB service
# Di Laragon: klik "Start All"
```

### 5. Playwright Browsers Not Installed

**Problem:**
```
browserType.launch: Executable doesn't exist
```

**Solution:**
```bash
# Install browsers
npx playwright install chromium

# Atau install semua browsers
npx playwright install
```

---

## 📁 Struktur Project

```
E2E-MagangIn/
├── tests/
│   ├── api/                          # API Testing
│   │   ├── auth/
│   │   │   └── login.spec.js        # Authentication tests (5 tests)
│   │   ├── mahasiswa/
│   │   │   └── mahasiswa-crud.spec.js # Mahasiswa CRUD tests (2 tests)
│   │   ├── lowongan/
│   │   │   └── lowongan-list.spec.js # Lowongan tests (2 tests)
│   │   ├── helpers.js               # Helper functions
│   │   └── README.md                # API testing documentation
│   │
│   ├── e2e/                          # E2E Testing
│   │   ├── admin/
│   │   │   └── admin-kelola-prodi.spec.js
│   │   ├── mahasiswa/               # (TODO: add tests)
│   │   ├── dosen/                   # (TODO: add tests)
│   │   ├── industri/                # (TODO: add tests)
│   │   └── README.md
│   │
│   ├── auth/                         # StorageState files
│   │   ├── admin.json
│   │   ├── mahasiswa.json
│   │   ├── dosen.json
│   │   └── industri.json
│   │
│   └── setup-auth.js                 # Generate storageState
│
├── playwright.config.js              # Playwright configuration
├── PANDUAN_TESTING.md               # 📘 Panduan ini
├── PANDUAN_API_TESTING.md           # API testing step-by-step
├── HASIL_API_TESTING.md             # API testing results
└── README.md                         # Project README
```

---

## ✅ Checklist Testing

### Setup Awal
- [ ] Install dependencies (`npm install`, `composer install`)
- [ ] Setup database (`php artisan migrate`, `php artisan db:seed`)
- [ ] Generate storageState (`node tests/setup-auth.js`)
- [ ] Install Playwright browsers (`npx playwright install chromium`)

### E2E Testing
- [ ] Test admin role (`npx playwright test --project=admin`)
- [ ] Test mahasiswa role (`npx playwright test --project=mahasiswa`)
- [ ] Test dosen role (`npx playwright test --project=dosen`)
- [ ] Test industri role (`npx playwright test --project=industri`)
- [ ] Generate HTML report (`npx playwright show-report`)

### API Testing
- [ ] Test authentication (`npx playwright test tests/api/auth --project=api`)
- [ ] Test mahasiswa API (`npx playwright test tests/api/mahasiswa --project=api`)
- [ ] Test lowongan API (`npx playwright test tests/api/lowongan --project=api`)
- [ ] Review hasil testing (`cat HASIL_API_TESTING.md`)

---

## 📊 Test Results Summary

### E2E Testing
- **Admin:** 1 test ✅ PASSING
- **Mahasiswa:** TODO
- **Dosen:** TODO
- **Industri:** TODO

### API Testing
- **Authentication:** 5 tests ✅ ALL PASS (100%)
- **Mahasiswa:** 2 tests ✅ ALL PASS (100%)
- **Lowongan:** 2 tests ✅ ALL PASS (100%)
- **Total:** 9 tests ✅ ALL PASS (100%)

---

## 🔗 Referensi

- [Playwright Documentation](https://playwright.dev)
- [Playwright Best Practices](https://playwright.dev/docs/best-practices)
- [Laravel Testing](https://laravel.com/docs/testing)

---

## 💡 Tips

1. **Gunakan UI Mode untuk Debugging:**
   ```bash
   npx playwright test --ui
   ```

2. **Gunakan Trace Viewer untuk Melihat Detail Error:**
   ```bash
   npx playwright show-trace test-results/path/to/trace.zip
   ```

3. **Run Tests dalam Headed Mode untuk Melihat Browser:**
   ```bash
   npx playwright test --headed
   ```

4. **Filter Tests dengan Grep:**
   ```bash
   npx playwright test --grep "login"
   ```

5. **Debug Specific Test:**
   ```bash
   npx playwright test --debug tests/api/auth/login.spec.js
   ```

---

**Last Updated:** November 3, 2025  
**Version:** 2.0  
**Status:** ✅ Production Ready
