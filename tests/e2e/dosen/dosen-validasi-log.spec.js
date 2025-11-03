/**
 * E2E Test: Dosen - Validasi Log Aktivitas Mahasiswa
 * Skenario: E2E-DSN-002
 * Dosen mereview dan meng-approve log aktivitas mahasiswa yang sudah disetujui industri
 */

const { test, expect } = require('@playwright/test');

test.describe('Dosen - Validasi Log Aktivitas', () => {

  test('E2E-DSN-002: Review dan approval log aktivitas mahasiswa', async ({ page }) => {
    // ===== STEP 1: Navigasi ke Dashboard =====
    // Karena menggunakan storageState, langsung redirect ke dashboard tanpa login manual
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/.*dashboard/);

    // Verifikasi role: pastikan ini dashboard dosen
    await expect(page.locator('text=Dashboard Dosen')).toBeVisible();
    console.log('✅ Step 1: Dashboard dosen tampil');

    // Screenshot dashboard
    await page.screenshot({
      path: 'tests/screenshots/dosen-01-dashboard.png',
      fullPage: true
    });

    // ===== STEP 2: Navigasi ke Log Aktivitas Mahasiswa =====
    await page.click('a:has-text("Log Aktivitas")'); // Sesuaikan selector menu
    await page.waitForURL('**/log-aktivitas');
    console.log('✅ Step 2: Navigasi ke halaman log aktivitas');

    await page.screenshot({
      path: 'tests/screenshots/dosen-02-log-list.png',
      fullPage: true
    });

    // ===== STEP 3: Filter log status "Disetujui Industri" =====
    // Sesuaikan selector dengan HTML Anda
    await page.selectOption('select[name="status"]', 'disetujui_industri');
    await page.click('button:has-text("Filter")');

    await page.waitForTimeout(1000); // Tunggu tabel ter-filter
    console.log('✅ Step 3: Filter status "Disetujui Industri" applied');

    await page.screenshot({
      path: 'tests/screenshots/dosen-03-log-filtered.png',
      fullPage: true
    });

    // ===== STEP 4: Klik Review pada log pertama =====
    const firstReviewButton = page.locator('button:has-text("Review")').first();
    await expect(firstReviewButton).toBeVisible();
    await firstReviewButton.click();

    // Tunggu modal review muncul
    await page.waitForSelector('.modal-review-log', { state: 'visible' });
    console.log('✅ Step 4: Modal review log muncul');

    await page.screenshot({
      path: 'tests/screenshots/dosen-04-modal-review.png',
      fullPage: true
    });

    // ===== STEP 5: Baca detail aktivitas mahasiswa =====
    const aktivitasText = await page.locator('.log-aktivitas-content').textContent();
    expect(aktivitasText.length).toBeGreaterThan(10); // Pastikan ada konten
    console.log('✅ Step 5: Detail aktivitas readable');

    // ===== STEP 6: Lihat foto dokumentasi (jika ada) =====
    const fotoElement = page.locator('img.foto-dokumentasi');
    if (await fotoElement.count() > 0) {
      await expect(fotoElement).toBeVisible();
      console.log('✅ Step 6: Foto dokumentasi visible');
    } else {
      console.log('ℹ️  Step 6: Tidak ada foto dokumentasi');
    }

    // ===== STEP 7: Isi catatan dosen =====
    const catatanDosen = 'Bagus! Aktivitas sangat produktif, lanjutkan konsistensi.';
    await page.fill('textarea[name="catatan_dosen"]', catatanDosen);
    console.log('✅ Step 7: Catatan dosen terisi');

    await page.screenshot({
      path: 'tests/screenshots/dosen-05-catatan-filled.png',
      fullPage: true
    });

    // ===== STEP 8: Pilih rating 4/5 =====
    await page.click('input[name="rating"][value="4"]'); // Atau star rating component
    console.log('✅ Step 8: Rating 4/5 dipilih');

    // ===== STEP 9: Klik Setujui =====
    await page.click('button:has-text("Setujui")');

    // Tunggu konfirmasi modal
    await page.waitForSelector('.modal-konfirmasi', { state: 'visible' });
    console.log('✅ Step 9: Konfirmasi modal muncul');

    await page.screenshot({
      path: 'tests/screenshots/dosen-06-konfirmasi.png',
      fullPage: true
    });

    // ===== STEP 10: Konfirmasi approval =====
    await page.click('button:has-text("Ya, Setujui")');

    // Tunggu notifikasi sukses
    await page.waitForSelector('.alert-success', { state: 'visible', timeout: 5000 });
    const successMessage = await page.locator('.alert-success').textContent();
    expect(successMessage).toContain('berhasil disetujui');
    console.log('✅ Step 10: Log berhasil disetujui, status → "Disetujui Dosen"');

    await page.screenshot({
      path: 'tests/screenshots/dosen-07-approval-success.png',
      fullPage: true
    });

    // ===== VALIDASI AKHIR =====
    // Verifikasi status log ter-update di tabel
    await page.goto('/log-aktivitas');
    const statusBadge = page.locator('span.badge:has-text("Disetujui Dosen")').first();
    await expect(statusBadge).toBeVisible();
    console.log('✅ Validasi: Status log ter-update di tabel');

    await page.screenshot({
      path: 'tests/screenshots/dosen-08-final-status.png',
      fullPage: true
    });

    console.log('🎉 TEST SELESAI: E2E-DSN-002 PASSED');
  });

});
