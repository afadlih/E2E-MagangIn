# E2E Tests - MagangIn Platform

## 📁 Struktur Folder

```
tests/e2e/
├── admin/              # Test untuk role Admin
├── mahasiswa/          # Test untuk role Mahasiswa
├── dosen/              # Test untuk role Dosen
├── industri/           # Test untuk role Industri/Perusahaan
└── multi-role/         # Test yang melibatkan multiple roles
```

## 🚀 Quick Start

### 1. Setup Authentication (Sekali saja)
```bash
node tests/setup-auth.js
```

### 2. Jalankan Test
```bash
# Test specific role
npx playwright test --project=admin
npx playwright test --project=mahasiswa
npx playwright test --project=dosen
npx playwright test --project=industri

# Test semua role
npx playwright test

# Test dengan UI mode
npx playwright test --ui
```

## 📝 Kredensial Testing

| Role | Username | Password | Dashboard URL |
|------|----------|----------|---------------|
| Admin | admin | 12345 | /dashboard-admin |
| Mahasiswa | mahasiswa | mhs | /dashboard-mahasiswa |
| Dosen | dosen | dsn | /dashboard-dosen |
| Industri | admin | 12345 | /dashboard-admin |

## 📸 Screenshots

Screenshots otomatis tersimpan di: `tests/screenshots/`

Format penamaan: `{role}-{feature}-{step}-{description}.png`

Contoh: `admin-prodi-01-halaman-prodi.png`

## 📚 Dokumentasi Lengkap

Lihat: [`tests/Documentation/README.md`](../Documentation/README.md)

## ⚠️ Catatan Penting

1. **Selalu jalankan `node tests/setup-auth.js`** setelah:
   - Database di-reset
   - Data user berubah
   - Session expired (biasanya setelah 2 jam)

2. **Pastikan Laravel server berjalan** di:
   ```
   http://localhost/E2E-MagangIn/public
   ```

3. **Form login menggunakan USERNAME, bukan email!**

## 🐛 Troubleshooting

### Error: storageState file not found
**Solusi:** Jalankan `node tests/setup-auth.js`

### Error: Timeout exceeded
**Solusi:** 
1. Cek Laravel server berjalan
2. Cek database terisi (run seeder)
3. Cek koneksi internet (jika ada external API)

### Error: Selector not found
**Solusi:**
1. Cek apakah halaman sudah loaded (`networkidle`)
2. Cek selector di browser DevTools
3. Gunakan multiple selector fallback

## 📞 Support

Untuk pertanyaan atau issue, buka GitHub Issues atau hubungi tim testing.
