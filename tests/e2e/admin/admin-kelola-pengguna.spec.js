const { test, expect } = require('@playwright/test');

// Gunakan session login admin agar tidak perlu login ulang
test.use({ storageState: 'tests/auth/admin.json' });

/* ─────────────────────────────
   Helper: Expand Sidebar Kaiadmin
───────────────────────────── */
async function expandSidebar(page) {
  console.log('🧭 Memeriksa kondisi sidebar Kaiadmin...');

  const wrapper = page.locator('.wrapper');
  const isCollapsed = await wrapper.evaluate(el =>
    el.classList.contains('sidebar_minimize') ||
    el.classList.contains('sidebar-collapse')
  );

  if (!isCollapsed) {
    console.log('   ✓ Sidebar sudah terbuka');
    return;
  }

  console.log('   ➤ Sidebar terdeteksi collapse, mencoba expand...');

  // Lokasi tombol toggle sidebar sesuai layout Kaiadmin
  const toggleBtn = page.locator('.nav-toggle .toggle-sidebar');

  if (await toggleBtn.count() > 0) {
    try {
      await toggleBtn.first().click({ force: true });
      await page.waitForTimeout(1000);
    } catch (err) {
      console.warn('⚠️ Gagal klik tombol toggle-sidebar:', err.message);
    }
  } else {
    console.warn('⚠️ Tombol .toggle-sidebar tidak ditemukan! Gunakan fallback JS.');
    await page.evaluate(() => {
      const wrapper = document.querySelector('.wrapper');
      wrapper?.classList.remove('sidebar_minimize', 'sidebar-collapse');
      wrapper?.classList.add('sidebar-open');
    });
  }

  const stillCollapsed = await wrapper.evaluate(el =>
    el.classList.contains('sidebar_minimize') ||
    el.classList.contains('sidebar-collapse')
  );

  if (!stillCollapsed) {
    console.log('   ✓ Sidebar berhasil di-expand');
  } else {
    console.warn('⚠️ Sidebar masih collapse setelah percobaan!');
  }
}

/* ─────────────────────────────
   Helper: Buka halaman Data Admin
───────────────────────────── */
async function bukaHalamanDataAdmin(page) {
  console.log('➡️ Navigasi ke dashboard admin...');
  await page.goto('http://localhost/E2E-MagangIn/public/dashboard-admin');
  await page.waitForLoadState('networkidle');

  const navbar = page.locator('.navbar');
  await expect(navbar).toBeVisible();
  console.log('   ✓ Navbar visible - User authenticated');

  // Pastikan sidebar terbuka
  await expandSidebar(page);

  // Klik menu utama
  console.log('➡️ Klik menu "Manajemen Pengguna"...');
  const menuManajemen = page.locator('a:has-text("Manajemen Pengguna")');
  await menuManajemen.waitFor({ state: 'visible', timeout: 5000 });
  await menuManajemen.click();

  // Klik submenu
  console.log('➡️ Klik submenu "Data Admin"...');
  const menuDataAdmin = page.locator('a[href*="/admin"]:has-text("Data Admin")');
  await menuDataAdmin.waitFor({ state: 'visible', timeout: 5000 });
  await menuDataAdmin.click();

  await page.waitForLoadState('networkidle');
  expect(page.url()).toContain('/admin');
  console.log('✅ Halaman Data Admin terbuka');
}

/* ─────────────────────────────
   TEST CASES: CRUD Admin
───────────────────────────── */
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
