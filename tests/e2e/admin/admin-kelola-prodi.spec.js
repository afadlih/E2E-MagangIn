/**
 * E2E Test: Admin - Kelola Prodi
 * Deskripsi: Test akses halaman kelola prodi oleh admin
 * Authentication: Menggunakan storageState (admin.json)
 *
 * NOTE: Test ini hanya memverifikasi authentication dan akses halaman.
 * Untuk test CRUD lengkap, sesuaikan selector dengan implementasi actual.
 */

const { test, expect } = require('@playwright/test');

test.describe('E2E Admin - Kelola Prodi', () => {

  test('Admin dapat mengakses halaman kelola prodi', async ({ page }) => {
    test.setTimeout(60000);

    // STEP 1: Navigasi ke dashboard admin
    console.log('Step 1: Navigasi ke dashboard admin...');
    await page.goto('http://localhost/E2E-MagangIn/public/dashboard-admin');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'tests/screenshots/admin-01-dashboard.png', fullPage: true });

    // STEP 2: Verifikasi berhasil login
    console.log('Step 2: Verifikasi sudah authenticated...');
    expect(page.url()).toContain('/dashboard-admin');

    const navbar = page.locator('.navbar');
    await expect(navbar).toBeVisible();
    console.log('   ✓ Navbar visible - User authenticated');

    // STEP 3: Navigasi ke halaman Prodi
    console.log('Step 3: Navigasi ke halaman Prodi...');
    await page.click('a[href*="/prodi"]');
    await page.waitForLoadState('networkidle');
    expect(page.url()).toContain('/prodi');
    console.log('   ✓ Berhasil navigasi ke halaman Prodi');

    // STEP 4: Verifikasi DataTable muncul
    console.log('Step 4: Verifikasi tabel prodi...');
    await page.waitForSelector('table', { timeout: 10000 });
    console.log('   ✓ Tabel prodi berhasil di-load');

    await page.screenshot({ path: 'tests/screenshots/admin-02-halaman-prodi.png', fullPage: true });

    // STEP 5: Verifikasi ada data atau minimal ada struktur tabel
    const tableRows = await page.locator('table tbody tr').count();
    console.log(`   ✓ Tabel memiliki ${tableRows} baris data`);

    console.log('✅ Test selesai! Admin berhasil mengakses halaman kelola prodi.');
    console.log('\n📝 NOTE: Untuk test CRUD lengkap, tambahkan:');
    console.log('   - Klik tombol Tambah');
    console.log('   - Isi form dengan data valid');
    console.log('   - Submit dan verifikasi success');
    console.log('   - Verifikasi data muncul di tabel');
  });

});
