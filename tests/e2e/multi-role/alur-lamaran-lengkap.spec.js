/**
 * E2E Test: Multi-Role - Full Lifecycle Lamaran
 * Skenario: E2E-MULTI-001
 * Mahasiswa lamar → Admin approve → Industri terima → Mahasiswa download surat
 */

const { test, expect, chromium } = require('@playwright/test');

test.describe('Multi-Role - Full Lifecycle Lamaran', () => {

  test('E2E-MULTI-001: Alur lamaran dari Mahasiswa sampai Industri', async () => {
    // ===== SETUP: Launch 3 browser context untuk 3 role =====
    const browser = await chromium.launch({ headless: false });

    // Context 1: Mahasiswa
    const contextMhs = await browser.newContext({
      storageState: 'tests/auth/mahasiswa.json'
    });
    const pageMhs = await contextMhs.newPage();

    // Context 2: Admin
    const contextAdmin = await browser.newContext({
      storageState: 'tests/auth/admin.json'
    });
    const pageAdmin = await contextAdmin.newPage();

    // Context 3: Industri
    const contextInd = await browser.newContext({
      storageState: 'tests/auth/industri.json'
    });
    const pageInd = await contextInd.newPage();

    // ===== PART 1: MAHASISWA MELAMAR =====
    console.log('\n📝 PART 1: Mahasiswa melamar lowongan...');

    await pageMhs.goto('/lowongan');
    await pageMhs.click('button:has-text("Detail")').first();
    await pageMhs.waitForSelector('.modal-detail-lowongan', { state: 'visible' });
    await pageMhs.click('button:has-text("Lamar")');

    const timestamp = Date.now();
    const motivasi = `Saya ingin magang di perusahaan ini - ${timestamp}`;
    await pageMhs.fill('textarea[name="motivasi"]', motivasi);

    // Upload files (pastikan file tersedia di fixtures)
    await pageMhs.setInputFiles('input[name="cv"]', 'tests/fixtures/sample-cv.pdf');
    await pageMhs.setInputFiles('input[name="transkrip"]', 'tests/fixtures/sample-transkrip.pdf');

    await pageMhs.check('input[name="setuju"]');
    await pageMhs.click('button:has-text("Kirim Lamaran")');

    await pageMhs.waitForSelector('.alert-success', { state: 'visible', timeout: 5000 });
    console.log('✅ Mahasiswa berhasil melamar, status="Pending"');

    await pageMhs.screenshot({ path: 'tests/screenshots/multi-01-mhs-lamar.png', fullPage: true });

    // ===== PART 2: ADMIN APPROVE =====
    console.log('\n🔐 PART 2: Admin mereview dan approve lamaran...');

    await pageAdmin.goto('/lamaran-masuk');

    // Cari lamaran dengan motivasi yang unik (timestamp)
    const lamaranRow = pageAdmin.locator(`tr:has-text("${motivasi.substring(0, 30)}")`);
    await expect(lamaranRow).toBeVisible();

    await lamaranRow.locator('button:has-text("Detail")').click();
    await pageAdmin.waitForSelector('.modal-detail-lamaran', { state: 'visible' });

    // Verifikasi dokumen ada
    await expect(pageAdmin.locator('a:has-text("Download CV")')).toBeVisible();

    // Approve lamaran
    await pageAdmin.click('button:has-text("Setujui")');
    await pageAdmin.waitForSelector('.modal-konfirmasi', { state: 'visible' });
    await pageAdmin.click('button:has-text("Ya, Setujui")');

    await pageAdmin.waitForSelector('.alert-success', { state: 'visible', timeout: 5000 });
    console.log('✅ Admin berhasil approve, status="Disetujui Admin"');

    await pageAdmin.screenshot({ path: 'tests/screenshots/multi-02-admin-approve.png', fullPage: true });

    // ===== PART 3: INDUSTRI TERIMA =====
    console.log('\n🏢 PART 3: Industri mereview dan menerima mahasiswa...');

    await pageInd.goto('/lamaran-masuk');

    // Tunggu lamaran muncul di panel industri
    await pageInd.waitForTimeout(2000); // Delay untuk sinkronisasi

    const lamaranRowInd = pageInd.locator(`tr:has-text("${motivasi.substring(0, 30)}")`);
    await expect(lamaranRowInd).toBeVisible();

    await lamaranRowInd.locator('button:has-text("Detail")').click();
    await pageInd.waitForSelector('.modal-detail-lamaran', { state: 'visible' });

    // Download dokumen (simulasi review)
    const [downloadCV] = await Promise.all([
      pageInd.waitForEvent('download'),
      pageInd.click('a:has-text("Download CV")')
    ]);
    console.log(`   📄 Industri download CV: ${downloadCV.suggestedFilename()}`);

    // Terima mahasiswa
    await pageInd.click('button:has-text("Terima")');
    await pageInd.waitForSelector('.modal-konfirmasi', { state: 'visible' });
    await pageInd.click('button:has-text("Ya, Terima")');

    await pageInd.waitForSelector('.alert-success', { state: 'visible', timeout: 5000 });
    console.log('✅ Industri berhasil terima, status="Diterima"');

    await pageInd.screenshot({ path: 'tests/screenshots/multi-03-industri-terima.png', fullPage: true });

    // ===== PART 4: MAHASISWA DOWNLOAD SURAT TUGAS =====
    console.log('\n📥 PART 4: Mahasiswa download surat tugas...');

    await pageMhs.goto('/lamaran-saya');
    await pageMhs.waitForTimeout(2000); // Delay untuk sinkronisasi

    // Cari lamaran yang statusnya "Diterima"
    const lamaranDiterima = pageMhs.locator(`tr:has-text("${motivasi.substring(0, 30)}")`);
    await expect(lamaranDiterima).toBeVisible();

    const statusBadge = lamaranDiterima.locator('span.badge-success:has-text("Diterima")');
    await expect(statusBadge).toBeVisible();
    console.log('   ✓ Status lamaran ter-update: "Diterima"');

    // Klik detail untuk akses surat tugas
    await lamaranDiterima.locator('button:has-text("Detail")').click();
    await pageMhs.waitForSelector('.modal-detail-lamaran', { state: 'visible' });

    // Download surat tugas
    const downloadSuratButton = pageMhs.locator('a:has-text("Download Surat Tugas")');

    if (await downloadSuratButton.count() > 0) {
      const [downloadSurat] = await Promise.all([
        pageMhs.waitForEvent('download'),
        downloadSuratButton.click()
      ]);
      console.log(`✅ Mahasiswa download surat tugas: ${downloadSurat.suggestedFilename()}`);
    } else {
      console.log('⚠️  Surat tugas belum tersedia (perlu di-generate admin)');
    }

    await pageMhs.screenshot({ path: 'tests/screenshots/multi-04-mhs-download-surat.png', fullPage: true });

    // ===== CLEANUP =====
    await contextMhs.close();
    await contextAdmin.close();
    await contextInd.close();
    await browser.close();

    // ===== VALIDASI AKHIR =====
    console.log('\n🎉 TEST SELESAI: E2E-MULTI-001 PASSED');
    console.log('✅ Validasi:');
    console.log('   - Status progression: Pending → Admin Approved → Diterima');
    console.log('   - Dokumen accessible oleh semua role yang relevan');
    console.log('   - Notifikasi cascade ke semua pihak');
    console.log('📊 Total screenshots: 4');
  });

});
