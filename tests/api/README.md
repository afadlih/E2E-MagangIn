# API Testing - MagangIn Platform

## 📁 Struktur Folder

```
tests/api/
├── helpers.js              # Utility functions untuk API testing
├── auth/                   # Authentication API tests
│   └── login.spec.js
├── mahasiswa/              # Mahasiswa API tests
│   └── mahasiswa-crud.spec.js
└── lowongan/               # Lowongan API tests
    └── lowongan-list.spec.js
```

## 🚀 Cara Menjalankan API Tests

### 1. Jalankan Semua API Tests
```bash
npx playwright test --project=api
```

### 2. Jalankan Test Spesifik
```bash
# Test authentication
npx playwright test tests/api/auth/login.spec.js --project=api

# Test mahasiswa
npx playwright test tests/api/mahasiswa/mahasiswa-crud.spec.js --project=api

# Test lowongan
npx playwright test tests/api/lowongan/lowongan-list.spec.js --project=api
```

### 3. Jalankan dengan UI Mode
```bash
npx playwright test --project=api --ui
```

### 4. Jalankan dengan Debug
```bash
npx playwright test --project=api --debug
```

## 📝 Test Cases yang Sudah Dibuat

### Authentication (tests/api/auth/login.spec.js)
- ✅ POST /login - Success with valid credentials (admin)
- ✅ POST /login - Fail with invalid credentials
- ✅ POST /login - Fail with empty username
- ✅ POST /login - Mahasiswa login success
- ✅ POST /login - Dosen login success

### Mahasiswa (tests/api/mahasiswa/mahasiswa-crud.spec.js)
- ✅ GET /mahasiswa - List all mahasiswa
- ✅ POST /mahasiswa/list - DataTable list with authentication

### Lowongan (tests/api/lowongan/lowongan-list.spec.js)
- ✅ GET /lowongan - Access lowongan page
- ✅ POST /lowongan/list - Get lowongan list via DataTable

## 🔧 Helper Functions

File: `tests/api/helpers.js`

```javascript
// Login helper
await login(request, 'admin', '12345');

// Generate random string
const randomStr = generateRandomString(10);

// Get timestamp
const ts = getTimestamp();

// Assert response status
assertStatus(response, 200);

// Assert response has keys
await assertResponseHasKeys(response, ['status', 'data']);
```

## 📊 Expected Results

Setelah menjalankan `npx playwright test --project=api`, Anda akan melihat:

```
Running 10 tests using 1 worker

  ✓ API - Authentication › POST /login - Success with valid credentials
  ✓ API - Authentication › POST /login - Fail with invalid credentials
  ✓ API - Authentication › POST /login - Fail with empty username
  ✓ API - Authentication › POST /login - Mahasiswa login success
  ✓ API - Authentication › POST /login - Dosen login success
  ✓ API - Mahasiswa CRUD › GET /mahasiswa - List all mahasiswa
  ✓ API - Mahasiswa CRUD › POST /mahasiswa/list - DataTable list
  ✓ API - Lowongan › GET /lowongan - Access lowongan page
  ✓ API - Lowongan › POST /lowongan/list - Get lowongan list
  
  9 passed (XX.Xs)
```

## 🔑 Authentication untuk API Tests

Ada 2 cara authentication di API tests:

### 1. Request Context (untuk endpoint publik/login)
```javascript
const response = await request.post(`${BASE_URL}/login`, {
  form: {
    username: 'admin',
    password: '12345'
  }
});
```

### 2. Browser Context (untuk endpoint yang butuh session)
```javascript
// Login via browser dulu
await page.goto(`${BASE_URL}/login`);
await page.fill('input[name="username"]', 'admin');
await page.click('button[type="submit"]');

// Lalu gunakan page.request untuk API calls
const response = await page.request.get(`${BASE_URL}/mahasiswa`);
```

## 📝 Menambah Test Baru

### Template untuk API Test Baru

```javascript
const { test, expect } = require('@playwright/test');
const { BASE_URL } = require('../helpers');

test.describe('API - Your Feature', () => {
  
  test('GET /your-endpoint - Description', async ({ request }) => {
    const response = await request.get(`${BASE_URL}/your-endpoint`);
    
    expect(response.ok()).toBeTruthy();
    
    const body = await response.json();
    expect(body).toHaveProperty('expectedKey');
    
    console.log('✓ Test passed');
  });
});
```

## 🐛 Troubleshooting

### Error: 401 Unauthorized
**Solusi:** Gunakan browser context untuk login dulu sebelum test API

### Error: CSRF Token Mismatch
**Solusi:** Laravel API menggunakan session-based auth, gunakan browser context

### Error: Connection Refused
**Solusi:** Pastikan Laravel server berjalan di `http://localhost/E2E-MagangIn/public`

## 📚 Referensi

- [Playwright API Testing](https://playwright.dev/docs/api-testing)
- [Playwright Request Context](https://playwright.dev/docs/api/class-apirequestcontext)
- [Laravel API Documentation](https://laravel.com/docs/routing)

## ✅ Next Steps

1. ✅ Tambah test untuk endpoint lainnya:
   - POST /mahasiswa/ajax (create mahasiswa)
   - PUT /mahasiswa/{id}/update_ajax (update mahasiswa)
   - DELETE /mahasiswa/{id}/delete_ajax (delete mahasiswa)
   - GET /lowongan/{id} (detail lowongan)
   - POST /lamaran (apply lowongan)

2. ✅ Tambah test untuk validation:
   - Required fields
   - Data types
   - Max length
   - Unique constraints

3. ✅ Tambah test untuk error cases:
   - 404 Not Found
   - 403 Forbidden
   - 422 Validation Error

---

*Last Updated: 2025-11-03*  
*Status: ✅ API Testing Setup Complete*
