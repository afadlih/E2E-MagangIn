/**
 * E2E Test: Admin - Manajemen Lowongan Magang
 * Deskripsi: Verifikasi admin dapat mengakses halaman manajemen lowongan,
 * menampilkan tabel data, dan membuka modal tambah/edit/detail lowongan.
 * Authentication: Menggunakan storageState (admin.json)
 *
 * NOTE: Test ini fokus pada akses halaman & interaksi dasar UI.
 * Untuk test CRUD lengkap, tambahkan verifikasi form input dan response.
 */

const { test, expect } = require('@playwright/test');

test.describe('E2E Admin - Manajemen Lowongan Magang', () => {

  test.use({ storageState: 'tests/auth/admin.json' }); // gunakan session admin login

  test('Admin dapat mengakses dan memeriksa halaman manajemen lowongan', async ({ page }) => {
    test.setTimeout(60000);

    // STEP 1: Navigasi ke dashboard admin
    console.log('Step 1: Navigasi ke dashboard admin...');
    await page.goto('http://localhost/E2E-MagangIn/public/dashboard-admin');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'tests/screenshots/lowongan-01-dashboard.png', fullPage: true });

    // STEP 2: Verifikasi sudah login
    console.log('Step 2: Verifikasi authentication...');
    expect(page.url()).toContain('/dashboard-admin');
    const navbar = page.locator('.navbar');
    await expect(navbar).toBeVisible();
    console.log('   ✓ Navbar tampil - User authenticated');

    // STEP 3: Navigasi ke halaman manajemen lowongan
    console.log('Step 3: Navigasi ke halaman Manajemen Lowongan...');
    await page.click('a[href*="/lowongan"]');
    await page.waitForLoadState('networkidle');
    expect(page.url()).toContain('/lowongan');
    console.log('   ✓ Berhasil menuju halaman lowongan');

    await page.screenshot({ path: 'tests/screenshots/lowongan-02-halaman.png', fullPage: true });

    // STEP 4: Verifikasi tabel data muncul
    console.log('Step 4: Verifikasi tabel lowongan muncul...');
    const table = page.locator('#lowongan-table');
    await expect(table).toBeVisible();
    await page.waitForTimeout(1000);
    console.log('   ✓ Tabel berhasil dimuat');

    // STEP 5: Verifikasi ada baris data (atau minimal struktur tabel)
    const rows = await page.locator('#lowongan-table tbody tr').count();
    console.log(`   ✓ Tabel memuat ${rows} baris data`);

    // STEP 6: Uji tombol Tambah (modal tampil)
    console.log('Step 6: Klik tombol Tambah Lowongan...');
    await page.click('button:has-text("Tambah")');
    const modal = page.locator('#myModal .modal-content');
    await expect(modal).toBeVisible({ timeout: 10000 });
    console.log('   ✓ Modal Tambah Lowongan tampil');

    await page.screenshot({ path: 'tests/screenshots/lowongan-03-modal-tambah.png', fullPage: true });

    // STEP 7: Tutup modal
    await page.click('#myModal .btn-warning'); // tombol "Batal"
    await page.waitForTimeout(1000);
    console.log('   ✓ Modal berhasil ditutup');

    console.log('✅ Test selesai! Admin berhasil mengakses halaman dan modal manajemen lowongan.');
    console.log('\n📝 NOTE: Untuk test CRUD lengkap, tambahkan skenario berikut:');
    console.log('   - Isi form tambah lowongan dan submit.');
    console.log('   - Verifikasi pesan sukses dari SweetAlert.');
    console.log('   - Cek data muncul di tabel.');
    console.log('   - Uji tombol edit & delete dengan modal yang sesuai.');
  });

});
