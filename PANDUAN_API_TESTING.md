# 🧪 PANDUAN API TESTING - Step by Step

## 📋 Prerequisites

Sebelum memulai, pastikan:
- ✅ Laravel server berjalan di `http://localhost/E2E-MagangIn/public`
- ✅ Database sudah di-seed (`php artisan db:seed`)
- ✅ Playwright sudah ter-install (`npm install`)
- ✅ User testing tersedia (admin, mahasiswa, dosen)

---

## 🚀 LANGKAH 1: Jalankan API Test Pertama Kali

### A. Test Authentication API

```bash
npx playwright test tests/api/auth/login.spec.js --project=api
```

**Expected Output:**
```
Running 5 tests using 1 worker

  ✓ API - Authentication › POST /login - Success with valid credentials
  ✓ API - Authentication › POST /login - Fail with invalid credentials
  ✓ API - Authentication › POST /login - Fail with empty username
  ✓ API - Authentication › POST /login - Mahasiswa login success
  ✓ API - Authentication › POST /login - Dosen login success

  5 passed (3.2s)
```

### B. Test Mahasiswa API

```bash
npx playwright test tests/api/mahasiswa/mahasiswa-crud.spec.js --project=api
```

### C. Test Lowongan API

```bash
npx playwright test tests/api/lowongan/lowongan-list.spec.js --project=api
```

### D. Test Semua API

```bash
npx playwright test --project=api
```

---

## 📊 LANGKAH 2: Lihat Report

```bash
npx playwright show-report
```

Report HTML akan terbuka di browser dengan informasi:
- ✅ Test yang passed/failed
- ⏱️ Durasi eksekusi
- 📸 Screenshots (jika ada error)
- 📝 Console logs

---

## 🔧 LANGKAH 3: Menambah Test API Baru

### Template Test Baru

Buat file baru: `tests/api/your-module/your-test.spec.js`

```javascript
const { test, expect } = require('@playwright/test');
const { BASE_URL } = require('../helpers');

test.describe('API - Your Module', () => {
  
  test('GET /your-endpoint - Test description', async ({ request }) => {
    // 1. Hit API endpoint
    const response = await request.get(`${BASE_URL}/your-endpoint`);
    
    // 2. Assert response status
    expect(response.ok()).toBeTruthy();
    
    // 3. Assert response body
    const body = await response.json();
    expect(body).toHaveProperty('expectedKey');
    
    // 4. Log success
    console.log('✓ Test passed');
  });
});
```

### Contoh: Test Create Mahasiswa

```javascript
test('POST /mahasiswa/ajax - Create new mahasiswa', async ({ page }) => {
  // 1. Login dulu
  await page.goto(`${BASE_URL}/login`);
  await page.fill('input[name="username"]', 'admin');
  await page.fill('input[name="password"]', '12345');
  await page.click('button[type="submit"]');
  await page.waitForSelector('.swal2-confirm');
  await page.click('.swal2-confirm');
  await page.waitForURL('**/dashboard-admin');
  
  // 2. Hit API endpoint
  const response = await page.request.post(`${BASE_URL}/mahasiswa/ajax`, {
    data: {
      nim: '12345678',
      nama_lengkap: 'Test Mahasiswa',
      email: 'test@test.com',
      no_hp: '081234567890',
      prodi_id: 1
    }
  });
  
  // 3. Assert
  expect(response.ok()).toBeTruthy();
  const body = await response.json();
  expect(body).toHaveProperty('status');
  expect(body.status).toBe(true);
  
  console.log('✓ Mahasiswa created successfully');
});
```

---

## 🎯 LANGKAH 4: Test dengan Authentication

### Method 1: Request Context (untuk login endpoint)

```javascript
test('Login test', async ({ request }) => {
  const response = await request.post(`${BASE_URL}/login`, {
    form: {
      username: 'admin',
      password: '12345'
    }
  });
  
  expect(response.ok()).toBeTruthy();
});
```

### Method 2: Browser Context (untuk protected endpoints)

```javascript
test('Protected endpoint test', async ({ page }) => {
  // Login via browser
  await page.goto(`${BASE_URL}/login`);
  await page.fill('input[name="username"]', 'admin');
  await page.fill('input[name="password"]', '12345');
  await page.click('button[type="submit"]');
  await page.waitForSelector('.swal2-confirm');
  await page.click('.swal2-confirm');
  await page.waitForURL('**/dashboard-admin');
  
  // Sekarang session tersimpan, bisa hit API
  const response = await page.request.get(`${BASE_URL}/mahasiswa`);
  expect(response.ok()).toBeTruthy();
});
```

---

## 📝 LANGKAH 5: Test Cases yang Harus Dibuat

### Priority 1 (Critical) - Authentication
- [x] POST /login - Admin success
- [x] POST /login - Mahasiswa success
- [x] POST /login - Dosen success
- [x] POST /login - Invalid credentials
- [ ] POST /logout - Success
- [ ] POST /register/mahasiswa - Success
- [ ] POST /register/mahasiswa - Validation error

