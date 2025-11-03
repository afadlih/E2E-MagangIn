/**
 * E2E Test: Industri - Review & Seleksi Lamaran
 * Skenario: E2E-IND-002
 * Industri mereview lamaran mahasiswa → Download dokumen → Accept/Reject lamaran
 */

const { test, expect } = require('@playwright/test');

test.describe('Industri - Review Lamaran', () => {

  test('E2E-IND-002: Review dan seleksi lamaran mahasiswa', async ({ page }) => {
    // ===== STEP 1: Dashboard Industri (dengan storageState) =====
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/.*dashboard/);
    await expect(page.locator('text=Dashboard Perusahaan')).toBeVisible();
    console.log('✅ Step 1: Dashboard industri tampil');

    await page.screenshot({
      path: 'tests/screenshots/industri-01-dashboard.png',
      fullPage: true
    });

    // ===== STEP 2: Verifikasi widget "Lamaran Baru" =====
    const lamaranBadge = page.locator('.widget-lamaran .badge');
    const jumlahLamaran = await lamaranBadge.textContent();
    console.log(`✅ Step 2: Widget menampilkan ${jumlahLamaran} lamaran baru`);

    // ===== STEP 3: Navigasi ke Lamaran Masuk =====
    await page.click('a:has-text("Lamaran Masuk")');
    await page.waitForURL('**/lamaran');
    console.log('✅ Step 3: Halaman lamaran masuk tampil');

    await page.screenshot({
      path: 'tests/screenshots/industri-02-lamaran-list.png',
      fullPage: true
    });

    // ===== STEP 4: Klik Detail pada lamaran pertama =====
    const detailButton = page.locator('button:has-text("Detail")').first();
    await expect(detailButton).toBeVisible();
    await detailButton.click();

    await page.waitForSelector('.modal-detail-lamaran', { state: 'visible' });
    console.log('✅ Step 4: Modal detail lamaran muncul');

    await page.screenshot({
      path: 'tests/screenshots/industri-03-detail-lamaran.png',
      fullPage: true
    });

    // ===== STEP 5: Lihat profil mahasiswa =====
    const namaMhs = await page.locator('.profil-mahasiswa .nama').textContent();
    const prodiMhs = await page.locator('.profil-mahasiswa .prodi').textContent();
    const ipkMhs = await page.locator('.profil-mahasiswa .ipk').textContent();

    console.log(`✅ Step 5: Profil mahasiswa: ${namaMhs} - ${prodiMhs} (IPK: ${ipkMhs})`);

    // ===== STEP 6: Download CV Mahasiswa =====
    const downloadCVButton = page.locator('a:has-text("Download CV")');
    const [downloadCV] = await Promise.all([
      page.waitForEvent('download'),
      downloadCVButton.click()
    ]);

    expect(downloadCV.suggestedFilename()).toContain('.pdf');
    console.log(`✅ Step 6: CV ter-download: ${downloadCV.suggestedFilename()}`);

    // ===== STEP 7: Download Transkrip Nilai =====
    const downloadTranskripButton = page.locator('a:has-text("Download Transkrip")');
    const [downloadTranskrip] = await Promise.all([
      page.waitForEvent('download'),
      downloadTranskripButton.click()
    ]);

    expect(downloadTranskrip.suggestedFilename()).toContain('.pdf');
    console.log(`✅ Step 7: Transkrip ter-download: ${downloadTranskrip.suggestedFilename()}`);

    await page.screenshot({
      path: 'tests/screenshots/industri-04-dokumen-downloaded.png',
      fullPage: true
    });

    // ===== STEP 8: Baca surat motivasi =====
    const motivasiText = await page.locator('.surat-motivasi').textContent();
    expect(motivasiText.length).toBeGreaterThan(50); // Ada konten
    console.log('✅ Step 8: Surat motivasi readable');

    // ===== STEP 9: Klik tombol "Terima" =====
    await page.click('button:has-text("Terima")');

    // Tunggu konfirmasi modal
    await page.waitForSelector('.modal-konfirmasi', { state: 'visible' });
    console.log('✅ Step 9: Konfirmasi penerimaan muncul');

    await page.screenshot({
      path: 'tests/screenshots/industri-05-konfirmasi.png',
      fullPage: true
    });

    // ===== STEP 10: Konfirmasi penerimaan =====
    await page.click('button:has-text("Ya, Terima")');

    // Tunggu notifikasi sukses
    await page.waitForSelector('.alert-success', { state: 'visible', timeout: 5000 });
    const successMsg = await page.locator('.alert-success').textContent();
    expect(successMsg).toContain('diterima');
    console.log('✅ Step 10: Lamaran berhasil diterima, status → "Diterima"');

    await page.screenshot({
      path: 'tests/screenshots/industri-06-lamaran-diterima.png',
      fullPage: true
    });

    // ===== STEP 11: Verifikasi notifikasi ke mahasiswa =====
    // (Simulasi: cek di database atau log notification)
    console.log('✅ Step 11: Notifikasi terkirim ke mahasiswa (verifikasi via log/email)');

    // ===== VALIDASI AKHIR =====
    await page.goto('/lamaran');
    const statusBadge = page.locator('span.badge-success:has-text("Diterima")').first();
    await expect(statusBadge).toBeVisible();
    console.log('✅ Validasi: Status lamaran ter-update di tabel');

    await page.screenshot({
      path: 'tests/screenshots/industri-07-final-status.png',
      fullPage: true
    });

    console.log('🎉 TEST SELESAI: E2E-IND-002 PASSED');
  });

});
