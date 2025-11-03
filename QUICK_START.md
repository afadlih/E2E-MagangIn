# 🚀 QUICK START - E2E MagangIn Testing

## ⚡ Setup Cepat (5 Menit)

### 1. Install Dependencies
```bash
npm install
composer install
npx playwright install chromium
```

### 2. Setup Database
```bash
php artisan migrate
php artisan db:seed
```

### 3. Generate Authentication
```bash
node tests/setup-auth.js
```

---

## 🧪 Run Tests (1 Menit)

### API Testing (9 tests - 100% PASS)
```bash
npx playwright test --project=api
```

### E2E Testing (1 test - 100% PASS)
```bash
npx playwright test --project=admin
```

### Generate Report
```bash
npx playwright show-report
```

---

## 📊 Expected Results

### ✅ API Tests
- Authentication: 5 tests ✅
- Mahasiswa CRUD: 2 tests ✅
- Lowongan: 2 tests ✅
- **Total: 9 tests ✅ (100%)**

### ✅ E2E Tests
- Admin: 1 test ✅
- **Total: 1 test ✅ (100%)**

---

## 📖 Dokumentasi Lengkap

| File | Deskripsi |
|------|-----------|
| `PANDUAN_TESTING.md` | 📘 Complete testing guide |
| `CHECKLIST_TESTING.md` | ✅ Testing checklist |
| `RINGKASAN_PROJECT.md` | 📊 Project summary |
| `PANDUAN_API_TESTING.md` | 🔌 API testing guide |
| `HASIL_API_TESTING.md` | 📈 API test results |

---

## 🔐 Default Users

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `12345` |
| Mahasiswa | `mahasiswa` | `mhs` |
| Dosen | `dosen` | `dsn` |
| Industri | `industri` | `ind` |

---

## 🐛 Quick Fix

### Authentication Error?
```bash
node tests/setup-auth.js
```

### CSRF Error?
```bash
php artisan cache:clear
```

### Port 9323 Busy?
```bash
# Wait 10 seconds and retry
```

---

## ✅ Status: READY FOR TESTING
**Version:** 2.0  
**Pass Rate:** 100% (10/10 tests)  
**Last Updated:** November 3, 2025