### Priority 2 (High) - CRUD Operations
- [x] GET /mahasiswa - List
- [x] POST /mahasiswa/list - DataTable
- [ ] POST /mahasiswa/ajax - Create
- [ ] PUT /mahasiswa/{id}/update_ajax - Update
- [ ] DELETE /mahasiswa/{id}/delete_ajax - Delete
- [ ] GET /mahasiswa/{id}/show_ajax - Detail

### Priority 3 (Medium) - Business Logic
- [x] GET /lowongan - List
- [x] POST /lowongan/list - DataTable
- [ ] GET /lowongan/{id} - Detail
- [ ] POST /lamaran - Apply lowongan
- [ ] GET /lamaran/mahasiswa/{nim} - List lamaran

### Priority 4 (Low) - Reports & Exports
- [ ] GET /mahasiswa/export_pdf - Export PDF
- [ ] GET /mahasiswa/export_excel - Export Excel
- [ ] GET /laporan - List laporan

---

## 🐛 LANGKAH 6: Troubleshooting

### Problem 1: Error 401 Unauthorized

**Symptom:** API returns 401 status

**Solution:**
```javascript
// Gunakan browser context untuk login dulu
await page.goto(`${BASE_URL}/login`);
// ... login process
// Lalu gunakan page.request
const response = await page.request.get(`${BASE_URL}/protected-endpoint`);
```

### Problem 2: CSRF Token Mismatch

**Symptom:** Laravel returns "CSRF token mismatch"

**Solution:**
```javascript
// Untuk POST requests via browser context, CSRF otomatis handled
// Jika pakai request context, tambahkan header:
const response = await request.post(`${BASE_URL}/endpoint`, {
  headers: {
    'X-CSRF-TOKEN': csrfToken
  },
  data: { ... }
});
```

### Problem 3: Connection Refused

**Symptom:** `ECONNREFUSED`

**Solution:**
1. Pastikan Laravel server berjalan
2. Cek URL di `helpers.js`: `http://localhost/E2E-MagangIn/public`
3. Test manual di browser

### Problem 4: Test Timeout

**Symptom:** `Timeout 30000ms exceeded`

**Solution:**
```javascript
// Tambah timeout di test
test('Your test', async ({ page }) => {
  test.setTimeout(60000); // 60 seconds
  // ... test code
});
```

---

## 📊 LANGKAH 7: Generate Report Lengkap

### A. HTML Report (Default)

```bash
npx playwright test --project=api
npx playwright show-report
```

### B. JSON Report

Update `playwright.config.js`:
```javascript
reporter: [
  ['html'],
  ['json', { outputFile: 'test-results/api-results.json' }]
],
```

### C. CI/CD Report (JUnit)

```javascript
reporter: [
  ['html'],
  ['junit', { outputFile: 'test-results/junit.xml' }]
],
```

---

## 📈 LANGKAH 8: Best Practices

### 1. Gunakan Helpers
```javascript
const { BASE_URL, assertStatus, assertResponseHasKeys } = require('../helpers');

// Good
assertStatus(response, 200);
await assertResponseHasKeys(response, ['status', 'data']);

// Bad
expect(response.status()).toBe(200);
```

### 2. Consistent Naming
```javascript
// Good
test('POST /mahasiswa/ajax - Create mahasiswa success', ...)
test('POST /mahasiswa/ajax - Create mahasiswa validation error', ...)

// Bad
test('create mhs', ...)
test('test validation', ...)
```

### 3. Group Related Tests
```javascript
test.describe('API - Mahasiswa CRUD', () => {
  test.describe('Create Operations', () => {
    test('Success case', ...);
    test('Validation error', ...);
  });
  
  test.describe('Update Operations', () => {
    test('Success case', ...);
    test('Not found error', ...);
  });
});
```

### 4. Clean Up Test Data
```javascript
test.afterEach(async ({ page }) => {
  // Hapus data test
  await page.request.delete(`${BASE_URL}/mahasiswa/${testId}/delete_ajax`);
});
```

---

## ✅ Checklist Completion

Setelah selesai setup API testing:

- [x] Struktur folder `tests/api/` dibuat
- [x] Helper functions dibuat (`helpers.js`)
- [x] Authentication tests dibuat (5 tests)
- [x] Mahasiswa tests dibuat (2 tests)
- [x] Lowongan tests dibuat (2 tests)
- [x] Playwright config updated (project 'api' added)
- [x] README.md untuk API testing dibuat
- [ ] Jalankan semua tests dan pastikan passing
- [ ] Generate report HTML
- [ ] Dokumentasikan hasil testing

---

## 📞 Support & Resources

- **Playwright API Docs:** https://playwright.dev/docs/api-testing
- **Laravel API Docs:** https://laravel.com/docs/routing
- **Project README:** `tests/api/README.md`
- **Main Docs:** `tests/Documentation/README.md`

---

*Last Updated: 2025-11-03*  
*Status: ✅ API Testing Guide Complete*
