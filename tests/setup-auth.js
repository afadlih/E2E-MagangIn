/**
 * Setup Authentication dengan StorageState
 * File ini akan generate file auth/*.json untuk setiap role
 * Run sekali sebelum testing: node tests/setup-auth.js
 */

const { chromium } = require('@playwright/test');
const path = require('path');
const fs = require('fs');

// ===== KREDENSIAL LOGIN =====
// CATATAN: Kredensial ini sesuai dengan database/seeders/UserSeeder.php
const CREDENTIALS = {
  admin: {
    username: 'admin',      // Sesuai UserSeeder
    password: '12345',      // Sesuai UserSeeder
    url: '/login',
    storageFile: 'tests/auth/admin.json'
  },
  mahasiswa: {
    username: 'mahasiswa',  // Sesuai UserSeeder
    password: 'mhs',        // Sesuai UserSeeder
    url: '/login',
    storageFile: 'tests/auth/mahasiswa.json'
  },
  dosen: {
    username: 'dosen',      // Sesuai UserSeeder
    password: 'dsn',        // Sesuai UserSeeder
    url: '/login',
    storageFile: 'tests/auth/dosen.json'
  },
  industri: {
    username: 'admin',      // TEMPORARY: Gunakan admin karena belum ada user industri
    password: '12345',      // TEMPORARY: Gunakan admin password
    url: '/login',
    storageFile: 'tests/auth/industri.json'
  }
};

const BASE_URL = 'http://localhost/E2E-MagangIn/public';

// ===== FUNGSI UTAMA =====
async function setupAuth() {
  console.log('🚀 Memulai proses setup authentication...\n');

  // Buat folder auth jika belum ada
  const authDir = path.join(__dirname, 'auth');
  if (!fs.existsSync(authDir)) {
    fs.mkdirSync(authDir, { recursive: true });
    console.log('✅ Folder tests/auth/ dibuat\n');
  }

  // Launch browser
  const browser = await chromium.launch({
    headless: false,  // Set true untuk CI/CD
    slowMo: 500       // Delay untuk visibility
  });

  // Loop setiap role
  for (const [role, cred] of Object.entries(CREDENTIALS)) {
    console.log(`📝 Setup auth untuk role: ${role.toUpperCase()}`);

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
      // 1. Navigasi ke halaman login
      await page.goto(`${BASE_URL}${cred.url}`, { waitUntil: 'networkidle' });
      console.log(`   ➤ Navigasi ke: ${BASE_URL}${cred.url}`);

      // 2. Screenshot sebelum login
      await page.screenshot({
        path: `tests/auth/${role}-login-page.png`,
        fullPage: true
      });

      // 3. Isi form login
      // Form menggunakan 'username' bukan 'email' (sesuai login.blade.php)
      await page.fill('input[name="username"]', cred.username);
      await page.fill('input[name="password"]', cred.password);
      console.log(`   ➤ Form login terisi: ${cred.username}`);

      // 4. Klik tombol login
      await page.click('button[type="submit"]');
      console.log(`   ➤ Klik tombol login`);

      // 5. Tunggu response AJAX (3 detik untuk proses)
      await page.waitForTimeout(3000);

      // 6. Screenshot untuk debugging
      await page.screenshot({ path: `tests/auth/${role}-after-login.png`, fullPage: true });

      // 7. Cek apakah ada error validation message
      const errorMsg = await page.locator('.invalid-feedback').count();
      if (errorMsg > 0) {
        const errorText = await page.locator('.invalid-feedback').first().textContent();
        console.log(`   ⚠️  Validation error: ${errorText}`);
      }

      // 8. Tunggu SweetAlert muncul (bisa success atau error)
      try {
        await page.waitForSelector('.swal2-container', { state: 'visible', timeout: 5000 });
        console.log(`   ➤ SweetAlert muncul`);

        // Cek apakah success atau error
        const swalTitle = await page.locator('.swal2-title').textContent();
        console.log(`   ➤ SweetAlert title: ${swalTitle}`);

        // Jika error, ambil pesan error
        if (swalTitle.includes('Kesalahan') || swalTitle.includes('Error') || swalTitle.includes('Gagal')) {
          const swalText = await page.locator('.swal2-html-container').textContent();
          console.log(`   ❌ Login gagal: ${swalText}`);
          await page.screenshot({ path: `tests/auth/${role}-error.png`, fullPage: true });
          return; // Skip role ini
        }

        // 9. Klik tombol OK di SweetAlert
        await page.click('.swal2-confirm');
        console.log(`   ➤ Klik OK di SweetAlert`);

        // 10. Tunggu redirect ke dashboard (berbeda per role)
        const dashboardUrls = {
          admin: '**/dashboard-admin',
          mahasiswa: '**/dashboard-mahasiswa',
          dosen: '**/dashboard-dosen',
          industri: '**/dashboard-admin' // temporary
        };

        await page.waitForURL(dashboardUrls[role], { timeout: 10000 });
        console.log(`   ➤ Login sukses! Redirect ke dashboard`);

      } catch (e) {
        console.log(`   ⚠️  SweetAlert tidak muncul atau timeout`);
        console.log(`   ⚠️  Error: ${e.message}`);

        // Cek apakah sudah redirect tanpa SweetAlert
        const currentUrl = page.url();
        console.log(`   ➤ Current URL: ${currentUrl}`);
      }

      // 6. Screenshot dashboard
      await page.screenshot({
        path: `tests/auth/${role}-dashboard.png`,
        fullPage: true
      });

      // 7. Simpan storageState
      await context.storageState({ path: cred.storageFile });
      console.log(`   ✅ StorageState disimpan: ${cred.storageFile}\n`);

    } catch (error) {
      console.error(`   ❌ ERROR saat setup ${role}:`, error.message);
      console.error(`   Pastikan kredensial benar dan server berjalan!\n`);
    }

    await context.close();
  }

  await browser.close();
  console.log('🎉 Setup authentication selesai!');
  console.log('📂 File storageState tersimpan di: tests/auth/\n');
  console.log('▶️  Jalankan test dengan: npx playwright test');
}

// ===== JALANKAN =====
setupAuth().catch(console.error);
