/**
 * E2E Test: Mahasiswa - Alur Pengajuan Lowongan
 * Skenario: E2E-MHS-002
 * Mahasiswa login → Browse lowongan → Lamar lowongan → Upload dokumen → Verifikasi status
 */

const { test, expect } = require('@playwright/test');
const path = require('path');

test.describe('Mahasiswa - Pengajuan Lowongan', () => {

  test('E2E-MHS-002: Alur lengkap pengajuan lowongan sukses', async ({ page }) => {
    // ===== MENGGUNAKAN STORAGESTATE: Skip manual login =====
    // Langsung navigasi ke dashboard karena sudah authenticated

    // ===== STEP 1: Dashboard Mahasiswa =====
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/.*dashboard/);
    await expect(page.locator('text=Dashboard Mahasiswa')).toBeVisible();
    console.log('✅ Step 1: Dashboard mahasiswa tampil');

    await page.screenshot({
      path: 'tests/screenshots/mahasiswa-01-dashboard.png',
      fullPage: true
    });

    // ===== STEP 2: Navigasi ke Lowongan Tersedia =====
    await page.click('a:has-text("Lowongan Tersedia")');
    await page.waitForURL('**/lowongan');
    console.log('✅ Step 2: Halaman lowongan tersedia tampil');

    await page.screenshot({
      path: 'tests/screenshots/mahasiswa-02-lowongan-list.png',
      fullPage: true
    });

    // ===== STEP 3: Klik Detail pada lowongan pertama =====
    const detailButton = page.locator('button:has-text("Detail")').first();
    await expect(detailButton).toBeVisible();
    await detailButton.click();

    await page.waitForSelector('.modal-detail-lowongan', { state: 'visible' });
    console.log('✅ Step 3: Modal detail lowongan muncul');

    await page.screenshot({
      path: 'tests/screenshots/mahasiswa-03-detail-lowongan.png',
      fullPage: true
    });

    // ===== STEP 4: Baca persyaratan dan silabus =====
    const persyaratan = await page.locator('.persyaratan-lowongan').textContent();
    expect(persyaratan.length).toBeGreaterThan(20); // Ada konten
    console.log('✅ Step 4: Persyaratan readable');

    // Download silabus (opsional)
    const silabusLink = page.locator('a:has-text("Unduh Silabus")');
    if (await silabusLink.count() > 0) {
      console.log('ℹ️  Silabus tersedia untuk diunduh');
    }

    // ===== STEP 5: Klik tombol Lamar =====
    await page.click('button:has-text("Lamar")');

    await page.waitForSelector('.form-lamaran', { state: 'visible' });
    console.log('✅ Step 5: Form lamaran muncul');

    await page.screenshot({
      path: 'tests/screenshots/mahasiswa-04-form-lamaran.png',
      fullPage: true
    });

    // ===== STEP 6: Isi motivasi =====
    const motivasi = `Saya sangat tertarik dengan posisi ini karena sesuai dengan bidang keahlian saya di ${new Date().toLocaleDateString()}. Saya ingin belajar dan berkontribusi di perusahaan Anda.`;
    await page.fill('textarea[name="motivasi"]', motivasi);
    console.log('✅ Step 6: Motivasi terisi (>100 karakter)');

    // ===== STEP 7: Upload CV (PDF) =====
    const cvPath = path.join(__dirname, '../../fixtures/sample-cv.pdf');
    const cvInput = page.locator('input[type="file"][name="cv"]');
    await cvInput.setInputFiles(cvPath);

    // Tunggu preview atau validasi
    await page.waitForTimeout(1000);
    console.log('✅ Step 7: CV ter-upload');

    await page.screenshot({
      path: 'tests/screenshots/mahasiswa-05-cv-uploaded.png',
      fullPage: true
    });

    // ===== STEP 8: Upload Transkrip Nilai =====
    const transkripPath = path.join(__dirname, '../../fixtures/sample-transkrip.pdf');
    const transkripInput = page.locator('input[type="file"][name="transkrip"]');
    await transkripInput.setInputFiles(transkripPath);

    await page.waitForTimeout(1000);
    console.log('✅ Step 8: Transkrip ter-upload');

    // ===== STEP 9: Centang checkbox persetujuan =====
    await page.check('input[type="checkbox"][name="setuju"]');
    console.log('✅ Step 9: Checkbox persetujuan checked');

    await page.screenshot({
      path: 'tests/screenshots/mahasiswa-06-form-complete.png',
      fullPage: true
    });

    // ===== STEP 10: Kirim Lamaran =====
    await page.click('button:has-text("Kirim Lamaran")');

    // Tunggu notifikasi sukses
    await page.waitForSelector('.alert-success', { state: 'visible', timeout: 5000 });
    const successMsg = await page.locator('.alert-success').textContent();
    expect(successMsg).toContain('berhasil');
    console.log('✅ Step 10: Lamaran terkirim!');

    await page.screenshot({
      path: 'tests/screenshots/mahasiswa-07-lamaran-sent.png',
      fullPage: true
    });

    // ===== STEP 11: Redirect ke "Lamaran Saya" =====
    await page.waitForURL('**/lamaran-saya');
    console.log('✅ Step 11: Redirect ke halaman Lamaran Saya');

    await page.screenshot({
      path: 'tests/screenshots/mahasiswa-08-lamaran-list.png',
      fullPage: true
    });

    // ===== STEP 12: Verifikasi lamaran baru muncul dengan status Pending =====
    const lamaranBaru = page.locator('tr').filter({ hasText: motivasi.substring(0, 30) }).first();
    await expect(lamaranBaru).toBeVisible();

    const statusBadge = lamaranBaru.locator('span.badge:has-text("Pending")');
    await expect(statusBadge).toBeVisible();
    console.log('✅ Step 12: Lamaran baru visible dengan status "Pending"');

    await page.screenshot({
      path: 'tests/screenshots/mahasiswa-09-status-pending.png',
      fullPage: true
    });

    // ===== VALIDASI AKHIR =====
    console.log('🎉 TEST SELESAI: E2E-MHS-002 PASSED');
    console.log('📊 Total screenshots: 9');
    console.log('✅ Validasi: File upload, form submission, status tracking berhasil');
  });

});
