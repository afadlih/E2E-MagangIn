/**
 * E2E Test: Admin - Kelola Data Admin (CRUD)
 * Deskripsi: Tambah, Edit, dan Hapus data admin dengan modal AJAX
 * Catatan:
 *  - Sidebar default dalam keadaan collapse → perlu di-expand dulu
 *  - Gunakan storageState (admin.json) untuk autentikasi otomatis
 */

const { test, expect } = require('@playwright/test');

// Gunakan session login admin agar tidak perlu login ulang
test.use({ storageState: 'tests/auth/admin.json' });

// ─────────────────────────────
// Helper: Expand sidebar bila collapse
// ─────────────────────────────
async function expandSidebar(page) {
  console.log('🧭 Memeriksa kondisi sidebar...');

  const body = page.locator('body');
  const isCollapsed = await body.evaluate(el => el.classList.contains('sidebar-collapse'));

  if (isCollapsed) {
    console.log('   ➤ Sidebar terdeteksi collapse, mencoba expand...');
    const toggleBtn = page.locator('[data-widget="pushmenu"], .nav-toggle');

    if (await toggleBtn.count() > 0) {
      await toggleBtn.first().click({ force: true });
      await page.waitForTimeout(1000);
    } else {
      console.warn('⚠️ Tidak menemukan tombol toggle sidebar!');
    }
  }

  const isNowExpanded = !(await body.evaluate(el => el.classList.contains('sidebar-collapse')));
  console.log(isNowExpanded ? '   ✓ Sidebar terbuka' : '   ⚠️ Sidebar masih collapse!');
}

// ─────────────────────────────
// Helper: Buka halaman Data Admin
// ─────────────────────────────
async function bukaHalamanDataAdmin(page) {
  console.log('➡️ Navigasi ke dashboard admin...');
  await page.goto('http://localhost/E2E-MagangIn/public/dashboard-admin');
  await page.waitForLoadState('networkidle');

  // Pastikan sidebar terbuka
  await expandSidebar(page);

  console.log('➡️ Klik menu "Manajemen Pengguna"...');
  const menuManajemen = page.locator('a:has-text("Manajemen Pengguna")');
  await menuManajemen.waitFor({ state: 'visible', timeout: 5000 });
  await menuManajemen.click();
  await page.waitForTimeout(500);

  console.log('➡️ Klik submenu "Data Admin"...');
  const menuDataAdmin = page.locator('a[href*="/admin"]:has-text("Data Admin")');
  await menuDataAdmin.waitFor({ state: 'visible', timeout: 5000 });
  await menuDataAdmin.click();

  await page.waitForLoadState('networkidle');
  expect(page.url()).toContain('/admin');
  console.log('✅ Halaman Data Admin terbuka');
}

// ─────────────────────────────
// TEST CASES
// ─────────────────────────────
test.describe('Admin - Kelola Data Admin (CRUD)', () => {
  // 1️⃣ Tambah Data Admin
  test('Tambah data admin baru', async ({ page }) => {
    test.setTimeout(90000);
    await bukaHalamanDataAdmin(page);

    console.log('➡️ Klik tombol Tambah Data...');
    const tambahBtn = page.locator('button:has-text("Tambah Data")');
    await tambahBtn.click();
    await page.waitForSelector('#myModal .modal-content form', { timeout: 10000 });
    console.log('✅ Modal tambah admin muncul');

    const random = Math.floor(Math.random() * 1000);
    console.log('➡️ Isi form tambah admin...');
    await page.fill('#username', `admin_test_${random}`);
    await page.fill('#password', 'password123');
    await page.fill('#nama', `Admin Testing ${random}`);
    await page.fill('#email', `admintest${random}@mail.com`);
    await page.fill('#telp', '08123456789');

    console.log('➡️ Submit form...');
    await Promise.all([
      page.click('button[type="submit"]'),
      page.waitForTimeout(1500),
    ]);

    const successPopup = page.locator('.swal2-popup');
    await expect(successPopup).toBeVisible({ timeout: 5000 });
    await expect(successPopup.locator('.swal2-title')).toContainText('Berhasil');
    await successPopup.locator('.swal2-confirm').click();
    console.log('✅ Data admin berhasil ditambahkan');

    await page.screenshot({ path: 'tests/screenshots/admin-tambah.png' });
  });

  // 2️⃣ Edit Data Admin
  test('Edit data admin', async ({ page }) => {
    test.setTimeout(90000);
    await bukaHalamanDataAdmin(page);

    console.log('➡️ Klik tombol Edit pertama...');
    const editBtn = page.locator('table tbody tr button.btn-warning:has(i.fa-edit)').first();
    await editBtn.click();
    await page.waitForSelector('#myModal .modal-content form');
    console.log('✅ Modal edit muncul');

    console.log('➡️ Edit field nama dan telp...');
    await page.fill('#nama', 'Admin Edited');
    await page.fill('#telp', '08999999999');

    await Promise.all([
      page.click('button[type="submit"]'),
      page.waitForTimeout(1500),
    ]);

    const successPopup = page.locator('.swal2-popup');
    await expect(successPopup).toBeVisible();
    await expect(successPopup.locator('.swal2-title')).toContainText('Berhasil');
    await successPopup.locator('.swal2-confirm').click();
    console.log('✅ Data admin berhasil diedit');

    await page.screenshot({ path: 'tests/screenshots/admin-edit.png' });
  });

  // 3️⃣ Hapus Data Admin
  test('Hapus data admin (bukan admin utama)', async ({ page }) => {
    test.setTimeout(90000);
    await bukaHalamanDataAdmin(page);

    console.log('➡️ Cari tombol Hapus selain admin utama...');
    const deleteButtons = page.locator('table tbody tr button.btn-danger:has(i.fa-trash)');
    const count = await deleteButtons.count();

    if (count < 2) {
      console.warn('⚠️ Tidak ada admin lain yang bisa dihapus selain admin utama!');
      return;
    }

    const deleteBtn = deleteButtons.nth(1);
    await deleteBtn.scrollIntoViewIfNeeded();
    await deleteBtn.click();

    console.log('➡️ Konfirmasi hapus...');
    const swalPopup = page.locator('.swal2-popup');
    await expect(swalPopup).toBeVisible();
    await swalPopup.locator('.swal2-confirm').click();

    const successPopup = page.locator('.swal2-popup');
    await expect(successPopup).toBeVisible({ timeout: 5000 });
    await expect(successPopup.locator('.swal2-title')).toContainText('Berhasil');
    await successPopup.locator('.swal2-confirm').click();
    console.log('✅ Data admin berhasil dihapus');

    await page.screenshot({ path: 'tests/screenshots/admin-hapus.png' });
  });
});
